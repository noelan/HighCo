<?php

namespace App\Repository;

use App\Service\Connection;

class TransactionRepository
{
    private $pdo;

    public function __construct(Connection $connection)
    {
        $this->pdo = $connection->getConnection();
    }

    public function createTable()
    {
            $query = 'CREATE TABLE IF NOT EXISTS transaction (
                `phone` VARCHAR(10) NOT NULL PRIMARY KEY,
                `total_amount` INT NOT NULL
            );';
            $this->pdo->exec($query);
   
    }

    public function multipleInsert(array $data) {
        $formatedData = (implode(",", $data));
        $query = "INSERT INTO transaction (phone, total_amount) 
                  VALUES $formatedData 
                  ON DUPLICATE 
                  KEY UPDATE total_amount = VALUES(total_amount) + total_amount;";

        $statement = $this->pdo->prepare($query);
        $statement->execute();
    }

    public function insert(array $data) {
        $query = 'INSERT INTO transaction (phone, total_amount) 
                  VALUES (:phone, :amount)
                  ON DUPLICATE 
                  KEY UPDATE total_amount = total_amount + :amount;';

        $sth = $this->pdo->prepare($query);
        $sth->bindValue(':phone', $data['phone']);
        $sth->bindValue(':amount', $data['amount'], \PDO::PARAM_INT);
        $sth->execute();
    }

 

    public function getTotalAmountGroupedByPeople(): array
    {
        $query = 'SELECT total_amount, count(*) 
                  AS donators from transaction 
                  GROUP BY total_amount 
                  ORDER BY total_amount ASC;
                 ';
        return $this->pdo->query($query)->fetchAll();
    }
} 