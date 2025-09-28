<?php
session_start();
require_once __DIR__ . '/pdo.php';

// Vérifier la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenu'])) {
    $contenu = trim($_POST['contenu']);

    if ($contenu !== '') {
        $stmt = $pdo->prepare("INSERT INTO journal_entries (utilisateur_id, contenu, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $contenu]);

        // ✅ Une fois enregistré, on redirige direct vers les objectifs
        header('Location: objectifs.php');
        exit;
    }
}

// Récupérer les anciens journaux
$stmt = $pdo->prepare("SELECT contenu, created_at FROM journal_entries WHERE utilisateur_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$journaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        .entry { background: #fff; padding: 10px; border-radius: 5px; margin-bottom: 10px; border: 1px solid #ccc; }
        .date { font-size: 0.9em; color: #666; }
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
                <p><?= htmlspecialchars($j['contenu']) ?></p>
                <div class="date">✍️ <?= date('d/m/Y H:i', strtotime($j['created_at'])) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
