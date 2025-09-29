<?php
session_start();
require_once __DIR__ . '/pdo.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$journal_id = $_GET['journal_id'] ?? null;

if (!$journal_id) {
    exit('Journal non défini.');
}

// Récupérer les objectifs cochés dans le formulaire
$objectifs_coches = $_POST['objectifs'] ?? [];

// Supprimer les anciennes entrées pour ce journal
$stmt = $pdo->prepare("DELETE FROM user_objectives WHERE utilisateur_id = ? AND journal_id = ?");
$stmt->execute([$user_id, $journal_id]);

// Ajouter les nouvelles entrées
$stmtInsert = $pdo->prepare("INSERT INTO user_objectives (utilisateur_id, journal_id, objectif_id, fait, date_checked) VALUES (?, ?, ?, 1, NOW())");

foreach ($objectifs_coches as $obj_id) {
    $stmtInsert->execute([$user_id, $journal_id, $obj_id]);
}

// 3️⃣ 🔹 Redirection automatique vers journal.php
header("Location: journal.php");
exit;
