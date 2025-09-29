<?php
session_start();
require_once __DIR__ . '/pdo.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Vérifier que l'id du journal est envoyé
$journal_id = $_POST['journal_id'] ?? null;

if ($journal_id) {
    // Supprimer les objectifs liés à ce journal
    $stmt = $pdo->prepare("DELETE FROM objectifs_journal WHERE journal_id = ?");
    $stmt->execute([$journal_id]);

    // Supprimer les objectifs cochés par l'utilisateur pour ce journal
    $stmt = $pdo->prepare("DELETE FROM user_objectives WHERE utilisateur_id = ? AND journal_id = ?");
    $stmt->execute([$user_id, $journal_id]);

    // Supprimer le journal lui-même
    $stmt = $pdo->prepare("DELETE FROM journal_entries WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$journal_id, $user_id]);
}

// Retour au journal
header('Location: journal.php');
exit;
