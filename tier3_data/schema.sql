-- Database එක නිර්මාණය කිරීම
CREATE DATABASE gym_management;
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
    phone VARCHAR(15)
);

-- 4. Payments Table
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT,
    amount DECIMAL(10, 2),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id)
);

-- 5. Users Table (Login සඳහා - Admin/User roles)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') DEFAULT 'User'
);