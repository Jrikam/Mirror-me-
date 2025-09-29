<?php
session_start();
require_once 'pdo.php';

// Vérifie si l'utilisateur est bien connecté
if (empty($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// On récupère la progression en base
$sql = $pdo->prepare("SELECT progression FROM progression WHERE user_id = ?");
$sql->execute([$_SESSION['user_id']]);
$row = $sql->fetch();

$progress = $row ? (int)$row['progression'] : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi de ta progression</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
            background: #f4f4f4;
        }
        h1 {
            margin-bottom: 25px;
        }
        .barre {
            width: 320px;
            height: 25px;
            margin: auto;
            background: #e1e1e1;
            border: 1px solid #aaa;
            border-radius: 12px;
            overflow: hidden;
        }
        .remplissage {
            height: 100%;
            width: <?= $progress ?>%;
            background: #4caf50;
            transition: width .4s;
        }
        p {
            margin-top: 15px;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <h1>Ta jauge de progression</h1>
    <div class="barre">
        <div class="remplissage"></div>
    </div>
    <p>Progression actuelle : <?= $progress ?>%</p>
</body>
</html>
