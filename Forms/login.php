<?php

// require_once __DIR__ =
//constante qui contient le chemin absolu du dossier dans lequel se trouve le fichier courant.

session_start(); // demarrer la session
require_once __DIR__ . '/../Classes/User.php';
require_once __DIR__ . '/../db.php';

$message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  $user = new User();
  $result = $user->login($email, $password);

  if($result === true){

    // si l'email et le mot de passe sont valides, on recuperr l'id de l'utilisateur pour le stocker pour la session
    $db = new Database();
    $conn = $db->connect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email  = ?");
    $stmt->execute([$email]);

    // data = données de l'utilisateur (ID)
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // on stock l'id de l'utilisateur dans la session
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['email'] = $email;

    // redirection vers le dashboard avec recuperation et validation des informations
    header("Location: dashboard.php");
    exit();
  } else {
    $message = $result;
  }
}