<?php
class db
{
    private $dsn = "mysql: host=127.0.0.1;port=3306;dbname=used_car_cms";
    private $username = 'root';
    private $passwd = '8826381d1581';
    private $dbh;
    
    public function __construct()
    {
        try {
            $this->dbh = new PDO($this->dsn, $this->username, $this->passwd);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e){
            $e->getMessage();
        }
    }
    
    private function getPasswd()
    {
//         $cmd = 'sudo grep mysql_root_passwd /root/env.txt | sed -n "s/mysql_root_passwd://p"';
//         exec($cmd, $output, $return);
//         $this->passwd = $output[0];
    }
    
    public function query($sql, $data = array())
    {
        if ($sql) {
            if ($this->dbh instanceof PDO) {
                $stmt = $this->dbh->prepare($sql);
                $result = $stmt->execute($data);
                if (preg_match('/^insert/i', $sql)) {
                    $result = $this->dbh->lastInsertId();
                }elseif (preg_match('/^select/i', $sql)) {
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }else {
                    $result = $stmt->rowCount();
                }
                
                return $result;
            }else {
                echo 'dbh is not instance';
            }
            
        }
    }
    
    public function __destruct()
    {
        $this->dbh = null;
    }
}