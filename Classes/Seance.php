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
    $stmt = $this->conn->prepare("INSERT INTO seances (user_id, type, duree, date, notes) VALUES (?, ?, ?, ?, ?)");
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
  public function update($id, $type, $duree, $date, $notes){
    $stmt = $this->conn->prepare("UPDATE seances SET type = ?, duree = ?, date = ?, notes = ? WHERE id = ?");
    return $stmt->execute([$type, $duree, $date, $notes, $id]);
  }

  // supprimer une seance
  public function delete($id){
    $stmt = $this->conn->prepare("DELETE FROM seances WHERE id = ?");
    return $stmt->execute([$id]);
  }


  // BONUS
  // rechercher des séances par user_id et par mot clé
  public function searchByUser($user_id, $search){
    $stmt = $this->conn->prepare("SELECT * FROM seances WHERE user_id = ? AND (type LIKE ? OR DATE(date) LIKE ?) ORDER BY date DESC");
    $likeSearch = "%$search%";
    $stmt->execute([$user_id, $likeSearch, $likeSearch]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}