<?php
/* 必须登录才能访问 */
namespace controllers;

class BaseController
{
    public function __construct()
    {
        if(!isset($_SESSION['id']))
        {
            redirect('/login/login');
        }
    }
}