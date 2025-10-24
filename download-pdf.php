<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php'; // requis pour le telechargement des PDF
require_once __DIR__ . '/Classes/Seance.php';
require_once __DIR__ . '/db.php';


use Dompdf\Dompdf; // librairie pour le PDF
use Dompdf\Options; // librairie pour les options

// verification si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Connexion et récupération de l'utilisateur
$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupération des séances
$seance = new Seance();
$seances = $seance->getAllByUser($user_id);


// Generer du HTML pour le PDF
$html = '<h1>Mes séances - ' . htmlspecialchars($user['prenom']) . '</h1>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
$html .= '<thead><tr style="background-color:#f2f2f2;"><th>Type</th><th>Durée</th><th>Date</th><th>Notes</th></tr></thead><tbody>';

foreach($seances as $s){
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($s['type']) . '</td>';
    $html .= '<td>' . htmlspecialchars($s['duree']) . '</td>';
    $html .= '<td>' . htmlspecialchars($s['date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($s['notes']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

// Options de Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Téléchargement du PDF
$filename = 'mes_seances.pdf';
$dompdf->stream($filename, ["Attachment" => true]);
exit;