<?php

class ApiGetController extends Controller
{
    public function __construct()
    {
        parent::__construct('');
    }

    public function test()
    {
        phpinfo();
    }

    public function css()
    {
        header('Content-type: text/css');
        require 'css/w3.css';
    }

    public function js()
    {
        header('Content-type: text/script');
        require 'script/Chart.min.js';
        echo "\n";
        require 'script/vue-charts.js';
        echo "\n";
        // require 'script/vue.min.js';
        require 'script/vue.js';
        echo "\n";
        require 'script/es6-promise.min.js';
        echo "\n";
        require 'script/es6-promise.auto.min.js';
        echo "\n";
        require 'script/axios.min.js';
        echo "\r\n";
        require 'script/httpVueLoader.js';
        echo "\r\n";
        echo "Vue.use(VueCharts);\n";
    }

    public function getLogo()
    {
        require 'img/logo.png';
    }

    public function getMyTime()
    {
        echo date('Y-m-d H:i:s');
    }

    public function loadingPic()
    {
        require 'img/loading.gif';
    }

    public function getImage($name)
    {
        require "img/$name";
    }

    public function getCmponent($name)
    {
        require_once './component/' . $name . '.vue';
    }

    public function getScript($class, $name)
    {
        header('Content-type: text/script');
        require_once "./script/$class/$name.js";
    }

    public function getHeaderData()
    {
        $result = array();
        $result['color'] = Color::getAll();
        $keys = array(
            'close',
            'overview',
            'history',
            'setting',
            'connection',
            'group',
            'datastore',
            'layout',
            'system',
            'color',
            'help',
            'monitor',
            'mqtt',
            'hsms',
            'about',
            'autowarehouse',
            'reference',
            'login',
            'logout',
            'record'
        );
        $text = array();
        foreach ($keys as $key) {
            Text::put($text, $key);
        }
        $result['text'] = $text;
        $result['auth'] = Authorization::getLevel();

        if (file_exists('/home/moxa/alias')) {
            $f = file("/home/moxa/alias");
            if (count($f) > 0 && strlen($f[0]) > 0) {
                $result['alias'] = $f[0];
            } else
                $result['alias'] = "New Device";
        } else
            $result['alias'] = "New Device";

        return json_encode($result);
    }

    public function getFooterData()
    {
        $result = array();
        $result['color'] = Color::getAll();
        $keys = array(
            'language',
        );
        $text = array();
        foreach ($keys as $key) {
            Text::put($text, $key);
        }
        $result['text'] = $text;
        $result['locale'] = Cookies::get('locale', 'en');

        return json_encode($result);
    }

    public function downloadFile($name)
    {
        $file_name = $name;
        $file_path = "/home/moxa/web/files/" . $name;
        $file_size = filesize($file_path);
        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $file_size);
        header('Content-Disposition: attachment; filename="' . $file_name . '";');
        header('Content-Transfer-Encoding: binary');
        readfile($file_path);
    }

    public function getFileList()
    {
        $files = scandir("/home/moxa/web/files");
        $result = array();
        foreach ($files as $file) {
            if (strcmp($file, ".") == 0 || strcmp($file, "..") == 0)
                continue;
            array_push($result, $file);
        }

        return json_encode($result);
    }
}
