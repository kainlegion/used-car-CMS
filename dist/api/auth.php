<?php

/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2017/11/28
 * Time: 10:19:16
 */
class Auth
{
    public function check()
    {
        file_put_contents('/tmp/xul', '200');
        $result['state'] = isset($_SESSION['username']) ? '200' : '401';
        echo json_encode($result);
        exit;
    }

    public function signin()
    {
        if ($_POST['username'] != '' & $_POST['password'] != '') {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['session_time'] = time();
            setcookie(session_name(), session_id());
            $result['state'] = '200';
            $result['title'] = '';
            $result['desc'] = '';
            echo json_encode($result);
            exit;
        }
    }

    public function signout()
    {
        if (!session_start()) {
            session_start();
        }
        //释放内存中已创建的所有$_SESSION变量
        session_unset();
        //清空当前用户对应的SESSION文件，释放SESSION id
        session_destroy();
        //删除存放cookie变量；session_name():存放session_id的cookie键值（在php.ini中指定）
        setcookie(session_name(), '', time() - 3600);
        $result['state'] = '200';
        echo json_encode($result);
        exit;
    }
}
