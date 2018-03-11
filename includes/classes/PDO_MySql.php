<?php

class PDO_MySql
{
    public $debug = TRUE;
    protected $db_pdo;
    protected $pdo;
    function __construct(){
        $this->pdo = $this->open();
    }

    public function query($query){
        $split = explode(' ', $query);
        $action = strtolower($split[0]);
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        /*
        if($action == 'select'){
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }
            return $result;
        }else{
            return true;
        }
        */
        return $stmt;
    }

    public function fetch_array($stmt){
        if($stmt->rowCount() > 1){
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }
            return $result;
        }else{
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    }

    public function num_rows($stmt){
        return $stmt->rowCount();
    }

    public function affected_rows($stmt){
        return $stmt->rowCount();
    }

    public function insert_id(){
        return $this->open()->lastInsertId();
    }

    function open() {
        if (!$this->db_pdo)
        {
            if ($this->debug)
            {
                $this->db_pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE .'', DB_SERVER_USERNAME, DB_SERVER_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            }
            else
            {
                $this->db_pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE .'', DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
            }
        }
        return $this->db_pdo;
    }
}