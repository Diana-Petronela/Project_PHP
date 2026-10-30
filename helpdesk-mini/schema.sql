-- HelpDesk Mini - database schema
-- Run this first, then seed.sql (optional demo data).

CREATE DATABASE IF NOT EXISTS helpdesk_mini
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE helpdesk_mini;

CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE tickets (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(150) NOT NULL,
    description TEXT         NOT NULL,
    status      ENUM('Open', 'In Progress', 'On Hold', 'Closed') NOT NULL DEFAULT 'Open',
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_tickets_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_tickets_user (user_id),
    INDEX idx_tickets_status (status)
) ENGINE=InnoDB;

CREATE TABLE ticket_notes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id  INT UNSIGNED NOT NULL,
    note       TEXT         NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notes_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        ON DELETE CASCADE,

    INDEX idx_notes_ticket (ticket_id)
) ENGINE=InnoDB;
