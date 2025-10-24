<?php

// require_once __DIR__ =
//constante qui contient le chemin absolu du dossier dans lequel se trouve le fichier courant.

require_once __DIR__ . '/../Classes/User.php';
require_once __DIR__ . '/../db.php';


// initialisation d'une variable vide pour stocker l'information (succès ou erreur)

// initialisation des variables du formulaire d'inscroption
$prenom = $email = $password = "";
$message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $prenom = $_POST['prenom'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  // creation de l'utilisateur si les informations sont présentes
  $user = new User();
  $result = $user->register($prenom, $email, $password);

  // si la requete est valide et l'utilisateur est crée avec succès
  if($result === "Utilisateur créé avec succès !"){
    // systeme de redirection vers la page de connexion
    header("Location: login.php");
    // on sors de la function
    exit();

} else {
  // si erreur, on affiche le message d'erreur
  $message = $result;
}
}
// verification si la redirection apres inscription fonctionne
if(isset($_GET['success'])){
  $message = "Inscription réussie !";

  // remettre les champs a zero
  $prenom = $email = $password = "";
}

?>