-- CabConnect Database Schema
-- Run this SQL script to create the database and tables

CREATE DATABASE IF NOT EXISTS cabconnect;
USE cabconnect;

-- Admin Table
CREATE TABLE admin (
    admin_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customer Table
CREATE TABLE customer (
    customer_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Driver Table
CREATE TABLE driver (
    driver_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    vehicle_name VARCHAR(100) NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    license_no VARCHAR(50),
    vehicle_photo VARCHAR(255),
    photo VARCHAR(255),
    license_file VARCHAR(255),
    id_proof VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    approval_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    availability_status ENUM('Available', 'Not Available') DEFAULT 'Not Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Booking Table
CREATE TABLE booking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id VARCHAR(10),
    driver_id VARCHAR(10),
    pickup_location VARCHAR(255) NOT NULL,
    drop_location VARCHAR(255) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    booking_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    trip_status ENUM('Pending', 'Accepted', 'Completed', 'Cancelled') DEFAULT 'Pending',
    driver_eta VARCHAR(50),
    fare DECIMAL(10,2),
    FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
    FOREIGN KEY (driver_id) REFERENCES driver(driver_id)
);

-- Insert default admin
INSERT INTO admin (admin_id, name, email, password)
VALUES ('A001', 'Admin', 'admin@cabconnect.com', '$2y$10$aQ8qK/3UnNx2gNhy4vd77.4miDY0b.VldKZw.gF1yK1/rhEXpAk2S')
ON DUPLICATE KEY UPDATE password=VALUES(password);

ALTER TABLE driver
ADD COLUMN license_image VARCHAR(255),
ADD COLUMN passportsize_photo VARCHAR(255),
ADD COLUMN vehicle_image VARCHAR(255),
ADD COLUMN id_proof VARCHAR(255),
ADD COLUMN aadhar_pdf VARCHAR(255);



?>


