<?php
session_start();
require_once 'pdo.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nom && $email && $password) {
        // Vérifie si l'email existe déjà
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "❌ Cet email est déjà pris.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            
            if ($insert->execute([$nom, $email, $hash])) {
                $message = "✅ Inscription réussie ! <a href='connexion.php'>Se connecter</a>";
            } else {
                $message = "⚠️ Une erreur est survenue, réessaie.";
            }
        }
    } else {
        $message = "⚠️ Merci de remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Mirror Me</title>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
</head>
<body>
    <h1>Créer ton compte</h1>

    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="nom" placeholder="Ton nom" required>
        <input type="email" name="email" placeholder="Ton email" required>
        <input type="password" name="password" placeholder="Ton mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>

    <p><a href="connexion.php">Déjà inscrit ? Clique ici</a></p>
</body>
</html>
