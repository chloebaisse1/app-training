<?php
session_start(); // demarrer la session

$_SESSION = []; // permet de vider le contenu de la session

session_destroy(); // destruction de la session

header("Location: login.php");// redirection de l'utilisateur vers la page de connexion
exit();

?>