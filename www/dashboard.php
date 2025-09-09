<?php
session_start();
require_once 'pdo.php';

// --- Sauvegarde du questionnaire ---
if (isset($_POST['submit_questionnaire'])) {
    $stmt = $pdo->prepare("
        INSERT INTO questionnaire (utilisateur_id, star_preferee, reponse1, reponse2, reponse3)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['star'],
        $_POST['q1'],
        $_POST['q2'],
        $_POST['q3']
    ]);
}

// --- Sauvegarde du journal ---
if (isset($_POST['submit_journal'])) {
    $objectif = isset($_POST['objectif']) ? 1 : 0;
    $stmt = $pdo->prepare("
        INSERT INTO journal (utilisateur_id, reflexion, action_du_jour, objectif)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['reflexion'],
        $_POST['action'],
        $objectif
    ]);
}

// --- Calcul de la progression ---
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total, SUM(objectif) as atteints
    FROM journal
    WHERE utilisateur_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$progress = $stmt->fetch();
$pourcentage = ($progress['total'] > 0) ? round(($progress['atteints'] / $progress['total']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace Coaching</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
            margin-bottom: 40px;
        }
        section {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            background-color: #fff;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        textarea, input[type="text"] {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            resize: vertical;
        }
        button {
            padding: 10px 20px;
            border: none;
            background-color: #333;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #555;
        }
        .progress-bar {
            width: 100%;
            background: #ddd;
            border-radius: 10px;
            overflow: hidden;
            height: 25px;
            margin-top: 10px;
        }
        .progress {
            height: 100%;
            background: #4caf50;
            width: <?= $pourcentage ?>%;
            text-align: center;
            color: #fff;
            line-height: 25px;
            transition: width 0.3s;
        }
    </style>
</head>
<body>

    <h1>Bienvenue dans ton espace coaching ✨</h1>

    <!-- Questionnaire -->
    <section>
        <h2>Questionnaire personnalisé</h2>
        <form method="POST">
            <label>Quelle star t'inspire le plus ?</label>
            <input type="text" name="star" required>

            <label>Question 1 :</label>
            <textarea name="q1"></textarea>

            <label>Question 2 :</label>
            <textarea name="q2"></textarea>

            <label>Question 3 :</label>
            <textarea name="q3"></textarea>

            <button type="submit" name="submit_questionnaire">Valider</button>
        </form>
    </section>

    <!-- Journal -->
    <section>
        <h2>Mon journal du jour</h2>
        <form method="POST">
            <label>Mes réflexions :</label>
            <textarea name="reflexion"></textarea>

            <label>Mon action du jour :</label>
            <textarea name="action"></textarea>

            <label>
                <input type="checkbox" name="objectif"> Objectif atteint ✅
            </label>

            <button type="submit" name="submit_journal">Enregistrer</button>
        </form>
    </section>

    <!-- Progression -->
    <section>
        <h2>Ma progression</h2>
        <div class="progress-bar">
            <div class="progress"><?= $pourcentage ?>%</div>
        </div>
    </section>

</body>
</html>
