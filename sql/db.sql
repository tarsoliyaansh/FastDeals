-- database.sql
CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;
show databases;

-- Users
CREATE TABLE IF NOT EXISTS Users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  address VARCHAR(255),
  role ENUM('customer','staff','admin') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE IF NOT EXISTS Categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT
);

-- Vendors
CREATE TABLE IF NOT EXISTS Vendors (
  vendor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  contact_info VARCHAR(255)
);

-- Products
CREATE TABLE IF NOT EXISTS Products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  vendor_id INT NULL,
  category_id INT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) DEFAULT 0.00,
  stock_quantity INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Orders
CREATE TABLE IF NOT EXISTS Orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  total_amount DECIMAL(10,2) DEFAULT 0.00,
  FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE SET NULL
);

-- Order_Items (weak entity)
CREATE TABLE IF NOT EXISTS Order_Items (
  order_item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE RESTRICT
);

-- Payments
CREATE TABLE IF NOT EXISTS Payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  payment_status ENUM('pending','completed','failed') DEFAULT 'pending',
  payment_mode ENUM('cash','card','upi','other') DEFAULT 'cash',
  amount DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE
);

-- Refunds (optional)
CREATE TABLE IF NOT EXISTS Refunds (
  refund_id INT AUTO_INCREMENT PRIMARY KEY,
  payment_id INT NOT NULL,
  refund_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  amount DECIMAL(10,2) NOT NULL,
  reason TEXT,
  FOREIGN KEY (payment_id) REFERENCES Payments(payment_id) ON DELETE CASCADE
);

-- Inventory Logs
CREATE TABLE IF NOT EXISTS Inventory_Log (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT NULL,
  change_type ENUM('added','sold','restocked') NOT NULL,
  quantity_changed INT NOT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE SET NULL
);
