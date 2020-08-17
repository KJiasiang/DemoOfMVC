<?php

class Controller
{
    private $viewName = 'home';

    public function __construct($pageName)
    {
        $this->viewName = $pageName;
    }

    public function view($pageName, $title, $page)
    {
        $authorization = Authorization::getLevel();
        if ($authorization || $this->viewName == 'login' || strpos($this->viewName, 'overview') !== false) {
            require_once "./views/$this->viewName/$pageName.html";
        } else {
            header('Location: /login');
        }
    }

    public function getJsonEncode(&$array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    public function getJsonDecode($item)
    {
        return json_decode($item);
    }

    public function createSCADAAgnet()
    {
        return new SCADAAgent();
    }
}
