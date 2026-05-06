
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

DELIMITER ;


CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    action VARCHAR(50),
    member_id INT,
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DELIMITER //

CREATE TRIGGER AfterMemberDelete
BEFORE DELETE ON members
FOR EACH ROW
BEGIN
    DELETE FROM payments
    WHERE member_id = OLD.member_id;

    INSERT INTO audit_logs (action, member_id) 
    VALUES ('DELETED', OLD.member_id);
END //

DELIMITER ;






DELIMITER //

CREATE PROCEDURE EnrollMemberInClass(IN p_member_id INT, IN p_class_id INT)
BEGIN
    DECLARE v_capacity INT;
    DECLARE v_current_count INT;
    
    START TRANSACTION;
    SELECT capacity INTO v_capacity FROM classes WHERE class_id = p_class_id;
    
    SELECT COUNT(*) INTO v_current_count FROM class_enrollments 
    WHERE class_id = p_class_id AND enroll_status = 'Enrolled';
    
    IF v_current_count < v_capacity THEN
        INSERT INTO class_enrollments (class_id, member_id, enroll_status) VALUES (p_class_id, p_member_id, 'Enrolled');
    ELSE
        INSERT INTO class_enrollments (class_id, member_id, enroll_status) VALUES (p_class_id, p_member_id, 'Waitlisted');
    END IF;
    COMMIT;
END //


CREATE PROCEDURE CancelEnrollment(IN p_enroll_id INT)
BEGIN
    DECLARE v_class_id INT;
    DECLARE v_next_waitlist_id INT;

    START TRANSACTION;
    
    SELECT class_id INTO v_class_id FROM class_enrollments WHERE enroll_id = p_enroll_id;
    
    DELETE FROM class_enrollments WHERE enroll_id = p_enroll_id;
    
    SELECT enroll_id INTO v_next_waitlist_id FROM class_enrollments 
    WHERE class_id = v_class_id AND enroll_status = 'Waitlisted' 
    ORDER BY enrolled_at ASC LIMIT 1;

    IF v_next_waitlist_id IS NOT NULL THEN
        UPDATE class_enrollments SET enroll_status = 'Enrolled' WHERE enroll_id = v_next_waitlist_id;
    END IF;
    COMMIT;
END //

DELIMITER ;
