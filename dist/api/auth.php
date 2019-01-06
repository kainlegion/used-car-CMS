<?php

/**
 * Created by PhpStorm.
 * User: hasee
 * Date: 2017/11/28
 * Time: 10:19:16
 */
class Auth
{
    private $authList = array(
        '/car',
        '/carList'
    );
    
    public function __call($name, $arguments)
    {
        $this->signout();
    }
    
    public function check()
    {
        $path = $_POST['path'];
        $result = array('state' => '401');
        if (isset($_SESSION['username'])) {
            if (2 == $_SESSION['userType'] && !in_array($path, $this->authList)) {
                $result['state'] = '302';
            }else {
                $result['state'] = '200';
            }
        }
        
        echo json_encode($result);
        exit;
    }

    public function signin()
    {
        if ($_POST['username'] != '' & $_POST['password'] != '') {
            $dbh = new db() or die('DB connection refused.');
            $sql = "select * from user where username = ?";
            $data = array($_POST['username']);
            $userInfo = $dbh->query($sql, $data);
            
            if (hash('sha256', $_POST['password']) == $userInfo[0]['passwd']) {
//             if (1 == 1) {
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['userType'] = $userInfo[0]['type'];
                $_SESSION['session_time'] = time();
                setcookie(session_name(), session_id());
                $result['state'] = '200';
                $result['title'] = '';
                $result['desc'] = '';
            }else {
                $result['state'] = '201';
                $result['title'] = 'Wrong password!';
                $result['desc'] = '';
            }
            
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
    
    public function getUserType()
    {
        if (isset($_SESSION['username'])) {
            $result['state'] = 200;
            $result['userType'] = $_SESSION['userType'];
            $result['username'] = $_SESSION['username'];
        }else {
            $result['state'] = 401;
        }
        echo json_encode($result);
    }
}
