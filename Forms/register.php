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

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-5">
    <div class="col-md-6 offset-md-3">
      <div class="card shadow p-4">
        <h3 class="text-center mb-3"> Inscription</h3>


        <?php if($message): ?>
          <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required minlength="3">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="nom@exemple.com">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="8">
          </div>

          <button type="submit" class="btn w-100" style="background-color: #5bc0de">S’inscrire</button>
        </form>

        <p class="text-center mt-3 mb-0">
          Déjà inscrit ?
          <a href="login.php" class="text-decoration-none fw-bold" style="color: #5bc0de;">
            Se connecter ici
          </a>
        </p>

      </div>
     </div>
    </div>
</body>
</html>