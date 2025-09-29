<?php
session_start();
require_once 'pdo.php';

$user_id = $_SESSION['user_id'] ?? 1;

// Récupérer les objectifs cochés depuis le formulaire
$objectifs_coches = $_POST['objectifs'] ?? [];

// Supprimer les anciennes sélections pour l'utilisateur
$stmt = $pdo->prepare("DELETE FROM user_objectives WHERE utilisateur_id = ?");
$stmt->execute([$user_id]);

// Insérer les nouveaux objectifs cochés avec date_checked
if (!empty($objectifs_coches)) {
    $stmt = $pdo->prepare("INSERT INTO user_objectives (utilisateur_id, objectif_id, date_checked) VALUES (?, ?, NOW())");
    foreach ($objectifs_coches as $id_obj) {
        $stmt->execute([$user_id, $id_obj]);
    }
}

// Redirection vers la page objectifs pour recalculer la progression
header("Location: objectifs.php");
exit;
