<?php
session_start();
require_once 'pdo.php';

$user_id = $_SESSION['user_id'] ?? 1;

// Récupérer 3 objectifs du jour
$stmt = $pdo->query("SELECT id, conseil FROM objectifs ORDER BY id ASC LIMIT 3");
$objectifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les objectifs déjà cochés
$stmt = $pdo->prepare("SELECT objectif_id FROM user_objectives WHERE utilisateur_id = ?");
$stmt->execute([$user_id]);
$checked_objectifs = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Calcul de la progression
$nb_total = count($objectifs);
$nb_checked = count($checked_objectifs);
$progression = ($nb_checked / $nb_total) * 100;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>🎯 Ton Objectif Mirror Me</title>
<style>
    body { font-family: Arial, sans-serif; padding: 40px; background-color: #f9f9f9; }
    h1 { text-align: center; margin-bottom: 20px; }
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

<form method="post" action="save_objectifs.php">
    <?php foreach ($objectifs as $obj): ?>
        <div class="objectif">
            <label>
                <input type="checkbox" name="objectifs[]" value="<?= $obj['id'] ?>" <?= in_array($obj['id'], $checked_objectifs) ? 'checked' : '' ?>>
                <?= htmlspecialchars($obj['conseil']) ?>
            </label>
            <div class="tips">
                <?php
                // Récupérer les 3 tips associés
                $stmtTips = $pdo->prepare("SELECT tip FROM tips WHERE objectif_id = ? LIMIT 3");
                $stmtTips->execute([$obj['id']]);
                $tips = $stmtTips->fetchAll(PDO::FETCH_COLUMN);
                foreach ($tips as $tip) {
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
