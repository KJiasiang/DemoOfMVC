<?php
session_start();
class Authorization
{
    public static function verify($password)
    {
        return true;
    }

    public static function dispose()
    {
    }

    public static function setLevel($v)
    {
        $_SESSION['login'] = $v;
    }

    public static function clearLevel()
    {
        unset($_SESSION['login']);
    }

    public static function getLevel()
    {
        if (isset($_SESSION['login'])) {
            if ($_SESSION['login'] == 2)
                return 2;
            return 1;
        }
        return 0;
    }
}
