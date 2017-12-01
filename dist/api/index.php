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

$controller = ucfirst($_GET['c']);
require './' . $controller . '.php';
if (class_exists($controller)) {
    $tmp = new $controller();
    $action = $_GET['a'];
    if (method_exists($tmp, $action)) {
        $tmp->$action();
    }
}