<?php
session_start();
require 'pdo.php'; // connexion bdd

// --------- Choix catégorie ---------
function normaliserCategorie($cat) {
    if (empty($cat)) return 'Sport';

    $c = strtolower(trim($cat));
    $c = str_replace(['-', '_'], ' ', $c);

    $correspondances = [
        'sport'       => 'Sport',
        'cinema'      => 'Cinema',
        'musique'     => 'Musique',
        'litterature' => 'Litterature',
        'comedie'     => 'Comédie',
        'comédie'     => 'Comédie'
    ];

    if (isset($correspondances[$c])) return $correspondances[$c];

    foreach ($correspondances as $k => $v) {
        if (strpos($c, $k) !== false) return $v;
    }

    return 'Sport';
}

$categorieChoisie = normaliserCategorie($_GET['categorie'] ?? ($_SESSION['categorie'] ?? 'Sport'));

// --------- Vérif réponses ---------
if (empty($_SESSION['reponses']) || !is_array($_SESSION['reponses'])) {
    echo "Aucune réponse enregistrée. <a href='questionnaire.php'>Retour au questionnaire</a>";
    exit;
}

// --------- Fonction pour nettoyer texte ---------
function nettoieTexte($txt) {
    if (is_array($txt)) $txt = implode(' ', $txt);
    $txt = mb_strtolower($txt ?? '', 'UTF-8');
    if (function_exists('iconv')) {
        $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $txt);
        if ($tmp !== false) $txt = strtolower($tmp);
    }
    return $txt;
}

// --------- Table correspondance traits ---------
$traitCategorie = [
    'Sport'       => 'confiance',
    'Musique'     => 'creativite',
    'Litterature' => 'leadership',
    'Cinema'      => 'perseverance',
];

// Base des traits
$traits = [
    'confiance'    => 0,
    'creativite'   => 0,
    'leadership'   => 0,
    'perseverance' => 0,
];

// --------- Mots-clés ---------
$motsPos = ['oui','capable','facile','motivé','motivation','je peux','je veux','reussi','ok','prêt','prete','possible','aisance','parler','exprimer','fort','facilement'];
$motsNeg = ['non','pas','peur','difficile','impossible','timide','incapable','decourage','décourage','fatigu','stress','anxieux','anxieuse','anxiete','rate','echoue','échec'];

// --------- Récup réponses ---------
$reponses = [];
$needle = strtolower(str_replace(' ', '', $categorieChoisie));
foreach ($_SESSION['reponses'] as $k => $v) {
    $kk = strtolower(str_replace(['-', '_'], '', $k));
    if (strpos($kk, $needle) !== false) {
        $reponses[$k] = $v;
    }
}

// --------- Calcul score ---------
$trait = $traitCategorie[$categorieChoisie] ?? 'confiance';
$nbQ   = count($reponses);
$score = 0;

foreach ($reponses as $rep) {
    $txt = nettoieTexte($rep);
    $val = 30; // neutre

    foreach ($motsNeg as $w) if (strpos($txt, $w) !== false) { $val = 10; break; }
    foreach ($motsPos as $w) if (strpos($txt, $w) !== false) { $val = 50; break; }

    $traits[$trait] += $val;
    $score += $val;
}

$max = max(1, $nbQ) * 50;
$pourcent = $nbQ > 0 ? round(($traits[$trait] / $max) * 100) : 0;
$pourcent = max(0, min(100, $pourcent));

// --------- Star associée ---------
$stmt = $pdo->prepare("SELECT * FROM stars WHERE categorie = :cat ORDER BY RAND() LIMIT 1");
$stmt->execute(['cat' => $categorieChoisie]);
$star = $stmt->fetch(PDO::FETCH_ASSOC);

$image = 'images/default.jpg';
if (!empty($star['image_path']) && file_exists(__DIR__.'/'.$star['image_path'])) {
    $image = $star['image_path'];
}

// --------- Conseils ---------
$conseils = [
    'confiance' => [
        'low'  => ["Lance-toi des petits défis.", "Prends ton temps et avance étape par étape."],
        'mid'  => ["Ose sortir de ta zone de confort petit à petit.", "Prépare-toi un peu plus à l’avance."],
        'high' => ["Aide quelqu’un d’autre à prendre confiance.", "Tente une prise de parole plus grande."],
    ],
    'creativite' => [
        'low'  => ["Reproduis une œuvre et change un détail.", "Note tout ce qui te passe par la tête sans filtre."],
        'mid'  => ["Teste un nouveau format (audio, vidéo, dessin...).", "Donne-toi un mini-projet express."],
        'high' => ["Partage tes créations et demande des avis.", "Lance un petit défi créatif avec des potes."],
    ],
    'leadership' => [
        'low'  => ["Commence par co-animer au lieu de diriger seul.", "Prépare deux questions ouvertes pour un échange."],
        'mid'  => ["Organise une mini réunion claire et simple.", "Donne un feedback positif concret."],
        'high' => ["Confie une tâche à quelqu’un et fais le suivi.", "Propose une petite initiative au groupe."],
    ],
    'perseverance' => [
        'low'  => ["Commence par 10 minutes sans pression.", "Avance un tout petit peu chaque jour."],
        'mid'  => ["Protège ton rythme, reste régulier.", "Prévois un plan B si tu bloques."],
        'high' => ["Augmente un peu la difficulté.", "Aide quelqu’un à persévérer aussi."],
    ],
];

$niveau = $pourcent < 40 ? 'low' : ($pourcent < 70 ? 'mid' : 'high');
$listeConseils = $conseils[$trait][$niveau] ?? [];
shuffle($listeConseils);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Résultat - <?= htmlspecialchars($categorieChoisie) ?></title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f8;padding:20px;text-align:center;}
.card{background:#fff;padding:16px;margin:14px auto;max-width:720px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.progress-bar{width:100%;background:#e0e0e0;border-radius:20px;overflow:hidden;height:26px;}
.progress{height:26px;background:#4CAF50;color:#fff;line-height:26px;font-weight:bold;}
img{max-width:220px;border-radius:8px;margin:0 auto 10px;display:block;}
ul{text-align:left;max-width:680px;margin:0 auto;}
a.btn{display:inline-block;margin:8px;padding:10px 16px;border-radius:8px;background:#2f76ff;color:#fff;text-decoration:none;font-weight:bold;}
.muted{color:#666;font-size:14px;}
</style>
</head>
<body>

<h1>Ton résultat – <?= htmlspecialchars($categorieChoisie) ?></h1>

<div class="card">
    <?php if ($star): ?>
        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($star['nom'] ?? 'Icône') ?>">
        <h2><?= htmlspecialchars($star['nom'] ?? 'Icône') ?></h2>
        <p class="muted"><?= htmlspecialchars($star['description'] ?? 'Pas de description') ?></p>
        <p><strong>Trait principal :</strong> <?= htmlspecialchars($star['trait_principal'] ?? '-') ?></p>
    <?php else: ?>
        <p>Aucune star trouvée.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Ton trait dominant : <?= ucfirst($trait) ?></h3>
    <div class="progress-bar">
        <div class="progress" style="width:<?= $pourcent ?>%"><?= $pourcent ?>%</div>
    </div>
</div>

<div class="card">
    <h3>Quelques conseils</h3>
    <ul>
        <?php foreach ($listeConseils as $c): ?>
            <li><?= htmlspecialchars($c) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="card">
    <a class="btn" href="journal.php?categorie=<?= urlencode($categorieChoisie) ?>">Voir ton journal 📔</a>
    <a class="btn" href="questionnaire.php?categorie=<?= urlencode($categorieChoisie) ?>">Refaire le test</a>
</div>

</body>
</html>
