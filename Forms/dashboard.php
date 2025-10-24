<?php
session_start();
require_once __DIR__ . '/../Classes/Seance.php';
require_once __DIR__ . '/../db.php';


// si mon utilisateur n'est pas connecté, on le redirige vers la page de connexion
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

// recuperation de l'ID de l'utilisateur
$user_id = $_SESSION['user_id'];

// connexion en base pour récuperr les informations de l'utilisateur
$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
