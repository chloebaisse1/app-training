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
  // récupération des seances d'un utilisateur
  public function getAllByUser($user_id){
    $stmt = $this->conn->prepare("SELECT * FROM seances WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // rechercher une seance par son id
  public function getById($id){
    $stmt = $this->conn->prepare("SELECT * FROM seances WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  // modifier une seance

  // supprimer une seance

}