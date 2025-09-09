<?php
session_start();

// Vérification du résultat du questionnaire
if (!isset($_SESSION['trait_result'])) {
    die("Aucun résultat disponible. Remplis d'abord le questionnaire.");
}

$trait = $_SESSION['trait_result'];

// Tableau des conseils par trait
$conseils_traits = [
    "acharnement" => [
        "Fixe-toi des objectifs clairs chaque jour et note tes progrès.",
        "Ne procrastine pas, attaque les tâches importantes dès le matin.",
        "Apprends à te féliciter pour chaque effort fourni."
    ],
    "creativite" => [
        "Consacre 30 min par jour à une activité créative (dessin, écriture, musique...).",
        "Observe le monde autour de toi et note tes idées originales.",
        "Cherche à résoudre les problèmes de manière inattendue."
    ],
    "perserverance" => [
        "Ne te décourage pas face aux obstacles, note tes réussites.",
        "Prends l’habitude de finir ce que tu commences.",
        "Entoure-toi de personnes qui te motivent à persévérer."
    ],
    "reflexion" => [
        "Avant chaque décision, note les avantages et inconvénients.",
        "Prends du temps chaque jour pour réfléchir à tes objectifs.",
        "Apprends de chaque erreur pour progresser."
    ],
    "leadership" => [
        "Prends des initiatives dans ton entourage ou travail.",
        "Écoute activement et motive les autres.",
        "Assume tes choix et inspire par l’exemple."
    ],
    "engagement" => [
        "Choisis une cause qui te tient à cœur et agis chaque semaine.",
        "Informe-toi pour mieux défendre ce qui est important pour toi.",
        "Encourage les autres à s’impliquer à leurs niveaux."
    ]
];

// Récupération des conseils correspondant au trait
$conseils = $conseils_traits[$trait] ?? ["Sois toi-même et explore tes forces !"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conseils Mirror Me</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            text-align: center;
            background-color: #f9f9f9;
        }
        h1 {
            margin-bottom: 20px;
        }
        ul {
            text-align: left;
            display: inline-block;
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
            transition: background 0.3s;
        }
        a:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <h1>Développe tes qualités !</h1>
    <p>
        Voici quelques conseils pour cultiver les qualités associées à ton profil : 
        <strong><?= htmlspecialchars($trait); ?></strong>
    </p>
    <ul>
        <?php foreach ($conseils as $tip): ?>
            <li>✅ <?= htmlspecialchars($tip); ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="categories.php">Retour aux catégories</a>
</body>
</html>
