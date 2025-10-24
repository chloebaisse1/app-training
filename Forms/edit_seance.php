<?php
session_start();
require_once __DIR__ . '/../Classes/Seance.php';
require_once __DIR__ . '/../db.php';

// verification de la connexion user, si non connecté redirection vers la page de connexion
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}


// creation de nouvelle seance
$seance = new Seance();

// instancié a vide afin de generer le message une fois la demande faite
$message= "";

// verification de l'id de l'utilisateur
if (!isset($_GET['id'])) {
  header("Location: dashboard.php");
  exit;
}

$id = (int) $_GET['id'];
$data = $seance->getById($id);

// si aucune séance trouvée -> retour sur le dashboard
if(!$data){
  header("Location: dashboard.php");
  exit;
}

// si le formulaire de modification est soumis
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $type = $_POST['type'] ?? '';
  $duree = $_POST['duree'] ?? '';
  $date = $_POST['date'] ?? '';
  $notes = $_POST['notes'] ?? '';

  if($seance->update($id, $type, $duree, $date, $notes)){
    $message = " ✅ La séance a bien été modifiée";

    // permet de rafraichir la page du dashboard apres 1 sec)
    header("Refresh: 1; URL=dashboard.php");
  } else {
    $message=" ❌ Une erreur est survenue lors de la modification de la séance";
  }
}
?>