<?php

namespace App\Service;

use \PDO;
use \PDOException;

class Connection
{
    private $pdo;

    private $user;

    private $dbName;

    private $host;

    private $dbPassword;

    function __construct($db_user, $db_name, $db_host, $db_password) {
        $this->user = $db_user;
        $this->dbName = $db_name;
        $this->host = $db_host;
        $this->dbPassword = $db_password;    

        try {
            $this->pdo = new PDO("mysql:host=" . $this->host,  $this->user,  $this->dbPassword);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->query("CREATE DATABASE IF NOT EXISTS " . $this->dbName);
            $this->pdo->query("use " . $this->dbName);
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function getConnection(): pdo {
        return $this->pdo;
    }

}