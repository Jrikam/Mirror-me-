<?php
session_start();
require 'pdo.php'; // connexion à la base

// =====================
// 1. Catégories + questions
// =====================
$categories = [
    "Sport" => [
        ['texte' => "Quand tu rates, tu as plutôt envie d’abandonner ou de revenir plus fort ?", 'trait' => 'confiance'],
        ['texte' => "Tu te fixes des objectifs concrets que tu bosses régulièrement ?", 'trait' => 'confiance'],
        ['texte' => "Es-tu compétitif(ve) ou tu préfères surtout te dépasser toi-même ?", 'trait' => 'confiance'],
        ['texte' => "Tu vois l’échec comme un frein ou une étape pour progresser ?", 'trait' => 'confiance'],
    ],
    "Musique" => [
        ['texte' => "Tu peux passer des heures à t’entraîner sur quelque chose qui te passionne ?", 'trait' => 'creativite'],
        ['texte' => "La musique t’aide à dire ce que tu ne dirais pas autrement ?", 'trait' => 'creativite'],
        ['texte' => "Tu te considères comme quelqu’un de créatif, qui aime donner forme à ses idées ?", 'trait' => 'creativite'],
        ['texte' => "Une chanson peut-elle te toucher comme si elle racontait ton histoire ?", 'trait' => 'creativite'],
    ],
    "Litterature" => [
        ['texte' => "Tu pourrais prendre la parole en public pour défendre une cause ?", 'trait' => 'leadership'],
        ['texte' => "Tu ressens parfois le besoin d’écrire pour exprimer tes idées ou émotions ?", 'trait' => 'leadership'],
        ['texte' => "Face à une injustice, tu ressens le besoin d’agir ou d’en parler ?", 'trait' => 'leadership'],
        ['texte' => "Tu pourrais être la voix ou le porte-parole d’un groupe ?", 'trait' => 'leadership'],
    ],
    "Cinema" => [
        ['texte' => "Tu aimes être au centre de l’attention et jouer un rôle ?", 'trait' => 'perseverance'],
        ['texte' => "Sais-tu garder ton calme quand la pression monte ?", 'trait' => 'perseverance'],
        ['texte' => "Tu t’accroches vraiment quand tu as un objectif ?", 'trait' => 'perseverance'],
        ['texte' => "Tu utilises parfois l’humour pour gérer les moments compliqués ?", 'trait' => 'perseverance'],
    ],
];

// =====================
// 2. Catégorie choisie
// =====================
$categorieChoisieRaw = $_GET['categorie'] ?? '';

function normalize($string) {
    $string = mb_strtolower($string, 'UTF-8');
    $string = str_replace(
        ['é','è','ê','ë','à','â','ä','ô','ö','ù','û','ü','î','ï','ç'],
        ['e','e','e','e','a','a','a','o','o','u','u','u','i','i','c'],
        $string
    );
    return $string;
}

$categorieChoisie = null;
foreach ($categories as $key => $val) {
    if (normalize($key) === normalize($categorieChoisieRaw)) {
        $categorieChoisie = $key;
        break;
    }
}

// si rien trouvé, on prend la première
if (!$categorieChoisie) {
    $categorieChoisie = array_key_first($categories); 
}

// pour affichage
$categorieChoisieDisplayed = $categorieChoisie == "Litterature" ? "Littérature engagée" : $categorieChoisie;

// =====================
// 3. Sauvegarde des réponses
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($categories[$categorieChoisie] as $q) {
        $key = 'q_' . $q['trait'];
        $reponse = $_POST[$key] ?? '';
        $_SESSION['reponses'][$categorieChoisie][$key] = trim($reponse);
    }
    header("Location: resultat_questionnaire.php?categorie=" . urlencode($categorieChoisie));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Questionnaire - <?= htmlspecialchars($categorieChoisieDisplayed) ?></title>
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
    $labels = [ "Litterature" => "Littérature engagée" ];
    foreach(array_keys($categories) as $key): ?>
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
            <input type="text" id="q_<?= $q['trait'] ?>" name="q_<?= $q['trait'] ?>" placeholder="Ta réponse ici...">
        </div>
    <?php endforeach; ?>
    <button type="submit">Envoyer</button>
</form>

</body>
</html>
