<?php

define('ROOT', __DIR__ . '/../');

// 引入函数文件
require(ROOT.'libs/functions.php');

/**
 * 类的自动加载
 */
function load($class)
{
    $path = str_replace('\\', '/', $class);
    require(ROOT . $path . '.php');
}
spl_autoload_register('load');


/**
 * 解析路由
 */
$controller = '\controllers\IndexController';
$action = 'index';
if(isset($_SERVER['PATH_INFO']))
{
    //     0  1      2
    //      /user/register
    $router = explode('/', $_SERVER['PATH_INFO']);
    $controller = '\controllers\\'.ucfirst($router[1]) . 'Controller';
    $action = $router[2];
}

$c = new $controller;
$c->$action();