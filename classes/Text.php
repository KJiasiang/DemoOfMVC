<?php

class Text
{
    public static $source = [];

    public static $flag = true;

    public static function put(&$array, $key)
    {
        $result = self::get($key);

        $array[$key] = $result;
    }

    public static function get($key)
    {
        if (self::$flag) {
            self::$source = simplexml_load_file(dirname(__DIR__).'/lang/'.Cookies::get('locale', 'en').'.xml');
            self::$flag = false;
        }
        if (isset(self::$source->$key)) {
            return (string) self::$source->$key;
        } else {
            return $key;
        }
    }
}
