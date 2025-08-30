<?php

class Session
{
    public static function start(): void
    {
        session_start();
    }

    public static function reset(): void
    {
        session_unset();
        session_destroy();
        session_start();
        static::regenerateId();
    }

    public static function regenerateId()
    {
        session_regenerate_id(true);
    }

    public static function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public static function get($name, $defaultValue = null)
    {
        return $_SESSION[$name] ?? $defaultValue;
    }
}
