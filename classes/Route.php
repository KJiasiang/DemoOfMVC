<?php

// header('Content-Type: application/json; charset=utf-8');
class Route
{
    public static $routes_get = array();

    public static $routes_post = array();

    public static $fun_Controller = array();

    public static $fun_Name = array();

    public static function get($uri, $controller, $function = 'index')
    {
        $uriStr = '';
        if ('' == $uri) {
            $uriStr = 'index.php';
        } else {
            $uriStr = $uri;
        }
        self::$routes_get[] = $uriStr;
        self::$fun_Controller[$uriStr] = $controller;
        self::$fun_Name[$uriStr] = $function;
    }

    public static function post($uri, $controller, $function = 'index')
    {
        self::$routes_post[] = $uri;
        self::$fun_Controller[$uri] = $controller;
        self::$fun_Name[$uri] = $function;
    }

    private static function checkUri($target, $param = array())
    {
        $getParam = $_GET['uri'];
        // ChromePhp::log($getParam);
        $match = false;
        foreach ($target as $key => $value) {
            if (preg_match("#^$value$#", $getParam)) {
                $controller = self::$fun_Controller[$getParam].'Controller';
                $fun = self::$fun_Name[$getParam];
                $c = new $controller();
                switch (count($param)) {
                    case 1:
                        echo $c->$fun($param[0]);
                        break;
                    case 2:
                        echo $c->$fun($param[0], $param[1]);
                        break;
                    case 3:
                        echo $c->$fun($param[0], $param[1], $param[2]);
                        break;
                    case 4:
                        echo $c->$fun($param[0], $param[1], $param[2], $param[3]);
                        break;
                    case 5:
                        echo $c->$fun($param[0], $param[1], $param[2], $param[3], $param[4]);
                        break;
                    case 6:
                        echo $c->$fun($param[0], $param[1], $param[2], $param[3], $param[4], $param[5]);
                        break;
                    case 7:
                        echo $c->$fun($param[0], $param[1], $param[2], $param[3], $param[4], $param[5], $param[6]);
                        break;
                    default:
                        echo $c->$fun();
                        break;
                }
                $match = true;
                break;
            }
        }
        if (!$match) {
            header('Location: /.');
        }
    }

    public static function run()
    {
        $target = array();
        $param = array();
        $body = '[]';
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            if (count($_GET) > 1) {
                $body = json_encode($_GET);
            }
            $target = self::$routes_get;
        } elseif ('POST' == $_SERVER['REQUEST_METHOD']) {
            if (count($_POST) > 0) {
                $body = json_encode($_POST);
            } else {
                $body = file_get_contents('php://input');
            }
            $target = self::$routes_post;
        }
        $param = self::getParam(json_decode($body));

        self::checkUri($target, $param);
    }

    private static function getParam($json)
    {
        $result = array();
        if (count($json)) {
            foreach ($json as $key => $value) {
                if (0 == strcmp($key, 'uri')) {
                    continue;
                }
                $result[] = $value;
            }
        }

        return $result;
    }
}
