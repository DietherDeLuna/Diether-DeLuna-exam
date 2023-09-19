<?php

class DbConnect
{
    private $host = 'localhost';
    private $dbName = 'youtube_db';
    private $user = 'root';
    private $pass = '';

    public function connect()
    {
        try {
            $co = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
            $conn = new PDO($co, $this->user, $this->pass);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    }
}
