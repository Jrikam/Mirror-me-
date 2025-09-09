<?php
session_start();
require 'pdo.php';

$user_id = $_SESSION['user_id'] ?? 1;
$checked_objectives = $_POST['objectifs'] ?? [];

// Supprimer les objectifs précédents
$stmt = $pdo->prepare("DELETE FROM user_objectives WHERE utilisateur_id = ?");
$stmt->execute([$user_id]);

// Insérer les nouveaux objectifs cochés
$stmt = $pdo->prepare("INSERT INTO user_objectives (utilisateur_id, objectif_id, date_checked) VALUES (?, ?, NOW())");

foreach ($checked_objectives as $obj_id) {
    $stmt->execute([$user_id, (int)$obj_id]);
}

// Redirection vers la page des objectifs
header("Location: objectif.php");
exit;
