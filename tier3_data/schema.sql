CREATE DATABASE IF NOT EXISTS gym_management;
USE gym_management;

-- 1. Membership Types Table
CREATE TABLE membership_types (
    type_id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    duration_months INT NOT NULL
);

-- 2. Members Table
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

-- 3. Trainers Table
CREATE TABLE trainers (
    trainer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    phone VARCHAR(15),
    current_load INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS member_trainer_assignments (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    trainer_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE,
    UNIQUE KEY (member_id),
    INDEX (trainer_id)
);

-- 4. Payments Table
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    amount DECIMAL(10, 2),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id)
);

-- 5. Users Table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') DEFAULT 'User'
);

CREATE TABLE IF NOT EXISTS payment_alerts (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    payment_id INT NOT NULL,
    alert_type VARCHAR(20) NOT NULL,
    amount DECIMAL(10, 2),
    detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

CREATE INDEX idx_payment_member_date ON payments(member_id, payment_date);

-- Initial Data for Membership Types
INSERT INTO membership_types (type_name, amount, duration_months)
VALUES
    ('Regular', 5000.00, 1),
    ('Premium', 9000.00, 3)
ON DUPLICATE KEY UPDATE amount = VALUES(amount), duration_months = VALUES(duration_months);

-- Membership lifecycle fields
ALTER TABLE members ADD COLUMN membership_start DATE DEFAULT (CURRENT_DATE);
ALTER TABLE members ADD COLUMN membership_end DATE;
ALTER TABLE members ADD COLUMN status ENUM('Active', 'Expired', 'Frozen') DEFAULT 'Active';

-- Freeze history
CREATE TABLE IF NOT EXISTS member_freezes (
    freeze_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    freeze_start DATE,
    freeze_end DATE,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE
);

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

    SELECT amount INTO v_amount FROM membership_types WHERE type_id = p_type_id;

    IF v_amount IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid membership type.';
    END IF;

    INSERT INTO members (full_name, email, phone, gender, type_id)
    VALUES (p_full_name, p_email, p_phone, p_gender, p_type_id);

    SET @new_member_id = LAST_INSERT_ID();

    INSERT INTO payments (member_id, amount)
    VALUES (@new_member_id, v_amount);
END$$

CREATE PROCEDURE GetAllMembers()
BEGIN
    SELECT m.member_id, m.full_name, m.email, m.phone, m.gender, m.join_date, mt.type_name, m.status, m.membership_end
    FROM members m
    LEFT JOIN membership_types mt ON m.type_id = mt.type_id;
END$$

CREATE PROCEDURE GetTotalRevenue()
BEGIN
    SELECT COALESCE(SUM(amount), 0) AS total_revenue FROM payments;
END$$

CREATE TRIGGER after_assignment_insert
AFTER INSERT ON member_trainer_assignments
FOR EACH ROW
BEGIN
    UPDATE trainers
    SET current_load = current_load + 1
    WHERE trainer_id = NEW.trainer_id;
END$$

CREATE TRIGGER after_assignment_delete
AFTER DELETE ON member_trainer_assignments
FOR EACH ROW
BEGIN
    UPDATE trainers
    SET current_load = current_load - 1
    WHERE trainer_id = OLD.trainer_id;
END$$

DELIMITER ;


-- ==========================================================
-- 2) Monthly Revenue Snapshot View & Index
-- ==========================================================

-- Index for faster grouping by date
CREATE INDEX idx_payment_date ON payments(payment_date);

-- View for Monthly Revenue Snapshot
CREATE OR REPLACE VIEW vw_monthly_revenue AS
SELECT 
    YEAR(payment_date) AS rev_year,
    MONTH(payment_date) AS rev_month,
    COUNT(payment_id) AS payment_count,
    COALESCE(SUM(amount), 0) AS total_revenue,
    COALESCE(AVG(amount), 0) AS average_payment
FROM payments
GROUP BY YEAR(payment_date), MONTH(payment_date);
