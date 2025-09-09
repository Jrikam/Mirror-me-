<?php
session_start();
require_once 'pdo.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($nom) && !empty($email) && !empty($password)) {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "❌ Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            if ($stmt->execute([$nom, $email, $hash])) {
                $message = "✅ Inscription réussie ! <a href='connexion.php'>Connectez-vous</a>";
            } else {
                $message = "❌ Erreur lors de l'inscription.";
            }
        }
    } else {
        $message = "⚠️ Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - MirrorMe</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <h1>Créer un compte</h1>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <form method="POST">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>

    <p><a href="connexion.php">Déjà un compte ? Connectez-vous</a></p>
</body>
</html>
