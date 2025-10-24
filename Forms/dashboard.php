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

//instance de seance vide pour pouvoir ensuite recuperer les données
$seances = [];
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

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title> Dashboard PlumbLifterPlaner🏋️‍♂️</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!--Affiche le nom de l'utilisateur avec connexion -->
  <h1>Bienvenue, <?=htmlspecialchars($user['prenom'])?> 👋 </h1>

  <?php if($message): //afficher un message apres le CRUD ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>


    <h2> Ajouter une séance</h2>
    <form method="POST">
      <label> Type de séance :</label>
      <input type="text" name="type" required><br>

      <label>Durée de la séance :</label>
      <input type="text" name="duree" placeholder="ex: 45min" required><br>

      <label>Date de la séance :</label>
      <input type="datetime-local" name="date" required><br>

      <label>Notes :</label>
      <textarea name="notes"></textarea><br>

      <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2> 📅 Mes séances</h2>

    <?php if (count($seances) > 0): // verifie le nombre de seance, si sup a 0 affiche des seances ?>
      <table border="1" cellpadding="8">
        <tr>
          <th>Type</th>
          <th>Durée</th>
          <th>Date</th>
          <th>Notes</th>
          <th>Actions</th>
        </tr>

        <?php foreach($seances as $s): // une fois les seances trouvé boucle sur les differentes seances avec les informations ?>
          <tr>
            <td><?= htmlspecialchars($s['type']) ?></td>
            <td><?= htmlspecialchars($s['duree']) ?></td>
            <td><?= htmlspecialchars($s['date']) ?></td>
            <td><?= htmlspecialchars($s['notes']) ?></td>
            <td>

            <a href="edit_seance.php?id=<?= $s['id'] ?>">✏️ Modifier</a>
            <a href="dashboard.php?delete=<?= $s['id'] ?>" onclick="return confirm('Supprimer cette séance ?')">🗑️ Supprimer</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
        <?php else : // si pas de seance trouvé indiquer un message ?>
          <p>Aucune séance pour le moment.</p>
          <?php endif; ?>

</body>
</html>