-- -----------------------------------------------------
-- DATABASE: blog_app
-- -----------------------------------------------------
-- CREATE DATABASE IF NOT EXISTS blog_app
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;

-- USE blog_app;

-- -----------------------------------------------------
-- USERS TABLE
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default.png',
    is_admin TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);

-- -----------------------------------------------------
-- CATEGORIES TABLE
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_categories_title ON categories(title);

-- -----------------------------------------------------
-- POSTS TABLE
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    thumbnail VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    author_id INT NOT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_posts_category
      FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_posts_author
      FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_posts_category_id ON posts(category_id);
CREATE INDEX idx_posts_author_id ON posts(author_id);
CREATE INDEX idx_posts_is_featured ON posts(is_featured);
CREATE INDEX idx_posts_date_time ON posts(date_time);

-- -----------------------------------------------------
-- DEFAULT CATEGORIES
-- -----------------------------------------------------
INSERT INTO categories (title, description)
VALUES
 ('Uncategorized', 'Default category'),
 ('Tech', 'Technology and programming'),
 ('Lifestyle', 'Lifestyle and wellbeing'),
 ('Travel', 'Travel experiences'),
 ('Food', 'Food and cooking'),
 ('Nature', 'Nature & environment'),
 ('Romance', 'Love and relationships')
ON DUPLICATE KEY UPDATE title=title;

-- -----------------------------------------------------
-- DEFAULT ADMIN USER
-- -----------------------------------------------------
INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin)
VALUES (
    'Admin',
    'User',
    'admin',
    'admin@example.com',
    -- password_hash('Admin123!', PASSWORD_DEFAULT)
    '$2y$10$6ZanhkJhpYdG/9hIsVDquObGtJPHtDu7WxYqgYGnbRx9JsQapqXGi',
    'default.png',
    1
)
ON DUPLICATE KEY UPDATE username=username;
