
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
    
    INSERT INTO members (full_name, email, phone, gender, type_id) 
    VALUES (p_full_name, p_email, p_phone, p_gender, p_type_id);
    
    
    SET @new_id = LAST_INSERT_ID();
    
    
    INSERT INTO payments (member_id, amount) 
    VALUES (@new_id, p_amount);
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
AFTER DELETE ON members
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (action, member_id) 
    VALUES ('DELETED', OLD.member_id);
END //

DELIMITER ;