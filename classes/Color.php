<?php

class Color
{
    public static $colors = array();

    public static function initialize()
    {
        self::add('background1', 'w3black');
        self::add('background2', 'w3white');
        self::add('unselect', 'w3black');
        self::add('selected', 'w3green');
        self::add('button', 'w3black');
        self::add('stategreen', 'w3green');
        self::add('stateyellow', 'w3yellow');
        self::add('statered', 'w3red');
        self::add('stateunknown', 'w3gray');
    }

    private static function add($name, $default)
    {
        self::$colors[$name] = Cookies::get($name, $default);
    }

    public static function getAll()
    {
        return self::$colors;
    }

    public static function set($colors)
    {
        foreach ($colors as $color) {
            Cookies::set($color->key, $color->color);
        }
    }
}
