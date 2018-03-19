<?php
/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2017/11/28
 * Time: 15:45:37
 */

if (!session_start()) {
    session_start();
}

//加载自动加载类文件
class_exists('Autoload') or require __DIR__ . '/autoload.class.php';
//实例化文件自动加载类
Autoload::init();
$controller = ucfirst($_GET['c']);
$tmp = new $controller();
$action = $_GET['a'];
$tmp->$action();