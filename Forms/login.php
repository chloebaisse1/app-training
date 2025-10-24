<?php
session_start(); // demarrer la session
require_once __DIR__ . '/../Classes/User.php';
require_once __DIR__ . '/../db.php';


// require_once __DIR__ =
//constante qui contient le chemin absolu du dossier dans lequel se trouve le fichier courant.

$message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  $user = new User();
  $result = $user->login($email, $password);

  if($result === true){

    // si l'email et le mot de passe sont valides, on recupere l'id de l'utilisateur pour le stocker pour la session
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow p-4">
            <h3 class="text-center mb-3">Connexion</h3>

        <?php if($message): ?>
          <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
          <?php endif; ?>


      <form method="POST" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($email ?? '') ?>">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Mot de passe</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn w-100" style="background-color: #5bc0de">Se connecter</button>
      </form>
      </div>
    </div>
</div>
</body>
</html>
