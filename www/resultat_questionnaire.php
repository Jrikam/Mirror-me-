<?php
session_start();
require 'pdo.php'; // connexion PDO

// -------------------------------
// 1) Normalisation catégorie sûre
// -------------------------------
function normalize_categorie($raw) {
    if (!$raw) return 'Sport';
    $s = strtolower(trim($raw));
    $s = str_replace(['_', '-'], ' ', $s);

    // mappings directs
    $map = [
        'sport' => 'Sport',
        'cinema' => 'Cinema',
        'musique' => 'Musique',
        'comédie' => 'Comédie',
        'comedie' => 'Comédie',
        'litterature' => 'Litterature',
    ];
    if (isset($map[$s])) return $map[$s];

    // fallback "contient"
    foreach ($map as $k => $v) {
        if (strpos($s, $k) !== false) return $v;
    }
    // valeur sûre
    return 'Sport';
}

$categorieChoisie = normalize_categorie($_GET['categorie'] ?? ($_SESSION['categorie'] ?? 'Sport'));

// -------------------------------
// 2) Vérification des réponses
// -------------------------------
if (!isset($_SESSION['reponses']) || !is_array($_SESSION['reponses'])) {
    echo "Aucune réponse enregistrée. <a href='questionnaire.php'>Faire le questionnaire</a>";
    exit;
}

// ---------------------------------------------------------
// 3) Scoring des réponses (positif / neutre / négatif)
// ---------------------------------------------------------
function normalize_text($s) {
    // Si c'est un tableau, on le transforme en texte
    if (is_array($s)) {
        $s = implode(' ', $s);
    }
    $s = mb_strtolower($s ?? '', 'UTF-8');
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($t !== false) $s = strtolower($t);
    }
    return $s;
}


// Définition des traits par catégorie
$mappedTrait = [
    'Sport'              => 'confiance',
    'Musique'            => 'creativite',
    'Litterature'=> 'leadership',
    'Cinema'             => 'perseverance',
];


// Init traits
$traits = [
    'confiance'    => 0,
    'creativite'   => 0,
    'leadership'   => 0,
    'perseverance' => 0,
];

// Mots-clés
$positifs = [
    'oui','capable','facile','confiant','motivé','motivation',
    'je peux','je veux','reussi','ok','prêt','prete','possible',
    'aisance','orale','parler','exprimer','fort','facilement'
];

$negatifs = ['non','pas','peur','difficile','impossible','incapable','timide','decourage','décourage','fatigu','stress','anxieux','anxieuse','anxiete','rate','echoue','échec'];

// Extraction des réponses pour la catégorie
$reponses = [];
$needle = strtolower(str_replace(' ', '', $categorieChoisie));
foreach ($_SESSION['reponses'] as $k => $v) {
    $kk = strtolower(str_replace(['-', '_'], '', $k));
    if (strpos($kk, $needle) !== false) {
        $reponses[$k] = $v;
    }
}

// Score
$traitCateg = $mappedTrait[$categorieChoisie] ?? 'confiance';
$nbQuestions = max(0, count($reponses));
$scoreAccumule = 0;

foreach ($reponses as $q => $r) {
    $rn = normalize_text($r);
    $score = 30; // neutre
    $hasPos = false;
    $hasNeg = false;

    foreach ($positifs as $w) if (strpos($rn, $w) !== false) { $hasPos = true; break; }
    foreach ($negatifs as $w) if (strpos($rn, $w) !== false) { $hasNeg = true; break; }

    if ($hasNeg) $score = 10;
    elseif ($hasPos) $score = 50;
    else $score = 30;

    $traits[$traitCateg] += $score;
    $scoreAccumule += $score;
}

// Calcul pourcentage
$scoreMax = max(1, $nbQuestions) * 50;
$pourcentage_trait = $nbQuestions > 0 ? round(($traits[$traitCateg]/$scoreMax)*100) : 0;
$pourcentage_trait = max(0, min(100, $pourcentage_trait));

// -------------------------------
// 4) Star aléatoire
// -------------------------------
$stmt = $pdo->prepare("SELECT * FROM stars WHERE categorie = :categorie ORDER BY RAND() LIMIT 1");
$stmt->execute(['categorie' => $categorieChoisie]);
$star = $stmt->fetch(PDO::FETCH_ASSOC);
$image_path = 'images/default.jpg';
if (!empty($star['image_path']) && file_exists(__DIR__.'/'.$star['image_path'])) $image_path = $star['image_path'];

// -------------------------------
// 5) Conseils dynamiques
// -------------------------------
$conseils = [
    'confiance' => [
        'low' => ["Commence par des micro-défis.", "Respire, compte et agis progressivement."],
        'mid' => ["Continue à sortir de ta zone de confort progressivement.", "Prépare-toi à l’avance."],
        'high'=> ["Aide quelqu’un d’autre à oser.", "Vise une prise de parole plus ambitieuse."],
    ],
    'creativite' => [
        'low' => ["Reproduis une œuvre que tu aimes puis modifie un détail.", "Fais un 'brain dump' sans te censurer."],
        'mid' => ["Teste un format différent (audio, vidéo, collage).", "Fixe-toi un mini-projet créatif en 48h."],
        'high'=> ["Partage ta création et demande un feedback.", "Monte un petit défi créatif avec des amis."],
    ],
    'leadership' => [
        'low' => ["Commence par co-animer plutôt que mener seul.", "Prépare 2 questions ouvertes pour lancer un échange."],
        'mid' => ["Organise une courte réunion avec ordre du jour clair.", "Donne un feedback positif précis."],
        'high'=> ["Délègue une partie d’un projet et fais un suivi régulier.", "Propose une mini-initiative à ton groupe."],
    ],
    'perseverance' => [
        'low' => ["Commence 10 min sans pression.", "Avance de 1% par jour.","Croire en toi est déjà un grand pas"],
        'mid' => ["Suis tes habitudes et protège ton rythme.", "Prévois un plan B rapide si tu bloques."],
        'high'=> ["Augmente légèrement la difficulté.", "Mentorise quelqu’un pour consolider ta persévérance."],
    ],
];

$niveau = ($pourcentage_trait < 40) ? 'low' : (($pourcentage_trait < 70) ? 'mid' : 'high');
$listeConseils = $conseils[$traitCateg][$niveau] ?? [];
shuffle($listeConseils);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Résultat Questionnaire - <?= htmlspecialchars($categorieChoisie) ?></title>
<style>
body{font-family:Arial,sans-serif;background:#f4f4f9;padding:20px;text-align:center;}
.card{background:#fff;border-radius:12px;padding:16px;margin:14px auto;max-width:720px;box-shadow:0 2px 6px rgba(0,0,0,0.08);}
.progress-bar{width:100%;background:#e7e7ec;border-radius:20px;overflow:hidden;height:26px;}
.progress{height:26px;background:#4CAF50;width:0%;color:#fff;line-height:26px;font-weight:bold;}
img{max-width:220px;border-radius:10px;display:block;margin:0 auto 10px;}
ul{text-align:left;max-width:680px;margin:0 auto;}
.muted{color:#666;font-size:14px;}
a.btn{display:inline-block;padding:10px 16px;border-radius:10px;background:#2f76ff;color:#fff;text-decoration:none;font-weight:600;margin:8px;}
</style>
</head>
<body>

<h1>Ton icône correspondante – <?= htmlspecialchars($categorieChoisie) ?></h1>

<div class="card">
    <?php if ($star): ?>
        <img src="<?= htmlspecialchars($star['image_path'] ?? 'images/default.jpg') ?>" alt="<?= htmlspecialchars($star['nom'] ?? 'Icône') ?>">
        <h2><?= htmlspecialchars($star['nom'] ?? 'Icône') ?></h2>
        <p class="muted"><?= htmlspecialchars($star['description'] ?: 'Aucune description disponible.') ?></p>
        <p><strong>Trait principal (star) :</strong> <?= htmlspecialchars($star['trait_principal'] ?? 'Non défini') ?></p>
    <?php else: ?>
        <p>Aucune star trouvée pour cette catégorie.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Progression de ton trait dominant : <?= ucfirst($traitCateg) ?></h3>
    <div class="progress-bar">
        <div class="progress" style="width: <?= (int)$pourcentage_trait ?>%;"><?= (int)$pourcentage_trait ?>%</div>
    </div>
</div>

<div class="card">
    <h3>Conseils personnalisés</h3>
    <ul>
        <?php foreach ($listeConseils as $c): ?>
            <li><?= htmlspecialchars($c) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="card">
    <a class="btn" href="journal.php?categorie=<?= urlencode($categorieChoisie) ?>">Accéder à ton journal 📔</a>
    <a class="btn" href="questionnaire.php?categorie=<?= urlencode($categorieChoisie) ?>">Refaire le questionnaire</a>
</div>

</body>
</html>
