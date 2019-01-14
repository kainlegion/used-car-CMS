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
        $searchState = $_POST['searchState'];
        $searchRelease = $_POST['searchRelease'];
        $sortColumn = $_POST['sortColumn'];
        $order = $_POST['order'];

        $dbh = new db() or die('DB connection refused.');
        $sql = "select count(c.id) as count from car as c where 1 = 1 ";
        if (1 == $search) {
            $sql .= "and c.{$searchColumn} like '%{$searchName}%' ";
        }
        if (!empty($searchState)) {
            $sql .= "and c.state = {$searchState[0]} ";
        }
        if (!empty($searchRelease)) {
            $sql .= "and c.release = {$searchRelease[0]} ";
        }
        $count = $dbh->query($sql);

        $sql = "select c.*, c_p.file_name from car as c left join car_picture as c_p on c.id = c_p.car_id and c_p.thumbnail = 1 where 1 = 1 ";
        if (1 == $search) {
            $sql .= "and c.{$searchColumn} like '%{$searchName}%' ";
        }
        if (!empty($searchState)) {
            $sql .= "and c.state = {$searchState[0]} ";
        }
        if (!empty($searchRelease)) {
            $sql .= "and c.release = {$searchRelease[0]} ";
        }
        if (!empty($sortColumn) && !empty($order)) {
            $sql .= "order by c.{$sortColumn} {$order} ";
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
        $cid = $_GET['cid'];
        $fileName = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $filePath = $this->photoPath;
        if ($cid) {
            $filePath .= $cid . '/';
        }
        if (is_dir($filePath) or mkdir($filePath)) {
            if(move_uploaded_file($tmp, $filePath . $fileName)){
                $result['state'] = '200';
                $result['title'] = 'upload success';
                $result['desc'] = '';
            }else{
                $result['state'] = '201';
                $result['title'] = 'upload failed';
                $result['desc'] = '';
            }
        }else {
            $result['state'] = '202';
            $result['title'] = 'photo path is not dir';
            $result['desc'] = '';
        }

        echo json_encode($result);
    }

    public function deletePhoto()
    {
        $cid = $_POST['cid'];
        $fileName = $_POST['fileName'];

        if ($cid) {
            $data = array(
                $cid,
                $fileName
            );
            $sql = "select * from car_picture where car_id = ? and file_name = ?";
            $dbh = new db();
            $picInfo = $dbh->query($sql, $data);
            if ($picInfo[0]) {
                $sql = "delete from car_picture where id = ?";
                $deleteResult = $dbh->query($sql, array($picInfo[0]['id']));
                if (1 == $deleteResult) {
                    if (1 == $picInfo[0]['thumbnail']) {
                        $sql = "select * from car_picture where car_id = ? limit 1";
                        $nextPic = $dbh->query($sql, array($cid));
                        if ($nextPic[0]) {
                            $sql = "update car_picture set thumbnail = 1 where id = ?";
                            $dbh->query($sql, array($nextPic[0]['id']));
                        }
                    }
                    unlink($this->photoPath . $cid . '/' . $fileName);
                    $result['state'] = '200';
                    $result['title'] = 'delete success';
                    $result['desc'] = '';
                }else {
                    $result['state'] = '201';
                    $result['title'] = 'delete failed';
                    $result['desc'] = '';
                }
            }
        }else {
            if (unlink($this->photoPath . $fileName)) {
                $result['state'] = '200';
                $result['title'] = 'delete success';
                $result['desc'] = '';
            }else {
                $result['state'] = '201';
                $result['title'] = 'delete failed';
                $result['desc'] = '';
            }
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
                                $value['name']
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
        $cid = $_POST['cid'];
        if (!empty($cid)) {
            $data = array(
                $cid
            );

            $dbh = new db();
            $sql = "select * from car where id = ?";
            $userInfo = $dbh->query($sql, $data);
            if (!empty($userInfo[0])) {
                if ($userInfo[0]['particular_year']) {
                    $userInfo[0]['particular_year'] = date('Y-m-d', $userInfo[0]['particular_year']);
                }
                $investor = array();
                $sql = "select user_id from car_investor where car_id = ?";
                $investor = $dbh->query($sql, $data, 1);
                $photo = array();
                $sql = "select * from car_picture where car_id = ?";
                $photo = $dbh->query($sql, $data);
                if (!empty($photo)) {
                    foreach ($photo as $key => &$value) {
                        $value['name'] = $value['file_name'];
                        $value['status'] = 'finished';
                    }
                }
                $result['state'] = '200';
                $result['title'] = '';
                $result['desc'] = '';
                $result['data'] = (Object)array('carInfo' => $userInfo[0], 'investor' => array_flip($investor), 'photo' => $photo);
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
                    $investorData = array(
                        $data[':cid'],
                        $value['id']
                    );
                    $sql = "insert into car_investor values (0, ?, ?)";
                    $dbh->query($sql, $investorData);
                }
            }
            if (!empty($uploadList)) {
                $sql = "select id from car_picture where car_id = ? and thumbnail = 1";
                $thumbnail = $dbh->query($sql, array($data[':cid']), 1);
                foreach ($uploadList as $key => $value) {
                    $picData = array(
                        $data[':cid'],
                        $value['name']
                    );
                    $sql = "select id from car_picture where car_id = ? and file_name = ?";
                    $picExist = $dbh->query($sql, $picData, 1);
                    if (!$picExist[0] && file_exists($this->photoPath . $data[':cid'] . '/' . $value['name'])) {
                        $uploadData = array(
                            $data[':cid'],
                            ($key == 0 && !$thumbnail[0]) ? 1 : 0,
                            $value['name']
                        );
                        $sql = "insert into car_picture values (0, ?, ?, ?)";
                        $dbh->query($sql, $uploadData);
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

    private function delPhotoDir($dirName)
    {
        if ( $handle = opendir( $dirName ) ) {
            while ( false !== ( $item = readdir( $handle ) ) ) {
                if ( $item != “.” && $item != “..” ) {
                    if ( is_dir( $dirName . '/' . $item ) ) {
                        delPhotoDir( $dirName . '/' . $item );
                    } else {
                        unlink( $dirName . '/' . $item );
                    }
                }
            }
            closedir( $handle );
            if( rmdir( $dirName ) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete()
    {
        $cid = $_POST['cid'];
        $dbh = new db();
        $sql = "delete from car where id = ?";
        $rowCount = $dbh->query($sql, array($cid));
        if ($rowCount) {
            $sql = "delete from car_picture where car_id = ?";
            $delPhotoRow = $dbh->query($sql, array($cid));
            $delPhotoDir = delPhotoDir($this->photoPath . '/' . $cid);
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
