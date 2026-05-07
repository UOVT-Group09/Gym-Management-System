
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




CREATE TRIGGER after_payment_insert
AFTER INSERT ON payments
FOR EACH ROW
BEGIN

    -- 1. Negative payment
    IF NEW.amount < 0 THEN
        INSERT INTO payment_alerts (member_id, payment_id, alert_type, amount)
        VALUES (NEW.member_id, NEW.payment_id, 'NEGATIVE', NEW.amount);
    END IF;

    -- 2. Unusually high payment (adjust threshold as needed)
    IF NEW.amount > 50000 THEN
        INSERT INTO payment_alerts (member_id, payment_id, alert_type, amount)
        VALUES (NEW.member_id, NEW.payment_id, 'HIGH_VALUE', NEW.amount);
    END IF;

    -- 3. Duplicate payment on the same day for the same member
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

DELIMITER ;


















