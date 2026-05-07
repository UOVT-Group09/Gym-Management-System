
CREATE DATABASE gym_management;
USE gym_management;


CREATE TABLE membership_types (
    type_id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    duration_months INT NOT NULL
);


CREATE TABLE members (
    member_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    gender ENUM('Male', 'Female', 'Other'),
    type_id INT,
    join_date DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (type_id) REFERENCES membership_types(type_id)
);


CREATE TABLE trainers (
    trainer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    phone VARCHAR(15)
);


CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    amount DECIMAL(10, 2),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id)
);

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') DEFAULT 'User'
);


CREATE TABLE IF NOT EXISTS payment_alerts (
    alert_id    INT PRIMARY KEY AUTO_INCREMENT,
    member_id   INT NOT NULL,
    payment_id  INT NOT NULL,
    alert_type  VARCHAR(20) NOT NULL,   -- 'NEGATIVE', 'HIGH_VALUE', 'DUPLICATE'
    amount      DECIMAL(10, 2),
    detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id)  REFERENCES members(member_id),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);


CREATE INDEX idx_payment_member_date ON payments(member_id, payment_date);



INSERT INTO membership_types (type_name, amount, duration_months) 
VALUES 
    ('Regular', 5000.00, 1), 
    ('Premium', 9000.00, 3)
ON DUPLICATE KEY UPDATE amount = VALUES(amount), duration_months = VALUES(duration_months);


DELIMITER $$

CREATE PROCEDURE RegisterNewMember(
    IN p_full_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(15),
    IN p_gender ENUM('Male', 'Female', 'Other'),
    IN p_type_id INT
)
BEGIN
    DECLARE v_amount DECIMAL(10,2);

    SELECT amount
    INTO v_amount
    FROM membership_types
    WHERE type_id = p_type_id;

    IF v_amount IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid membership type.';
    END IF;

    INSERT INTO members (full_name, email, phone, gender, type_id)
    VALUES (p_full_name, p_email, p_phone, p_gender, p_type_id);

    SET @new_member_id = LAST_INSERT_ID();

    INSERT INTO payments (member_id, amount)
    VALUES (@new_member_id, v_amount);
END$$


CREATE PROCEDURE GetAllMembers()
BEGIN
    SELECT m.member_id, m.full_name, m.email, m.phone, m.gender, m.join_date, mt.type_name
    FROM members m
    LEFT JOIN membership_types mt ON m.type_id = mt.type_id;
END$$



CREATE PROCEDURE GetTotalRevenue()
BEGIN
    SELECT COALESCE(SUM(amount), 0) AS total_revenue FROM payments;
END$$

DELIMITER ;










