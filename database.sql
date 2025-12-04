CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('unverified', 'active', 'blocked') NOT NULL DEFAULT 'unverified',
    confirmation_token VARCHAR(64) DEFAULT NULL,
    registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME DEFAULT NULL,
    last_activity_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL
);

-- Unique index requirement: each e-mail is unique
CREATE UNIQUE INDEX idx_users_email_unique ON users(email);

-- Extra indexes that may help queries
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_last_login ON users(last_login_at);


