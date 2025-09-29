<?php
session_start();
require_once 'pdo.php';

$user_id = $_SESSION['user_id'] ?? 1;
$journal_id = $_GET['journal_id'] ?? null;

if (!$journal_id) exit('Journal introuvable');

// Récupérer 3 objectifs pour ce journal
$objectifs = $pdo->prepare("SELECT id, titre FROM objectifs_journal WHERE journal_id = ? ORDER BY id ASC LIMIT 3");
$objectifs->execute([$journal_id]);
$objectifs = $objectifs->fetchAll(PDO::FETCH_ASSOC);

// Objectifs déjà cochés
$checked_objectifs = $pdo->prepare("SELECT objectif_id FROM user_objectives WHERE utilisateur_id = ? AND journal_id = ?");
$checked_objectifs->execute([$user_id, $journal_id]);
$checked_objectifs = $checked_objectifs->fetchAll(PDO::FETCH_COLUMN);

// Calcul progression
$progression = count($checked_objectifs) / max(count($objectifs), 1) * 100;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>🎯 Ton Objectif Mirror Me</title>
<style>
body { font-family: Arial, sans-serif; padding: 40px; background:#f9f9f9; }
h1 { text-align:center; margin-bottom:20px; }
.progress-bar { margin:20px 0; background:#eee; border-radius:10px; overflow:hidden; }
.progress { width:<?= $progression ?>%; background:#4caf50; padding:5px 0; color:#fff; text-align:center; }
.objectif { margin-bottom:15px; background:#fff; padding:10px; border-radius:5px; border:1px solid #ccc; }
.tips { margin-left:20px; color:#555; }
button { margin-top:20px; padding:10px 20px; border:none; background:#333; color:#fff; border-radius:5px; cursor:pointer; }
button:hover { background:#555; }
</style>
</head>
<body>
<h1>🎯 Ton Objectif Mirror Me</h1>

<div class="progress-bar">
    <div class="progress">Progression : <?= round($progression) ?>%</div>
</div>

<form method="post" action="save_objectifs.php?journal_id=<?= $journal_id ?>">
    <?php foreach ($objectifs as $obj): ?>
        <div class="objectif">
            <label>
                <input type="checkbox" name="objectifs[]" value="<?= $obj['id'] ?>" <?= in_array($obj['id'], $checked_objectifs) ? 'checked' : '' ?>>
                <?= htmlspecialchars($obj['titre']) ?>
            </label>
            <div class="tips">
                <?php
                $stmtTips = $pdo->prepare("SELECT tip FROM tips_journal WHERE objectif_journal_id = ? LIMIT 3");
                $stmtTips->execute([$obj['id']]);
                foreach ($stmtTips->fetchAll(PDO::FETCH_COLUMN) as $tip) {
                    echo "💡 " . htmlspecialchars($tip) . "<br>";
                }
                ?>
            </div>
        </div>
    <?php endforeach; ?>
    <button type="submit">Mettre à jour l'objectif coché</button>
</form>
</body>
</html>
