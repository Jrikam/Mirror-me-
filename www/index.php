<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mirror Me - Bienvenue</title>
    <link rel="stylesheet" href="/style.css?v=<?php echo time(); ?>">

<body>
    <header>
        <h1>Mirror Me</h1>
        <nav>
            <a href="connexion.php">Se connecter</a>
            <a href="inscription.php">Créer un compte</a>
        </nav>
    </header>

    <main>
        <section class="intro">
            <h2>Bienvenue sur Mirror Me</h2>
            <p>Une application pour te découvrir, t'inspirer et atteindre ton plein potentiel.</p>
            <p>🌟 Explore les grandes personnalités auxquelles tu ressembles et développe leurs qualités.</p>
            <a href="presentation.php" class="btn">Découvrir l'application</a>
<section class="highlight">
            <p class="question">✨ Qui te reflète vraiment ?</p>
        </section>
    </main>

   
    <footer>
        <p>&copy; 2025 Mirror Me - Tous droits réservés</p>
    </footer>
</body>
</html>
