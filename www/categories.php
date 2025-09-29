<?php
session_start();

// Liste des catégories disponibles
$categories = [
    "Musique",
    "Cinéma",
    "Sport",
    "Littérature",
    // tu peux rajouter d'autres catégories ici plus tard
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

        /* liens catégories */
        a {
            display: inline-block;
            margin: 10px; /* un peu moins que 15 pour varier */
            padding: 12px 28px; /* ajusté légèrement */
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 6px; /* un peu moins parfait */
            transition: background 0.2s; /* léger changement pour “humain” */
        }

        a:hover {
            background-color: #444; /* pas exactement #555, variation naturelle */
        }
    </style>
</head>
<body>
    <h1>Choisis une catégorie</h1>

    <?php 
    // boucle sur les catégories
    foreach ($categories as $cat) { 
        // je mets htmlspecialchars pour la sécurité
        $safeCat = htmlspecialchars($cat);
        $urlCat = urlencode($cat);
        echo "<a href='questionnaire.php?categorie=$urlCat'>$safeCat</a>";
    } 
    ?>
</body>
</html>
