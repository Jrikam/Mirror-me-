<?php
try {
    $pdo = new PDO(
        'mysql:host=db_mirrorme;port=3306;dbname=mirrorme_db;charset=utf8',
        'mirrorme_user',
        'secretpass'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie !"; // <- à supprimer si tu fais une redirection
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
