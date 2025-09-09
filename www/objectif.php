<?php
session_start();
require_once 'pdo.php';

// Utilisateur connecté
$user_id = $_SESSION['user_id'] ?? 1;

// Récupérer les derniers journaux
$stmt = $pdo->prepare("SELECT contenu, created_at FROM journal_entries WHERE utilisateur_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$last_journal = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer tous les objectifs depuis la table
$stmt = $pdo->query("SELECT id, conseil FROM objectifs ORDER BY id ASC");
$all_objectives = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Objectifs déjà cochés par l'utilisateur
$stmt = $pdo->prepare("SELECT objectif_id FROM user_objectives WHERE utilisateur_id = ?");
$stmt->execute([$user_id]);
$checked_objectives = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Définir les objectifs générés selon le dernier journal
$generated_objectives = [];
$coach_message = '';

if ($last_journal) {
    $text = strtolower($last_journal['contenu']);
    $coach_message = "✅ J'ai lu/j'ai entendu ton journal du " . date('d/m/Y', strtotime($last_journal['created_at'])) . ". Voici ce que tu pourrais faire :";

    foreach ($all_objectives as $obj) {
        $obj_text = strtolower($obj['conseil']);

        // Conditions simples de correspondance mots-clés
        if ((str_contains($text, 'confiance') && str_contains($obj_text, 'confiance')) ||
            (str_contains($text, 'harceler') && str_contains($obj_text, 'strategie')) ||
            (str_contains($text, 'stress') && str_contains($obj_text, 'relaxation')) ||
            true // Ajouter tous les objectifs si aucun mot-clé
        ) {
            $generated_objectives[] = $obj;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Objectifs Mirror Me</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background-color: #f9f9f9; }
        h1 { text-align: center; margin-bottom: 20px; }
        .coach-message { margin: 20px 0; padding: 15px; background-color: #e0f7fa; border-left: 6px solid #00bcd4; }
        .objectives { margin-top: 30px; }
        .objectif { margin-bottom: 15px; padding: 10px; background: #fff; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ccc; }
        .objectif input[type=checkbox] { transform: scale(1.3); margin-right: 10px; }
        button { margin-top: 20px; padding: 10px 20px; border: none; background-color: #333; color: #fff; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #555; }
        .date { font-size: 0.9em; color: #666; }
        .back-link { margin-top: 30px; display: block; text-align: center; }
    </style>
</head>
<body>
    <h1>🎯 Tes Objectifs Mirror Me</h1>

    <?php if ($coach_message): ?>
        <div class="coach-message"><?= htmlspecialchars($coach_message) ?></div>
    <?php endif; ?>

    <form method="post" action="save_objectifs.php">
        <div class="objectives">
            <?php foreach ($generated_objectives as $obj): ?>
                <div class="objectif">
                    <label>
                        <input type="checkbox" name="objectifs[]" 
                               value="<?= $obj['id'] ?>" 
                               <?= in_array($obj['id'], $checked_objectives) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($obj['conseil']) ?>
                    </label>
                    <span class="date"><?= date('d/m/Y') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit">Mettre à jour les objectifs cochés</button>
    </form>

    <a class="back-link" href="journal.php">⬅️ Retour au journal</a>
</body>
</html>
