<?php
require_once '../db.php';

class User {
  private $conn;

  public function __construct(){
    $db = new Database();
    $this->conn = $db->connect();
  }

  public function register($prenom, $email, $password){

    $prenom = htmlspecialchars(trim($prenom) ?? '');
    $email = htmlspecialchars(trim($email ?? ''));
    $password = htmlspecialchars(trim($password ?? ''));

    // verification de la longueur du champs de caractères
    // strlen = longueur du string
    if(strlen($prenom) < 3) return "Le prénom doit contenir au moins 3 caractères";

    // effectuer un filter pour verifier que c'est un email valide
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return "L'adresse email n'est pas valide";

   // Vérifie le mot de passe avec regex
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    // preg_match va verifier si les informations sont bien des string ou correspondent au pattern
    if (!preg_match($pattern, $password)) {
    return "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
}

    try {
      // verification si l'email existe deja
      $check = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
      $check->execute([$email]);

      // ne pas donner l'information si l'email existe deja ou le mot de passe pour des question de sécurité
      if($check->rowCount()> 0) return "Email ou mot de passe existant";

      // hashage du mot de passe
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // creation de l'utilisateur
      $stmt = $this->conn->prepare("INSERT INTO users (prenom, email, password) VALUES (?, ?, ?)");
      $stmt->execute([$prenom, $email, $hashedPassword]);

      return "Utilisateur créé avec succès !";
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
  }
   // connexion de l'utilisateur
  public function login($email, $password){
    // trim va verifier que les champs ne contiennent que des caractères
  $email = trim($email ?? '');
  $password = trim($password ?? '');

    // filter_var pour la validation du champs email
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return "L'adresse email n'est pas valide";

  if(empty($password)) return "Informations manquantes";

  $stmt =$this->conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // si l'inscription est valide un message de succès est envoyé sinon un message d'erreur indiquant les informations manquantes
  if($user && password_verify($password, $user['password'])) {
    return "Connexion réussie";
  } else {
    return "L'email ou le mot de passe est incorrect";
  }
}

// mise en place de la verification de l'email pour pouvoir stocker l'id de l'utilisateur en session
public function getUserByEmail($email){
  $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
