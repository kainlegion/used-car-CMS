<?php
class Car
{
    public $photoPath = __DIR__ . '/../photo/';
    private $emissionGradeList = array(
        'C1' => '国I',
        'C2' => '国II',
        'C3' => '国III',
        'C4' => '国IV',
        'C5' => '国V',
        'C6' => '国VI',
        'E1' => '欧I',
        'E2' => '欧II',
        'E3' => '欧III',
        'E4' => '欧IV',
        'E5' => '欧V',
        'E6' => '欧VI'
    );

    public function __call($name, $arguments)
    {

    }

    public function carList()
    {
        $page = $_POST['page'];
        $rowNum = $_POST['rowNum'];
        $search = $_POST['search'];
        $searchColumn = $_POST['searchColumn'];
        $searchName = $_POST['searchName'];
        $sortColumn = $_POST['sortColumn'];
        $order = $_POST['order'];

        $dbh = new db() or die('DB connection refused.');
        $sql = "select count(*) as count from car where 1 = 1";
        if (1 == $search) {
            $sql .= " and {$searchColumn} like '%{$searchName}%'";
        }
        $count = $dbh->query($sql);

        $sql = "select * from car where 1 = 1 ";
        if (1 == $search) {
            $sql .= " and {$searchColumn} like '%{$searchName}%' ";
        }
        if (!empty($sortColumn) && !empty($order)) {
            $sql .= " order by {$sortColumn} {$order} ";
        }
        $sql .= "limit " . ($page - 1) * $rowNum . ", {$rowNum}";
        $list = $dbh->query($sql);
        if ($list) {
            foreach ($list as $key => &$value) {
                $value['particular_year'] = $value['particular_year'] ? date('Y-m-d', $value['particular_year']) : '-';
                $value['emission_grade'] = $this->emissionGradeList[$value['emission_grade']];
            }
            $result['state'] = '200';
            $result['title'] = '';
            $result['desc'] = '';
            $result['data'] = (Object)array(
                'total' => $count[0]['count'],
                'list' => $list
            );
        }else {
            $result['state'] = '201';
            $result['title'] = 'get car list failed';
            $result['desc'] = '';
            $result['data'] = (Object)array(
                'total' => $count[0]['count'],
                'list' => $list
            );
        }
        echo json_encode($result);
    }

    public function getEmissionGradeList()
    {
        echo json_encode($this->emissionGradeList);
    }

    public function uploadPhoto()
    {
        $imgname = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $filepath = $this->photoPath . $imgname;
        if(move_uploaded_file($tmp, $filepath)){
            $result['state'] = '200';
            $result['title'] = 'upload success';
            $result['desc'] = '';
        }else{
            $result['state'] = '201';
            $result['title'] = 'upload failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function deletePhoto()
    {
        $cid = $_POST['cid'];
        $fileName = $_POST['fileName'];
        if ($cid) {
            $fileName = $cid . '/' . $fileName;
        }
        if (unlink($this->photoPath . $fileName)) {
            $result['state'] = '200';
            $result['title'] = 'delete success';
            $result['desc'] = '';
        }else {
            $result['state'] = '201';
            $result['title'] = 'delete failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function add()
    {
        $data = array(
            $_POST['brand'],
            $_POST['model'],
            strtotime($_POST['date']),
            $_POST['emissionGrade'],
            time(),
            $_POST['purchasePrice'],
            $_POST['setupCost'],
            $_POST['investment'],
            $_POST['salePrice'],
            $_POST['numOfInvestment'],
            $_POST['proportion'],
            $_POST['state'],
            $_POST['desc'],
            $_POST['selfFunds'],
            $_POST['release']
        );

        $investor = $_POST['investor'];

        $uploadList = $_POST['uploadList'];

        $dbh = new db();
        $sql = "insert into car ";
        $sql .= "values (0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertID = $dbh->query($sql, $data);
        if (0 < $insertID) {
            if (!empty($investor)) {
                foreach ($investor as $key => $value) {
                    $data = array(
                        $insertID,
                        $value['id']
                    );
                    $sql = "insert into car_investor values (0, ?, ?)";
                    $dbh->query($sql, $data);
                }
            }
            if (!empty($uploadList)) {
                if (is_dir($this->photoPath . $insertID . '/') or mkdir($this->photoPath . $insertID . '/')) {
                    foreach ($uploadList as $key => $value) {
                        if (rename($this->photoPath . $value['name'], $this->photoPath . $insertID . '/' . $value['name'])) {
                            $data = array(
                                $insertID,
                                $key == 0 ? 1 : 0,
                                $this->photoPath . $insertID . '/' . $value['name']
                            );
                            $sql = "insert into car_picture values (0, ?, ?, ?)";
                            $dbh->query($sql, $data);
                        }
                    }
                }
            }
            $result['state'] = '200';
            $result['title'] = 'add car successful';
            $result['desc'] = '';
        }else {
            $result['state'] = '201';
            $result['title'] = 'add car failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function getCar()
    {
        if (!empty($_POST['cid'])) {
            $data = array(
                $_POST['cid']
            );

            $dbh = new db();
            $sql = "select * from car where id = ?";
            $userInfo = $dbh->query($sql, $data);
            if (!empty($userInfo[0])) {
                if ($userInfo[0]['particular_year']) {
                    $userInfo[0]['particular_year'] = date('Y-m-d', $userInfo[0]['particular_year']);
                }
                $result['state'] = '200';
                $result['title'] = '';
                $result['desc'] = '';
                $result['data'] = (Object)array('carInfo' => $userInfo[0]);
            }else {
                $result['state'] = '201';
                $result['title'] = 'get car info failed';
                $result['desc'] = '';
            }
        }else {
            $result['state'] = '202';
            $result['title'] = 'error param';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function edit()
    {
        $data = array(
            ':cid' => $_POST['cid'],
            ':brand' => $_POST['brand'],
            ':model' => $_POST['model'],
            ':date' => strtotime($_POST['date']),
            ':emissionGrade' => $_POST['emissionGrade'],
            ':purchasePrice' => $_POST['purchasePrice'],
            ':setupCost' => $_POST['setupCost'],
            ':investment' => $_POST['investment'],
            ':salePrice' => $_POST['salePrice'],
            ':numOfInvestment' => $_POST['numOfInvestment'],
            ':proportion' => $_POST['proportion'],
            ':state' => $_POST['state'],
            ':desc' => $_POST['desc'],
            ':selfFunds' => $_POST['selfFunds'],
            ':release' => $_POST['release']
        );

        $investor = $_POST['investor'];

        $uploadList = $_POST['uploadList'];

        $dbh = new db();
        $sql = "update car ";
        $sql .= "set brand = :brand, model = :model, particular_year = :date, emission_grade = :emissionGrade, state = :state, description = :desc, ";
        $sql .= "purchase_price = :purchasePrice, setup_cost = :setupCost, investment = :investment, sale_price = :salePrice, ";
        $sql .= "num_of_investment = :numOfInvestment, proportion = :proportion, self_funds = :selfFunds, `release` = :release ";
        $sql .= "where id = :cid";
        $rowCount = $dbh->query($sql, $data);
        if (0 <= $rowCount) {
            if (!empty($investor)) {
                $sql = "delete from car_investor where car_id = ?";
                $dbh->query($sql, array($data[':cid']));
                foreach ($investor as $key => $value) {
                    $data = array(
                        $insertID,
                        $value['id']
                    );
                    $sql = "insert into car_investor values (0, ?, ?)";
                    $dbh->query($sql, $data);
                }
            }
            if (!empty($uploadList)) {
                if (is_dir($this->photoPath . $insertID . '/') or mkdir($this->photoPath . $insertID . '/')) {
                    foreach ($uploadList as $key => $value) {
                        if (rename($this->photoPath . $value['name'], $this->photoPath . $insertID . '/' . $value['name'])) {
                            $data = array(
                                $insertID,
                                $key == 0 ? 1 : 0,
                                $this->photoPath . $insertID . '/' . $value['name']
                            );
                            $sql = "insert into car_picture values (0, ?, ?, ?)";
                            $dbh->query($sql, $data);
                        }
                    }
                }
            }
            $result['state'] = '200';
            $result['title'] = 'edit car successful';
            $result['desc'] = '';
        }else {
            $result['state'] = '201';
            $result['title'] = 'edit car failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }

    public function delete()
    {
        $dbh = new db();
        $sql = "delete from car where id = ?";
        $rowCount = $dbh->query($sql, array($_POST['cid']));
        if ($rowCount) {
            $result['state'] = '200';
            $result['title'] = 'delete car successful';
            $result['desc'] = '';
        }else {
            $result['state'] = '201';
            $result['title'] = 'delete car failed';
            $result['desc'] = '';
        }
        echo json_encode($result);
    }
}
