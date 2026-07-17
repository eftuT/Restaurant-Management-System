-- Create database
CREATE DATABASE IF NOT EXISTS restaurant;
USE restaurant;

-- Users table (for customer accounts)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin users
CREATE TABLE login (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uname VARCHAR(50) UNIQUE NOT NULL,
    pass VARCHAR(255) NOT NULL
);

-- Tables management
CREATE TABLE alltables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,
    purpose VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'Available',
    cid INT
);

-- Table bookings (admin view)
CREATE TABLE tablebook (
    id INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(10),
    FName VARCHAR(50) NOT NULL,
    LName VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    National VARCHAR(50),
    Country VARCHAR(50),
    Phone VARCHAR(20) NOT NULL,
    Tbltyp VARCHAR(50) NOT NULL,
    Purpose VARCHAR(50),
    Meal VARCHAR(50),
    time TIME NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'NOT CONFIRM'
);

-- Customer reservations
CREATE TABLE reservation (
    reserve_id INT PRIMARY KEY AUTO_INCREMENT,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL,
    guest INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date_res DATE NOT NULL,
    time TIME NOT NULL,
    suggestions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Newsletter subscribers
CREATE TABLE contact (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(100) NOT NULL,
    phoneno VARCHAR(20),
    email VARCHAR(100) NOT NULL,
    approval VARCHAR(20) DEFAULT 'Not Allowed'
);

-- Newsletter logs
CREATE TABLE newsletterlog (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100),
    subject VARCHAR(100),
    news TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Food items
CREATE TABLE food (
    id INT PRIMARY KEY AUTO_INCREMENT,
    food_name VARCHAR(100) NOT NULL,
    food_category VARCHAR(50) NOT NULL,
    food_price DECIMAL(10,2) NOT NULL,
    food_description TEXT,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT 1
);

-- Orders
CREATE TABLE basket (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    address TEXT,
    email VARCHAR(100),
    contact_number VARCHAR(20),
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    items TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items
CREATE TABLE items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    food VARCHAR(100) NOT NULL,
    qty INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES basket(id) ON DELETE CASCADE
);

-- Restaurant info (for admin login)
CREATE TABLE restaurant_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin
INSERT INTO login (uname, pass) VALUES ('admin', 'admin123');
INSERT INTO restaurant_info (username, password) VALUES ('admin', 'admin123');

-- Insert sample tables
INSERT INTO alltables (type, purpose, status) VALUES 
('Table for 2', 'casual', 'Available'),
('Table for 4', 'celebration', 'Available'),
('Table for 6', 'meeting', 'Available');

-- Insert sample food items
INSERT INTO food (food_name, food_category, food_price, food_description, is_available) VALUES
('Fuul', 'breakfast', 100, 'Served with yogurt, tomato, green chili, onion', 1),
('Ertib', 'breakfast', 50, 'Fried onion, potato, rosemary, green chili, spices', 1),
('Fir-fir', 'breakfast', 60, 'Shredded injera stir-fried with berbere', 1),
('Doro Wot', 'lunch', 500, 'Chicken stew with berbere and eggs', 1),
('Kitfo', 'lunch', 300, 'Ground raw beef with butter and mitmita', 1),
('Shekla Tibs', 'lunch', 200, 'Sliced beef pan-fried with onion', 1),
('Tej', 'beverage', 150, 'Honey wine with gesho', 1),
('Tella', 'beverage', 100, 'Traditional beer from teff and gesho', 1);