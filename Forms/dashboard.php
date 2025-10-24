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


// instancié a vide afin de generer le message une fois la demande faite
$message= "";
// creation de nouvelle seance
$seance = new Seance();

// ajout de la seance
// champs requis pour creer une seance
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])){
  $type = $_POST['type'] ?? '';
  $duree = $_POST['duree'] ?? '';
  $date = $_POST['date'] ?? '';
  $notes = $_POST['notes'] ?? '';

  if($seance->create($user_id, $type, $duree, $date, $notes)){
    $_SESSION['message'] = "✅ La séance a bien été créée";
  } else {
    $_SESSION['message'] = "❌ Une erreur est survenue lors de la création de la séance";
  }

  // redirection uniquement après l'ajout
  header("Location: dashboard.php");
  exit;
}

// suppression d'une séance
if(isset($_GET['delete'])){
  $id = (int) $_GET['delete'];
  $seance->delete($id);
  $_SESSION['message'] = "✅ La séance a été supprimée";
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSr3zE+4z9aQ8K7uHk5DA1bz5Oe8qKjGZFxP6CwXkIBwz0V7Q3BQ=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
/>
</head>


<body>
  <div class="container mt-4">
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm rounded mb-4">
  <a class="navbar-brand px-3 py-2 text-dark fw-bold" href="/">PlumbLifterPlaner 🏋️‍♂️</a>

    <form method="GET" class="d-flex mx-auto">
      <input type="text" name="search" class="form-control me-2" placeholder="rechercher une séance" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      <button type="submit" class="btn btn-outline-secondary">🔍</button>
      <a href="dashboard.php" class="btn btn-light ms-2">Annuler</a>
    </form>


    <a class="btn btn-outline-danger" href="logout.php">🚪 Déconnexion</a>
    </nav>

     <!--Affiche le nom de l'utilisateur avec connexion -->
     <div class="mb-4">
    <h1>Bienvenue, <?=htmlspecialchars($user['prenom'])?> 👋 </h1>
    </div>


  <?php if(isset($_SESSION['message'])): ?>
  <p style="color: green;"><?= htmlspecialchars($_SESSION['message']) ?></p>
  <?php unset($_SESSION['message']); // supprime le message après affichage, message stocké en session apres creation de seance ?>
<?php endif; ?>


<div class="row">

<div class="col-md-6 mb-4">
<div class="card p-4 h-100 shadow-sm bg-light rounded">
<h2 class="h5 mb-3">➕ Ajouter une séance</h2>
    <form method="POST">
      <div class="class mb-3">
      <label class="form-label"> Type de séance :</label>
      <input type="text" name="type" class="form-control" required><br>
    </div>

      <div class="mb-3">
      <label class="form-label">Durée de la séance :</label>
      <input type="text" class="form-control" name="duree" placeholder="ex: 45min" required><br>
      </div>

      <div class="mb-3">
      <label class="form-label">Date de la séance :</label>
      <input type="datetime-local"  class="form-control" name="date" required><br>
      </div>

        <div class="mb-3">
      <label class="form-label">Notes :</label>
      <textarea class="form-control" name="notes"></textarea><br>
      </div>

      <button type="submit" name="ajouter" class="btn w-100" style="background-color: #5bc0de">Ajouter</button>
    </form>
  </div>
</div>

<div class="col-md-6 mb-4">
<div class="card p-4 h-100 shadow-sm bg-light rounded">
<h2 class="h5 mb-3">📅 Mes séances</h2>
  <?php if (count($seances) > 0): ?>
    <table class="table table-striped table-hover">
            <thead class="table-dark">
      <tr>
        <th>Type</th>
        <th>Durée</th>
        <th>Date</th>
        <th>Notes</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($seances as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['type']) ?></td>
          <td><?= htmlspecialchars($s['duree']) ?></td>
          <td><?= htmlspecialchars($s['date']) ?></td>
          <td><?= htmlspecialchars($s['notes']) ?></td>
<td>
<div class="d-flex gap-2">
    <a href="edit_seance.php?id=<?= $s['id'] ?>"
       title="Modifier"
       style="text-decoration: none; font-size: 1rem;">
       ✏️
    </a>
    <a href="dashboard.php?delete=<?= $s['id'] ?>"
       title="Supprimer"
       onclick="return confirm('Supprimer cette séance ?')"
       style="text-decoration: none; font-size: 1rem;">
       🗑️
    </a>
  </div>
</td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>Aucune séance enregistrée pour l’instant.</p>
  <?php endif; ?>
  </div>
  </div>
  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>