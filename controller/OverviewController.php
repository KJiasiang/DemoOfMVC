<?php

class OverviewController extends Controller
{
    private $workType = 0;
    private $workIndex = 0;

    public function __construct()
    {
        parent::__construct('overview');
    }

    public function index()
    {
        $this->view('index', Text::get('overview'), 1);
    }
}
