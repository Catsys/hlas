--- mysql
CREATE TABLE `users` (
                         `id` CHAR(36) NOT NULL,
                         `pass_hash` VARCHAR(60) NOT NULL,
                         `first_name` TEXT,
                         `last_name` TEXT,
                         `birth_date` DATE,
                         `gender` VARCHAR(10),
                         `interests` TEXT,
                         `city` VARCHAR(100),
                         `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         `biography` TEXT,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX `users_first_name_last_name_idx` ON `users` (`first_name`(255), `last_name`(255));

CREATE USER 'dbuser'@'%' IDENTIFIED BY 'dbpass';
GRANT ALL PRIVILEGES ON study_db.* TO 'dbuser'@'%';
FLUSH PRIVILEGES;