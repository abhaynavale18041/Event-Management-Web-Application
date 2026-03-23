-- Event Management System Database
-- Database: event_manager
-- Run this SQL file to create the database and tables

CREATE DATABASE IF NOT EXISTS event_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE event_manager;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    max_capacity INT DEFAULT 0,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_event_date (event_date),
    FULLTEXT idx_search (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event registrations table
CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table for CSRF tokens (optional but useful)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    data TEXT,
    timestamp INT NOT NULL,
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password, is_admin) VALUES 
('admin', 'admin@eventmanager.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert sample events
INSERT INTO events (title, description, event_date, event_time, venue, image, max_capacity, created_by) VALUES
('Tech Summit 2024', 'Join us for the biggest technology summit of the year. Featuring keynote speakers, workshops, and networking opportunities for tech enthusiasts.', '2024-03-15', '09:00:00', 'Convention Center, Downtown', 'event1.jpg', 500, 1),
('Music Festival', 'A day filled with live performances from top artists across multiple genres. Food vendors and activities for all ages.', '2024-03-20', '14:00:00', 'City Park Amphitheater', 'event2.jpg', 1000, 1),
('Business Networking Meetup', 'Connect with local business leaders, entrepreneurs, and professionals. Build valuable business relationships.', '2024-03-10', '18:00:00', 'Grand Hotel, Conference Room A', 'event3.jpg', 100, 1),
('Photography Workshop', 'Learn professional photography techniques from award-winning photographers. Bring your camera!', '2024-03-22', '10:00:00', 'Arts & Media Center', 'event4.jpg', 50, 1),
('Charity Marathon', 'Run for a cause! Join our annual charity marathon supporting local communities. 5K, 10K, and half-marathon options available.', '2024-03-25', '07:00:00', 'Riverside Park', 'event5.jpg', 300, 1);

-- Insert sample registrations
INSERT INTO event_registrations (event_id, name, email, phone) VALUES
(1, 'John Doe', 'john.doe@email.com', '555-0101'),
(1, 'Jane Smith', 'jane.smith@email.com', '555-0102'),
(1, 'Bob Johnson', 'bob.johnson@email.com', '555-0103'),
(2, 'Alice Brown', 'alice.brown@email.com', '555-0104'),
(2, 'Charlie Wilson', 'charlie.wilson@email.com', '555-0105'),
(3, 'Diana Lee', 'diana.lee@email.com', '555-0106');

