<?php
namespace controllers;

class IndexController
{
    public function index()
    {
        // views/index/index.html
        view('index/index', [
            'name' => 'tom',
            'age' => 10,
        ]);
    }
}