CREATE DATABASE antisocial_network;

CREATE TABLE `user` (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(150) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash LONGTEXT NOT NULL,
    activation_token_hash VARCHAR(64) UNIQUE,
    is_active BOOLEAN DEFAULT 0,
    is_superuser BOOLEAN DEFAULT 0,
    is_staff BOOLEAN DEFAULT 0
);

CREATE TABLE `profile` (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    avatar VARCHAR(260),
    first_name VARCHAR(150),
    last_name VARCHAR(150),
    bio LONGTEXT,
    date_joined DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)
        REFERENCES user(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE `post` (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    file VARCHAR(260) NOT NULL,
    title VARCHAR(250) NOT NULL,
    text LONGTEXT NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated DATETIME,
    FOREIGN KEY (user_id)
        REFERENCES user(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE `remembered_login` (
    token_hash VARCHAR(64) PRIMARY KEY,
    user_id INT NOT NULL,
    expiration_date DATETIME NOT NULL,
    FOREIGN KEY (user_id)
        REFERENCES user(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);
