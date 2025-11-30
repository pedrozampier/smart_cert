SET foreign_key_checks = 0;

DROP TABLE IF EXISTS event_participant;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS logos;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    encrypted_password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    cpf VARCHAR(14),
    created_at DATETIME,
    updated_at DATETIME,
    is_active BOOLEAN DEFAULT 1,
    is_admin BOOLEAN DEFAULT 0
);

CREATE TABLE logos (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER,
    file_extension VARCHAR(10),
    width INTEGER,
    height INTEGER,
    uploaded_at DATETIME,
    user_id INTEGER,
    is_active BOOLEAN DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_is_active (is_active)
);

CREATE TABLE events (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE,
    event_location VARCHAR(255),
    workload_hours INTEGER,
    event_type VARCHAR(100),
    logo_id INTEGER,
    creator_id INTEGER NOT NULL,
    owner_id INTEGER,
    created_at DATETIME,
    updated_at DATETIME,
    is_active BOOLEAN DEFAULT 1,
    FOREIGN KEY (logo_id) REFERENCES logos(id) ON DELETE SET NULL,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_logo (logo_id),
    INDEX idx_creator (creator_id),
    INDEX idx_owner (owner_id),
    INDEX idx_start_date (start_date),
    INDEX idx_is_active (is_active)
);

CREATE TABLE event_participant (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    event_id INTEGER NOT NULL,
    participant_id INTEGER NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    participation_status ENUM('registered', 'confirmed', 'attended', 'absent', 'cancelled') DEFAULT 'registered',
    certificate_description TEXT,
    certificate_generated BOOLEAN DEFAULT 0,
    certificate_generation_date DATETIME,
    certificate_code VARCHAR(100),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_participant (event_id, participant_id),
    INDEX idx_event (event_id),
    INDEX idx_participant (participant_id),
    INDEX idx_status (participation_status)
);

SET foreign_key_checks = 1;