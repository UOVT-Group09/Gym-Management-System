-- =============================================
-- Tier 3: Database Logic
-- =============================================

CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    action VARCHAR(50),
    member_id INT,
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DELIMITER //

CREATE PROCEDURE RegisterNewMember(
    IN p_full_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(15),
    IN p_gender ENUM('Male', 'Female', 'Other'),
    IN p_type_id INT,
    IN p_amount DECIMAL(10, 2)
)
BEGIN
    DECLARE v_new_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO members (full_name, email, phone, gender, type_id)
    VALUES (p_full_name, p_email, p_phone, p_gender, p_type_id);

    SET v_new_id = LAST_INSERT_ID();

    INSERT INTO payments (member_id, amount)
    VALUES (v_new_id, p_amount);

    COMMIT;
END //

CREATE PROCEDURE ApplyMemberFreeze(IN p_member_id INT, IN p_days INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE members
    SET membership_end = DATE_ADD(membership_end, INTERVAL p_days DAY),
        status = 'Frozen'
    WHERE member_id = p_member_id;

    INSERT INTO member_freezes (member_id, freeze_start, freeze_end)
    VALUES (p_member_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL p_days DAY));

    COMMIT;
END //

CREATE PROCEDURE EnrollMemberInClass(IN p_member_id INT, IN p_class_id INT)
proc: BEGIN
    DECLARE v_capacity INT;
    DECLARE v_enrolled_count INT DEFAULT 0;
    DECLARE v_member_status VARCHAR(20);
    DECLARE v_existing_status VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT capacity INTO v_capacity
    FROM classes
    WHERE class_id = p_class_id
    FOR UPDATE;

    IF v_capacity IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Class not found.';
    END IF;

    SELECT status INTO v_member_status
    FROM members
    WHERE member_id = p_member_id;

    IF v_member_status IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Member not found.';
    END IF;

    IF v_member_status != 'Active' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only active members can enroll.';
    END IF;

    SELECT status INTO v_existing_status
    FROM class_enrollments
    WHERE class_id = p_class_id AND member_id = p_member_id
    LIMIT 1
    FOR UPDATE;

    SELECT COUNT(*) INTO v_enrolled_count
    FROM class_enrollments
    WHERE class_id = p_class_id AND status = 'Enrolled';

    IF v_existing_status = 'Enrolled' THEN
        COMMIT;
        LEAVE proc;
    END IF;

    IF v_existing_status = 'Waitlisted' AND v_enrolled_count >= v_capacity THEN
        COMMIT;
        LEAVE proc;
    END IF;

    IF v_enrolled_count < v_capacity THEN
        INSERT INTO class_enrollments (class_id, member_id, status, enrolled_at, waitlisted_at, cancelled_at)
        VALUES (p_class_id, p_member_id, 'Enrolled', NOW(), NULL, NULL)
        ON DUPLICATE KEY UPDATE
            status = 'Enrolled',
            enrolled_at = NOW(),
            waitlisted_at = NULL,
            cancelled_at = NULL;
    ELSE
        INSERT INTO class_enrollments (class_id, member_id, status, enrolled_at, waitlisted_at, cancelled_at)
        VALUES (p_class_id, p_member_id, 'Waitlisted', NULL, NOW(), NULL)
        ON DUPLICATE KEY UPDATE
            status = 'Waitlisted',
            waitlisted_at = NOW(),
            enrolled_at = NULL,
            cancelled_at = NULL;
    END IF;

    COMMIT;
END //

CREATE PROCEDURE CancelEnrollment(IN p_member_id INT, IN p_class_id INT)
proc: BEGIN
    DECLARE v_current_status VARCHAR(20);
    DECLARE v_wait_member_id INT;
    DECLARE v_class_exists INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT class_id INTO v_class_exists
    FROM classes
    WHERE class_id = p_class_id
    FOR UPDATE;

    IF v_class_exists IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Class not found.';
    END IF;

    SELECT status INTO v_current_status
    FROM class_enrollments
    WHERE class_id = p_class_id AND member_id = p_member_id
    LIMIT 1
    FOR UPDATE;

    IF v_current_status IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Enrollment not found.';
    END IF;

    IF v_current_status = 'Cancelled' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Enrollment already cancelled.';
    END IF;

    UPDATE class_enrollments
    SET status = 'Cancelled',
        cancelled_at = NOW()
    WHERE class_id = p_class_id AND member_id = p_member_id;

    IF v_current_status = 'Enrolled' THEN
        SELECT member_id INTO v_wait_member_id
        FROM class_enrollments
        WHERE class_id = p_class_id AND status = 'Waitlisted'
        ORDER BY waitlisted_at ASC, enrollment_id ASC
        LIMIT 1
        FOR UPDATE;

        IF v_wait_member_id IS NOT NULL THEN
            UPDATE class_enrollments
            SET status = 'Enrolled',
                enrolled_at = NOW(),
                waitlisted_at = NULL,
                cancelled_at = NULL
            WHERE class_id = p_class_id AND member_id = v_wait_member_id;
        END IF;
    END IF;

    COMMIT;
END //

CREATE PROCEDURE ExpireMemberships()
BEGIN
    UPDATE members
    SET status = 'Expired'
    WHERE membership_end < CURDATE() AND status = 'Active';
END //

CREATE TRIGGER AfterMemberDelete
BEFORE DELETE ON members
FOR EACH ROW
BEGIN
    DELETE FROM member_trainer_assignments WHERE member_id = OLD.member_id;
    DELETE FROM payments WHERE member_id = OLD.member_id;

    INSERT INTO audit_logs (action, member_id)
    VALUES ('DELETED', OLD.member_id);
END //

CREATE TRIGGER after_payment_insert
AFTER INSERT ON payments
FOR EACH ROW
BEGIN
    IF NEW.amount < 0 THEN
        INSERT INTO payment_alerts (member_id, payment_id, alert_type, amount)
        VALUES (NEW.member_id, NEW.payment_id, 'NEGATIVE', NEW.amount);
    END IF;

    IF NEW.amount > 50000 THEN
        INSERT INTO payment_alerts (member_id, payment_id, alert_type, amount)
        VALUES (NEW.member_id, NEW.payment_id, 'HIGH_VALUE', NEW.amount);
    END IF;

    IF EXISTS (
        SELECT 1 FROM payments
        WHERE member_id = NEW.member_id
          AND DATE(payment_date) = DATE(NEW.payment_date)
          AND payment_id != NEW.payment_id
    ) THEN
        INSERT INTO payment_alerts (member_id, payment_id, alert_type, amount)
        VALUES (NEW.member_id, NEW.payment_id, 'DUPLICATE', NEW.amount);
    END IF;
END //

CREATE TRIGGER after_assignment_insert
AFTER INSERT ON member_trainer_assignments
FOR EACH ROW
BEGIN
    UPDATE trainers
    SET current_load = current_load + 1
    WHERE trainer_id = NEW.trainer_id;
END //

CREATE TRIGGER after_assignment_delete
AFTER DELETE ON member_trainer_assignments
FOR EACH ROW
BEGIN
    UPDATE trainers
    SET current_load = current_load - 1
    WHERE trainer_id = OLD.trainer_id;
END //

DELIMITER ;
