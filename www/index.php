<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mirror Me - Bienvenue</title>
    <link rel="stylesheet" href="/style.css?v=<?= time(); ?>">
</head>
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
            <h2>Salut ! Bienvenue sur Mirror Me</h2>
            <p>Ici tu peux apprendre à mieux te connaître et avancer à ton rythme.</p>
            <p>Tu pourras t’inspirer de personnalités et bosser sur tes points forts.</p>
            <a href="presentation.php" class="btn">Découvrir l'appli</a>
        </section>

        <section class="highlight">
            <p class="question">Et toi, qui est ton modèle ?</p>
        </section>
    </main>
    
    <footer>
        <p>© 2025 Mirror Me - Tous droits réservés</p>
    </footer>
</body>
</html>
