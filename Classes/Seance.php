<?php
require_once '../db.php';

class Seance {
  private $conn;

  public function __construct() {
    $db = new Database();
    $this->conn = $db->connect();
  }

  // mise en place du crud

  // creation d'une seance
  public function create($user_id, $type, $duree, $date, $notes){
    $stmt = $this->conn->prepare("INSERT INTO seances (user_id, type, duree, date, notes) VALUE (?, ?, ?, ?, ?)");
    return $stmt->execute([$user_id, $type, $duree, $date, $notes]);
  }
  // récupération d'une seance

  // rechercher une seance par son id


  // modifier une seance

  // supprimer une seance
}