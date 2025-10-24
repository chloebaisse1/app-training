<?php

class Database {
  private $host;
  private $dbname;
  private $username;
  private $password;
  private $conn;

  public function connect() {
    try {
      $this->conn = new PDO(
        "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
        $this->username,
        $this->password
      );
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $this->conn;
    } catch (PDOException $e) {
      die("Erreur de connexion : " . $e->getMessage());
    }
    }
}
?>