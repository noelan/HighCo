<?php

namespace App\Repository;

use App\Service\Connection;

class UserRepository
{
    private $pdo;

    public function __construct(Connection $connection)
    {
        $this->pdo = $connection->getConnection();
    }

    public function createTable()
    {
            $query = 'CREATE TABLE IF NOT EXISTS user (
                      phone VARCHAR(10) NOT NULL PRIMARY KEY,
                      `date` DATETIME NOT NULL,
                      postal_code VARCHAR(5) NOT NULL
                      );';


            $this->pdo->exec($query);
    }

    public function multipleInsert(array $data) 
    {
        $formatedData = (implode(",", $data));
        $query = "INSERT INTO user (phone, postal_code, date) 
                  VALUES $formatedData 
                  ON DUPLICATE 
                  KEY UPDATE 
                  date = IF(date > VALUES(date), date, VALUES(date)), 
                  postal_code = IF(date > VALUES(date), postal_code, VALUES(postal_code));";
        

        $sth = $this->pdo->prepare($query);
        $sth->execute();
    }


    /* Update the postal_code and the date if the dateToInsert is more recent than the date in the database  */
    public function insert(array $data)
    {
        $query = 'INSERT INTO user (phone, postal_code, date) 
                  VALUES (:phone, :postal_code, :date)
                  ON DUPLICATE 
                  KEY UPDATE 
                  date = IF(date > VALUES(date), date, VALUES(date)), 
                  postal_code = IF(date > VALUES(date), postal_code, VALUES(postal_code));';

        $sth = $this->pdo->prepare($query);
        $sth->bindValue(':phone', $data['phone']);
        $sth->bindValue(':postal_code', $data['postal_code']);
        $sth->bindValue(':date', $data['date']);
        $sth->execute();
    }

 

} 