<?php
session_start();
require_once 'pdo.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pwd = $_POST['password'];

    if ($email && $pwd) {
        $stmt = $pdo->prepare("SELECT id, nom, mot_de_passe FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pwd, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            header("Location: index.php");
            exit;
        } else {
            $msg = "❌ Email ou mot de passe incorrect.";
        }
    } else {
        $msg = "⚠️ Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - MirrorMe</title>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
</head>
<body>
    <h1>Se connecter</h1>

    <?php if ($msg) echo "<p class='message'>$msg</p>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button>Se connecter</button>
    </form>

    <p>Pas encore inscrit ? <a href="inscription.php">Créer un compte</a></p>
</body>
</html>
