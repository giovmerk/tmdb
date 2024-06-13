<?php
#dichiaro la classe database e dichiaro delle funzioni all'interno
class Database {
    private $host = '127.0.0.1';
    private $db_name = 'TMDB';
    private $username = 'root';
    private $password = 'root';
    private $port = 3306;
    public $conn;

    public function __construct() {
        $conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Errore di connessione: " . $exception->getMessage();
        }
    }

    public function read($param, $table, $condition, $page = 0) #manda a schermo (utilizzabile come select)
    {
        $offset = 20;
        $query = "SELECT $param FROM $table";
        echo $query;
        if ($condition !== null) {
            // $query = $query . " WHERE $condition";
            $query .= " WHERE $condition";
        }
        if ($page !== null) {
            $query .= " LIMIT $offset OFFSET " . ($page - 1)* $offset . ";";
        }
        else
        {
            $page=1;
            $query .= " LIMIT $offset OFFSET " . ($page - 1)* $offset . ";";

        }
        echo $query;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        #var_export manda a schermo in maniera semplificata, fetchAll trasforma l'output in json, PDO::FETCH_ASSOC associa i valori e li rende univoci
        // var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function create($table, $param, $data) #inserisce dei datti all'interno di un database (utilizzabile come insert)
    {
        $string = implode(', ', $data);
        // $string = mysql_escape_string($string);
        $query = "INSERT IGNORE INTO $table($param) VALUES ($string)";
        echo $query;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        // var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function update($table, $data, $condition) #aggiorna dei datti all'interno di un database (utilizzabile come update)
    {
        $query = `UPDATE $table SET $data`;
        if ($condition !== null) {
            $query = $query . `WHERE $condition`;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function delete($param, $table, $condition) #cancewlla dei datti all'interno di un database (utilizzabile come delete)
    {
        $query = `DELETE $param FROM $table`;
        if ($condition !== null) {
            $query = $query . `WHERE $condition`;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
    }


    public function real_escape_string($string) {
        return $this->conn->quote($string);
    }
}
