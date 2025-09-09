<?php
session_start();
require 'pdo.php'; // connexion PDO

$user_id = $_SESSION['user_id'] ?? 1;

$checked_objectives = $_POST['objectifs'] ?? [];

// On supprime les anciens objectifs de cet utilisateur
$pdo->prepare("DELETE FROM user_objectives WHERE utilisateur_id = ?")->execute([$user_id]);

// On insère les nouveaux
$stmt = $pdo->prepare("
    INSERT INTO user_objectives (utilisateur_id, objectif_id) 
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE objectif_id = objectif_id
");

foreach ($checked_objectives as $obj_id) {
    $stmt->execute([$user_id, (int)$obj_id]);
}

// Redirection vers objectif.php
header("Location: objectif.php");
exit;
?>
