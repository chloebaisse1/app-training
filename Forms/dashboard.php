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

  // recharge la page pour eviter le repost du formulaire, et suppression du message de succès ou d'echec apres affichage
  header("Location: dashboard.php");
  exit;
}

// suppression d'une séance
if(isset($_GET['delete'])){
  $id = (int) $_GET['delete'];
  $seance->delete($id);
  header("Location: dashboard.php");
  exit;
}

// gestion de recherche
$search = $_GET['search'] ?? '';
if($search){
  $seances = $seance->searchByUser($user_id, $search);
} else {
  $seances = $seance->getAllByUser($user_id);
}

// récupération des séances de l'utilisateur
$seances = $seance->getAllByUser($user_id);

// si aucune séance trouvée, initialisation d'un tableau vide
if (!$seances) {
  $seances = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>PlumbLifterPlaner🏋️‍♂️</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!--Affiche le nom de l'utilisateur avec connexion -->
  <h1>Bienvenue, <?=htmlspecialchars($user['prenom'])?> 👋 </h1>

  <?php if(isset($_SESSION['message'])): ?>
  <p style="color: green;"><?= htmlspecialchars($_SESSION['message']) ?></p>
  <?php unset($_SESSION['message']); // supprime le message après affichage, message stocké en session apres creation de seance ?>
<?php endif; ?>

    <h2> 🔍 Rechercher une séance</h2>
    <form method="GET" class="mb-3">
      <input type="text" name="search" placeholder="rechercher une séance" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      <button type="submit" class="btn btn-secondary btn-sm">Rechercher</button>
      <a href="dashboard.php" class="btn btn-secondary btn-sm">Annuler</a>
    </form>

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