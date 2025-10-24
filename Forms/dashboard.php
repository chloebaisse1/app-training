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

// creation de nouvelle seance
$seance = new Seance();
// instancié a vide afin de generer le message une fois la demande faite
$message= "";

// ajout de la seance
// champs requis pour creer une seance
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])){
  $type = $_POST['type'] ?? '';
  $duree = $_POST['duree'] ?? '';
  $date = $_POST['date'] ?? '';
  $notes = $_POST['notes'] ?? '';

  if($seance->create($user_id, $type, $duree, $date, $notes)){
    // si la creation est réussie
    $message = " ✅ La seance a bien été créée";
  } else {
    $message = " ❌ Une erreur est survenue lors de la création de la seance";
  }
}

// suppression d'une séance
if(isset($_GET['delete'])){
  $id = (int) $_GET['delete'];
  $seance->delete($id);
  header("Location: dashboard.php");
  exit;
}
?>
