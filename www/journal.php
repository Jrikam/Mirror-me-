<?php
session_start();
require_once __DIR__ . '/pdo.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenu'])) {
    $contenu = trim($_POST['contenu']);
    if ($contenu !== '') {
        // Insérer le journal
        $stmt = $pdo->prepare("INSERT INTO journal_entries (utilisateur_id, contenu, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $contenu]);
        $journal_id = $pdo->lastInsertId();

        // Récupérer 3 objectifs aléatoires
        $objectifs_modele = $pdo->query("SELECT id, conseil FROM objectifs ORDER BY RAND() LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($objectifs_modele as $obj) {
            $pdo->prepare("INSERT INTO objectifs_journal (journal_id, titre) VALUES (?, ?)")
                ->execute([$journal_id, $obj['conseil']]);
            $objectif_journal_id = $pdo->lastInsertId();

            // Copier 3 tips dans tips_journal
            $tips = $pdo->prepare("SELECT tip FROM tips WHERE objectif_id = ? LIMIT 3");
            $tips->execute([$obj['id']]);
            foreach ($tips->fetchAll(PDO::FETCH_COLUMN) as $tip) {
                $pdo->prepare("INSERT INTO tips_journal (objectif_journal_id, tip) VALUES (?, ?)")
                    ->execute([$objectif_journal_id, $tip]);
            }
        }

        header('Location: objectifs.php?journal_id=' . $journal_id);
        exit;
    }
}

// Récupérer les journaux existants
$journaux = $pdo->prepare("SELECT id, contenu, created_at FROM journal_entries WHERE utilisateur_id = ? ORDER BY created_at DESC");
$journaux->execute([$user_id]);
$journaux = $journaux->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mon Journal</title>
<style>
body { font-family: Arial, sans-serif; padding: 40px; background: #f9f9f9; }
h1 { text-align: center; }
form { margin-bottom: 20px; }
textarea { width: 100%; height: 100px; padding: 10px; }
button { margin-top: 10px; padding: 10px 20px; border: none; background: #333; color: #fff; border-radius: 5px; cursor: pointer; }
button:hover { background: #555; }
.journal { margin-top: 30px; }
.entry { background: #fff; padding: 10px; border-radius: 5px; margin-bottom: 10px; border: 1px solid #ccc; display:flex; justify-content:space-between; align-items:center;}
.date { font-size: 0.9em; color: #666; }
.entry a { text-decoration:none; color:#333; font-weight:bold; }
.entry form { margin:0; }
.entry button { background:#c0392b; padding:5px 10px; font-size:0.9em; }
.entry button:hover { background:#e74c3c; }
</style>
</head>
<body>
<h1>📖 Mon Journal</h1>

<form method="post">
    <textarea name="contenu" placeholder="Écris ton journal ici..."></textarea>
    <br>
    <button type="submit">Enregistrer et voir mes objectifs 🎯</button>
</form>

<div class="journal">
    <h2>Mes anciens journaux</h2>
    <?php foreach ($journaux as $j): ?>
        <div class="entry">
            <a href="objectifs.php?journal_id=<?= $j['id'] ?>">
                <?= htmlspecialchars($j['contenu']) ?> <span class="date">(<?= date('d/m/Y H:i', strtotime($j['created_at'])) ?>)</span>
            </a>
            <form method="post" action="supprimer_journal.php" onsubmit="return confirm('Supprimer ce journal ?');">
                <input type="hidden" name="journal_id" value="<?= $j['id'] ?>">
                <button type="submit">Supprimer</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
