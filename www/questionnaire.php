<?php
session_start();
require 'pdo.php'; // connexion PDO

// =====================
// 1. Définition des catégories et questions
// =====================
$categories = [
    "Sport" => [
        ['texte' => "Quand tu tombes, est-ce que tu as tendance à te relever encore plus motivé(e) ?", 'trait' => 'confiance'],
        ['texte' => "Aimes-tu te fixer des objectifs et travailler régulièrement pour les atteindre ?", 'trait' => 'confiance'],
        ['texte' => "Es-tu compétitif(ve), ou est-ce que tu préfères surtout te dépasser toi-même ?", 'trait' => 'confiance'],
        ['texte' => "Penses-tu que l’échec est une étape nécessaire pour progresser ?", 'trait' => 'confiance'],
    ],
    "Musique" => [
        ['texte' => "Est-ce que tu arrives à travailler dur pendant des heures pour perfectionner quelque chose qui te passionne ?", 'trait' => 'creativite'],
        ['texte' => "La musique t’aide-t-elle à exprimer tes émotions que tu n’arrives pas à dire autrement ?", 'trait' => 'creativite'],
        ['texte' => "Te considères-tu comme quelqu’un de créatif, qui aime transformer ses idées en quelque chose de concret ?", 'trait' => 'creativite'],
        ['texte' => "Te sens-tu transporté(e) quand tu écoutes une chanson qui résonne avec ton vécu ?", 'trait' => 'creativite'],
    ],
    "Litterature" => [
        ['texte' => "Serais-tu capable de prendre la parole en public pour défendre une cause ?", 'trait' => 'leadership'],
        ['texte' => "Est-ce que tu ressens le besoin d’exprimer tes émotions ou indignations par l’écriture ?", 'trait' => 'leadership'],
        ['texte' => "Quand tu vois une injustice, est-ce que tu te sens poussé(e) à agir ou à en parler ?", 'trait' => 'leadership'],
        ['texte' => "Pourrait tu etre le porte parole d'un mouvement ?", 'trait' => 'leadership'],
    ],
    "Cinema" => [
        ['texte' => "Aimes-tu être au centre de l’attention et briller dans un rôle ou une situation ?", 'trait' => 'perseverance'],
        ['texte' => "Sais-tu garder ton calme et te concentrer sous pression ?", 'trait' => 'perseverance'],
        ['texte' => "Es-tu persévérant(e) quand tu veux atteindre un objectif ?", 'trait' => 'perseverance'],
        ['texte' => "Utilises-tu l’humour comme une arme pour faire face aux difficultés ?", 'trait' => 'perseverance'],
    ],
];

// =====================
// 2. Récupération de la catégorie depuis l'URL (corrigé)
// =====================
$categorieChoisieRaw = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';

// Normalisation : suppression des accents et mise en majuscule pour comparaison
function normalize($string) {
    $string = mb_strtolower($string, 'UTF-8'); // tout en minuscules
    $string = str_replace(['é','è','ê','ë','à','â','ä','ô','ö','ù','û','ü','î','ï','ç'], 
                          ['e','e','e','e','a','a','a','o','o','u','u','u','i','i','c'], $string);
    return $string;
}

// Trouver la catégorie correspondante dans le tableau $categories
$categorieChoisie = null;
foreach ($categories as $key => $val) {
    if (normalize($key) === normalize($categorieChoisieRaw)) {
        $categorieChoisie = $key; // prend la clé exacte du tableau
        break;
    }
}

// Si aucune catégorie correspondante, prendre la première par défaut
if (!$categorieChoisie) {
    $categorieChoisie = array_key_first($categories); 
}

// Pour l'affichage
$categorieChoisieDisplayed = $categorieChoisie == "Litterature" ? "Littérature engagée" : $categorieChoisie;



// =====================
// 3. Soumission du formulaire
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($categories[$categorieChoisie] as $q) {
        $key = 'q_' . $q['trait'];
        $reponse = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        // Stocke dans session par catégorie
        $_SESSION['reponses'][$categorieChoisie][$key] = $reponse;
    }
    // Redirection vers la page résultat
    header("Location: resultat_questionnaire.php?categorie=" . urlencode($categorieChoisie));
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Questionnaire Mirror Me - <?= htmlspecialchars($categorieChoisieDisplayed) ?></title>
<style>
body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
h1 { text-align:center; }
.menu { margin-bottom: 20px; text-align:center; }
.menu a { margin: 0 10px; text-decoration: none; font-weight: bold; color: #333; }
.question { background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
input[type=text] { width: 90%; padding: 8px; margin-top: 5px; }
button { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 10px; display:block; margin-left:auto; margin-right:auto;}
button:hover { background: #45a049; }
h2 { margin-top:30px; color:#333; text-align:center; }
</style>
</head>
<body>

<h1>Questionnaire Mirror Me</h1>

<div class="menu">
    <?php 
    // dictionnaire pour corriger les noms affichés
    $labels = [ 
        "Litterature" => "Littérature engagée"
    ];
    ?>

    <?php foreach(array_keys($categories) as $key): ?>
        <a href="questionnaire.php?categorie=<?= urlencode($key) ?>">
            <?= htmlspecialchars($labels[$key] ?? $key) ?>
        </a>
    <?php endforeach; ?>
</div>


<h2><?= htmlspecialchars($categorieChoisieDisplayed) ?></h2>

<form method="POST">
    <?php foreach($categories[$categorieChoisie] as $q): ?>
        <div class="question">
            <label for="q_<?= $q['trait'] ?>"><?= $q['texte'] ?></label><br>
            <input type="text" id="q_<?= $q['trait'] ?>" name="q_<?= $q['trait'] ?>" placeholder="Écris ta réponse ici">
        </div>
    <?php endforeach; ?>
    <button type="submit">Valider le questionnaire</button>
</form>

</body>
</html>