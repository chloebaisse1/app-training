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
    header("Location: dashboard.php");
  } else {
    $message=" ❌ Une erreur est survenue lors de la modification de la séance";
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>Modifier une séance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <h1> ✏️ Modifier une séance</h1>

  <?php if($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="POST">
    <label for="type">Type de séance : </label>
    <input type="text" name="type" value="<?= htmlspecialchars($data['type']) ?>" required><br>

    <label for="duree">Durée de la séance :</label>
    <input type="text" name="duree" value="<?= htmlspecialchars($data['duree']) ?>" required><br>

    <label for="date">Date de la séance :</label>
    <input type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i', strtotime($data['date'])) ?>" required><br>

    <label for="notes">Notes :</label>
    <textarea name="notes"><?= htmlspecialchars($data['notes']) ?></textarea><br>

    <button type="submit">Mettre à jour</button>
  </form>

  <p><a href="dashboard.php">⬅️ Retour au tableau de bord</a></p>
</body>
</html>