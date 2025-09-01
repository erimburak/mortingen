<?php

namespace Models;

use Model;

class User extends Model
{
    public static function setup()
    {
        // Manual table creation will be handled in init
    }

    public static function init(?\DB\DB $db)
    {
        $db = $db ?? \DB\DB::$db;
        
        // Create users table manually
        $createTable = "
            CREATE TABLE IF NOT EXISTS `models\\user` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $db->query($createTable);
    }
}
