<?php

namespace Models;

use Model;
use DB\Column;

class Post extends Model
{
    public static Column $id;
    public static Column $title;
    public static Column $content;
    public static Column $user_id;
    public static Column $created_at;
    public static Column $updated_at;

    public static function setup()
    {
        self::$id = new Column('INT', 'UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        self::$title = new Column('VARCHAR(255)', 'NOT NULL');
        self::$content = new Column('TEXT', 'NOT NULL');
        self::$user_id = new Column('INT', 'UNSIGNED NOT NULL');
        self::$created_at = new Column('TIMESTAMP', 'DEFAULT CURRENT_TIMESTAMP');
        self::$updated_at = new Column('TIMESTAMP', 'DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

        self::addForeignKey('user_id', 'Models\\User', 'id');
    }
}
