<?php
require_once 'db.php';

$db = new Database();
$conn = $db->connect();

if($conn){
  echo "<p style='color:green;'> ✅ Connexion réussie</p>";
} else {
  echo "<p style='color:red;'>❌ Echec de la connexion</p>";
}
?>