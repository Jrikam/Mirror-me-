<?php  
try {  
    $pdo = new PDO(  
        'mysql:host=db_mirrorme;port=3310;dbname=mirrorme_db;charset=utf8',  
        'mirrorme_user',  
        'secretpass'  
    );  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
} catch (PDOException $e) {  
    die("Erreur de connexion : " . $e->getMessage());  
}
