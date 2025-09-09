<?php
session_start();
require_once 'pdo.php';

// On peut utiliser l'utilisateur connecté pour personnaliser l'affichage
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Présentation - MirrorMe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur MirrorMe</h1>
        <?php if ($user): ?>
            <p>Bonjour, <?= htmlspecialchars($user['nom']) ?> 👋</p>
        <?php else: ?>
            <p><a href="connexion.php">Se connecter</a> | <a href="inscription.php">S'inscrire</a></p>
        <?php endif; ?>
    </header>

    <main>
        <section>
            <h2>✨ Qu’est-ce que MirrorMe ?</h2>
            <p>MirrorMe est une plateforme interactive qui t'aide à t’inspirer de figures emblématiques (stars, artistes, sportifs, etc.) et à progresser en te fixant des objectifs adaptés à ta personnalité.</p>
        </section>

        <section>
            <h2>💡 Fonctionnalités principales</h2>
            <ul>
                <li>Crée un profil et découvre ta personnalité via un test psychologique</li>
                <li>Découvre les stars qui te ressemblent</li>
                <li>Fixe-toi des objectifs pour évoluer comme tes modèles</li>
                <li>Accède à des conseils et des suivis personnalisés</li>
            </ul>
        </section>

        <section>
            <h2>🚀 Commence dès maintenant !</h2>
            <p><a href="questionnaire.php" class="btn">Passer le test</a> ou <a href="liste_stars.php" class="btn">Découvrir les stars</a></p>
        </section>
    </main>

    <footer>
        <p>© 2025 MirrorMe - Tous droits réservés</p>
    </footer>
</body>
</html>wwp
