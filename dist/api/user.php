<?php
class User
{
    public function __call($name, $arguments)
    {

    }

    public function userList()
    {
        $page = $_POST['page'];
        $rowNum = $_POST['rowNum'];
        $search = $_POST['search'];
        $searchName = $_POST['searchName'];
        $sortColumn = $_POST['sortColumn'];
        $order = $_POST['order'];

        $dbh = new db() or die('DB connection refused.');
        $sql = "select count(*) as count from user where type = 2";
        if (1 == $search) {
            $sql .= " and real_name like '%{$searchName}%'";
        }
        $count = $dbh->query($sql);

        $sql = "select * from user where type = 2 ";
        if (1 == $search) {
            $sql .= " and real_name like '%{$searchName}%' ";
        }
        if (!empty($sortColumn) && !empty($order)) {
            $sql .= " order by {$sortColumn} {$order} ";
        }
        if (!empty($rowN)) {
            $sql .= "limit " . ($page - 1) * $rowNum . ", {$rowNum}";
        }

        $list = $dbh->query($sql);
        if ($list) {
            $result['state'] = '200';
            $result['title'] = '';
            $result['desc'] = '';
            $result['data'] = (Object)array(
                'total' => $count[0]['count'],
                'list' => $list
            );
        }else {
            $result['state'] = '201';
            $result['title'] = 'get user list failed';
            $result['desc'] = '';
            $result['data'] = (Object)array(
                'total' => $count[0]['count'],
                'list' => $list
            );
        }
        echo json_encode($result);
    }

    public function add()
    {
        $data = array(
            ':username' => $_POST['account'],
            ':passwd' => hash('sha256', $_POST['pwd']),
            ':type' => 2,
            ':state' => 1,
            ':realName' => $_POST['name'],
            ':phone' => $_POST['phone'],
            ':regTime' => time(),
            ':regCapital' => $_POST['totalAmount']
        );

        $dbh = new db();
        $sql = "insert into user (username, passwd, type, state, real_name, phone, reg_time, reg_capital) ";
        $sql .= "values (:username, :passwd, :type, :state, :realName, :phone, :regTime, :regCapital)";
        $insertID = $dbh->query($sql, $data);
        if (0 < $insertID) {
            $result['state'] = '200';
            $result['title'] = 'add user successful';
            $result['desc'] = '';
        }else {
            $result['state'] = '201';
            $result['title'] = 'add user failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function getUser()
    {
        if (!empty($_POST['uid'])) {
            $data = array(
                $_POST['uid']
            );

            $dbh = new db();
            $sql = "select * from user where id = ?";
            $userInfo = $dbh->query($sql, $data);
            if (!empty($userInfo[0])) {
                $result['state'] = '200';
                $result['title'] = '';
                $result['desc'] = '';
                $result['data'] = (Object)array('userInfo' => $userInfo[0]);
            }else {
                $result['state'] = '201';
                $result['title'] = 'get user info failed';
                $result['desc'] = '';
            }
        }else {
            $result['state'] = '202';
            $result['title'] = 'error param';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function checkPasswd($uid, $passwd)
    {
        if (!empty($uid)) {
            $dbh = new db();
            $sql = "select passwd from user where id = ?";
            $passInfo = $dbh->query($sql, array($uid));
            if (!empty($passInfo) && $passInfo[0]['passwd'] == $passwd) {
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }

    public function edit()
    {
        $data = array(
            ':username' => $_POST['account'],
            ':passwd' => (true == $this->checkPasswd($_POST['uid'], $_POST['pwd'])) ? $_POST['pwd'] : hash('sha256', $_POST['pwd']),
            ':type' => 2,
            ':state' => $_POST['state'],
            ':realName' => $_POST['name'],
            ':phone' => $_POST['phone'],
            ':regCapital' => $_POST['totalAmount'],
            ':id' => $_POST['uid']
        );

        $dbh = new db();
        $sql = "update user ";
        $sql .= "set username = :username, passwd = :passwd, type = :type, state = :state, real_name = :realName, phone = :phone, reg_capital = :regCapital ";
        $sql .= "where id = :id";
        $rowCount = $dbh->query($sql, $data);
        if (0 <= $rowCount) {
            $result['state'] = '200';
            $result['title'] = 'edit user successful';
            $result['desc'] = '';
        }else {
            $result['state'] = '201';
            $result['title'] = 'edit user failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function delete()
    {
        $dbh = new db();
        $sql = "update user set state = 3 where id = ?";
        $rowCount = $dbh->query($sql, array($_POST['uid']));
        if ($rowCount) {
            $result['state'] = '200';
            $result['title'] = 'delete user successful';
            $result['desc'] = '';
        }else {
            $result['state'] = '201';
            $result['title'] = 'delete user failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function asyncCheck()
    {
        $username = trim($_POST['username']);
        $uid = $_POST['uid'];
        if (!empty($username)) {
            $data = array($username);
            $dbh = new db();
            $sql = 'select id from user where username = ?';
            if ($uid) {
                $sql .= ' and id != ?';
                array_push($data, $uid);
            }
            $uid = $dbh->query($sql, $data, 1);
            if (!empty($uid[0])) {
                $result['state'] = '201';
                $result['title'] = 'username already exist';
                $result['desc'] = '';
            }else {
                $result['state'] = '200';
                $result['title'] = 'username available';
                $result['desc'] = '';
            }
        }else {
            $result['state'] = '202';
            $result['title'] = 'username cannot be empty';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }
}
