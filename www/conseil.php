<?php
session_start();

// On vérifie qu'il y a un résultat de questionnaire
if (!isset($_SESSION['trait_result'])) {
    die("Tu n'as pas encore rempli le questionnaire.");
}

$trait = $_SESSION['trait_result'];

// Conseils simples pour chaque trait
$conseils = [
    "acharnement" => [
        "Note tes objectifs chaque jour et vois tes progrès.",
        "Commence par les tâches importantes sans attendre.",
        "Félicite-toi pour chaque effort."
    ],
    "creativite" => [
        "Prends 30 min pour créer (dessin, écriture, musique...).",
        "Observe autour de toi et note tes idées.",
        "Essaie de résoudre les problèmes autrement que d’habitude."
    ],
    "perserverance" => [
        "Ne te décourage pas, note tes petites victoires.",
        "Finis ce que tu commences.",
        "Entoure-toi de personnes qui t’encouragent."
    ],
    "reflexion" => [
        "Avant de décider, pense aux avantages et inconvénients.",
        "Prends un moment chaque jour pour réfléchir à tes objectifs.",
        "Apprends de tes erreurs pour avancer."
    ],
    "leadership" => [
        "Prends des initiatives autour de toi.",
        "Écoute les autres et motive-les.",
        "Montre l’exemple et assume tes choix."
    ],
    "engagement" => [
        "Choisis une cause qui te tient à cœur et agis régulièrement.",
        "Informe-toi pour mieux défendre tes idées.",
        "Encourage les autres à faire pareil."
    ]
];

// Récupère les conseils du trait courant ou un conseil par défaut
$mesConseils = $conseils[$trait] ?? ["Sois toi-même et découvre tes forces !"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conseils MirrorMe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            text-align: center;
            background: #f9f9f9;
        }
        h1 {
            margin-bottom: 20px;
        }
        ul {
            display: inline-block;
            text-align: left;
            margin-top: 20px;
        }
        li {
            margin-bottom: 10px;
        }
        a {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <h1>Développe tes qualités !</h1>
    <p>
        Voici des conseils pour ton profil : <strong><?= htmlspecialchars($trait); ?></strong>
    </p>
    <ul>
        <?php foreach ($mesConseils as $c): ?>
            <li>✅ <?= htmlspecialchars($c) ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="categories.php">Retour aux catégories</a>
</body>
</html>
