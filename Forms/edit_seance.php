<?php
session_start();
require_once __DIR__ . '/../Classes/Seance.php';
require_once __DIR__ . '/../db.php';

// Verification connexion user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$seance = new Seance();
$message = "";

// Vérifie si on recupere l'ID du user
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int) $_GET['id'];
$data = $seance->getById($id);

// Si aucune séance trouvée → retour au dashboard
if (!$data) {
    header("Location: dashboard.php");
    exit;
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $duree = $_POST['duree'] ?? '';
    $date = $_POST['date'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if ($seance->update($id, $type, $duree, $date, $notes)) {
        $message = "✅ Séance modifiée avec succès !";
        header("Refresh: 1; URL=dashboard.php");
    } else {
        $message = "❌ Erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>Modifier une séance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body>
  <div class="container mt-5">
  <h3 classs="mb-4 text-primary text-center"> ✏️ Modifier une séance</h3>

  <?php if($message): ?>
    <p class="alert alert-success"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="POST" class="card p-4 shadow-sm bg-light rounded mx-auto" style="max-width: 500px;">
    <div class class="mb-3">
    <label for="type" class="form-label">Type de séance : </label>
    <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($data['type']) ?>" required><br>
  </div>

  <div class="mb-3">
    <label for="duree" class="form-label">Durée de la séance :</label>
    <input type="text" name="duree" class="form-control" value="<?= htmlspecialchars($data['duree']) ?>" required><br>
    </div>

    <div class="mb-3">
    <label for="date" class="form-label">Date de la séance :</label>
    <input type="datetime-local" class="form-control" name="date" value="<?= date('Y-m-d\TH:i', strtotime($data['date'])) ?>" required><br>
    </div>

    <div class="mb-3">
    <label for="notes" class="form-label">Notes :</label>
    <textarea  class="form-control"name="notes"><?= htmlspecialchars($data['notes']) ?></textarea><br>
    </div>


    <button type="submit" class="btn w-100" style="background-color: #5bc0de">Mettre à jour</button>
    </form>

    <p class="mt-3 text-center">
  <a href="dashboard.php" class="btn btn-light border rounded-circle shadow-sm" title="Retour">
  <i class="bi bi-arrow-left-circle"></i>
  </a>
</p>

  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>