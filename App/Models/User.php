<?php

namespace Models;

use Model;
use DB\Column;

class User extends Model
{
    public static Column $id;
    public static Column $name;
    public static Column $email;
    public static Column $created_at;
    public static Column $updated_at;

    public static function setup()
    {
        self::$id = new Column('INT', 'UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        self::$name = new Column('VARCHAR(255)', 'NOT NULL');
        self::$email = new Column('VARCHAR(255)', 'NOT NULL UNIQUE');
        self::$created_at = new Column('TIMESTAMP', 'DEFAULT CURRENT_TIMESTAMP');
        self::$updated_at = new Column('TIMESTAMP', 'DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }
}
