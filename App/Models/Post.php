<?php

namespace Models;

use Model;

class Post extends Model
{
    public static function setup()
    {
        // Manual table creation will be handled in init
    }

    public static function init(?\DB\DB $db)
    {
        $db = $db ?? \DB\DB::$db;
        
        // Create posts table manually with foreign key
        $createTable = "
            CREATE TABLE IF NOT EXISTS `models\\post` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `content` TEXT NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_user_id` (`user_id`),
                CONSTRAINT `fk_post_user_id` FOREIGN KEY (`user_id`) REFERENCES `models\\user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $db->query($createTable);
    }
}
