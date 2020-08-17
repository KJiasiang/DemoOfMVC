<?php

class Cookies
{
    private static function check($name, $defaut)
    {
        if (!isset($_COOKIE[$name])) {
            self::set($name, $defaut);

            return false;
        }

        return true;
    }

    public static function get($name, $default)
    {
        if (self::check($name, $default)) {
            return $_COOKIE[$name];
        }

        return $default;
    }

    public static function set($name, $value)
    {
        setcookie($name, $value, 2147483647);
    }
}
