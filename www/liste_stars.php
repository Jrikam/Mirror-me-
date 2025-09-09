<?php
session_start();
require_once 'pdo.php';
require_once 'type_personalites.php';

// inclut ton tableau $types_personnalite

// Récupération de la catégorie si passée dans l'URL
$categorie = $_GET['categorie'] ?? null;

if ($categorie) {
    // Récupère les stars pour la catégorie sélectionnée
    $stmt = $pdo->prepare("SELECT nom, image, description, categorie FROM stars WHERE categorie = ?");
    $stmt->execute([$categorie]);
    $stars = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Récupère toutes les stars
    $stmt = $pdo->query("SELECT nom, image, description, categorie FROM stars");
    $stars = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Supprimer les doublons par nom
$stars_unique = [];
$seen = [];
foreach ($stars as $star) {
    if (!in_array($star['nom'], $seen)) {
        $stars_unique[] = $star;
        $seen[] = $star['nom'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $categorie ? "Stars - " . htmlspecialchars($categorie) : "Toutes les Stars" ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; background-color: #f9f9f9; }
        h1, h2 { margin-bottom: 30px; }
        .stars { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-bottom: 40px; }
        .star { background: #fff; padding: 15px; border-radius: 10px; width: 200px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .star img { width: 100%; border-radius: 10px; }
        .star h3 { margin-top: 10px; }
        a { display: inline-block; margin-top: 30px; padding: 10px 20px; background: #333; color: #fff; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        a:hover { background: #555; }
        .types { margin-top: 30px; max-width: 600px; margin-left: auto; margin-right: auto; text-align: left; }
        .types p { margin: 8px 0; }
    </style>
</head>
<body>
    <h1><?= $categorie ? "Stars - " . htmlspecialchars($categorie) : "Toutes les Stars" ?></h1>

    <div class="stars">
        <?php foreach ($stars_unique as $star): ?>
            <div class="star">
                <img src="images/<?= htmlspecialchars($star['image']); ?>" alt="<?= htmlspecialchars($star['nom']); ?>">
                <h3><?= htmlspecialchars($star['nom']); ?></h3>
                <?php if (!$categorie && isset($star['categorie'])): ?>
                    <p>Type : <?= htmlspecialchars($star['categorie']); ?></p>
                <?php endif; ?>
                <?php if (!empty($star['description'])): ?>
                    <p><?= htmlspecialchars($star['description']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="categories.php">Retour aux types de personnalités</a>

    <h2>Développe tes qualités ou inspire-toi des stars !</h2>
    <div class="types">
        <?php foreach ($stars_unique as $star): 
            $nom = $star['nom'];
            $qualite = null;
            // Cherche la qualité dans ton tableau types_personnalite
            foreach ($types_personnalite as $type => $starsType) {
                if (isset($starsType[$nom])) {
                    $qualite = $starsType[$nom];
                    break;
                }
            }
            if (!$qualite) $qualite = "Personnalité unique";
        ?>
            <p><?= htmlspecialchars($nom); ?> → <strong><?= htmlspecialchars($qualite); ?></strong></p>
        <?php endforeach; ?>
    </div>
</body>
</html>
