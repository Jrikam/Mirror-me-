<?php
session_start();

$categories = [
    "Musique",
    "Cinéma",
    "Sport",
    "Littérature",
    
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix de la catégorie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f9f9f9;
        }
        h1 {
            margin-bottom: 40px;
        }
        a {
            display: inline-block;
            margin: 15px;
            padding: 15px 30px;
            background: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }
        a:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <h1>Choisis une catégorie</h1>
    <?php foreach ($categories as $cat): ?>
        <a href="questionnaire.php?categorie=<?= urlencode($cat); ?>">
            <?= htmlspecialchars($cat); ?>
        </a>
    <?php endforeach; ?>
</body>
</html>
