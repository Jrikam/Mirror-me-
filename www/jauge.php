<?php
session_start();
require_once 'pdo.php';

// Vérification de l'utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération de la progression
$stmt = $pdo->prepare("SELECT progression FROM progression WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$data = $stmt->fetch();
$progression = $data ? $data['progression'] : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma progression</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
            background-color: #f9f9f9;
        }
        h1 {
            margin-bottom: 30px;
        }
        .barre {
            width: 300px;
            height: 25px;
            border: 1px solid #000;
            background-color: #eee;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
        }
        .remplissage {
            height: 100%;
            background-color: #4caf50;
            width: <?= $progression ?>%;
            transition: width 0.3s;
        }
        p {
            margin-top: 15px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Jauge de progression</h1>
    <div class="barre">
        <div class="remplissage"></div>
    </div>
    <p>Progression : <?= $progression ?>%</p>
</body>
</html>
