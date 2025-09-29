<?php
session_start();
require_once 'pdo.php';

// Définition des qualités par catégorie et par star
$types_personnalite = [

    'Sport' => [
        'Serena Williams' => 'Force et persévérance',
        'Simone Biles' => 'Agilité et détermination',
        'Lionel Messi' => 'Travail en équipe',
        'Cristiano Ronaldo' => 'Détermination',
        'Kylian Mbappe' => 'Vitesse et agilité',
        'Samuel Eto\'o' => 'Performance et leadership',
        'Lewis Hamilton' => 'Concentration et endurance',
        'Usain Bolt' => 'Rapidité et constance',
        'LeBron James' => 'Excellence et leadership',
        'Zinedine Zidane' => 'Technique et calme',
        'Jesse Owens' => 'Détermination et courage',
        'Amanda Serrano' => 'Persévérance',
        'Claressa Shields' => 'Force et ténacité',
        'Caster Semenya' => 'Endurance et force',
        'Naomi Osaka' => 'Résilience et concentration',
        'Muhammad Ali' => 'Charisme et confiance',
        'Eva Longoria' => 'Personnalité unique',
    ],

    'Cinema' => [
        'Alexandra Lamy' => 'Charisme et authenticité',
        'Lucie Lucas' => 'Performance et engagement',
        'Taraji P. Henson' => 'Force et inspiration',
        'Anne Hathaway' => 'Talent et persévérance',
        'Zendaya' => 'Créativité et confiance',
        'Viola Davis' => 'Force et inspiration',
        'Jennifer Aniston' => 'Leadership et influence',
        'Lisa Kudrow' => 'Créativité',
        'Courteney Cox' => 'Persévérance',
        'Sasha Pieterse' => 'Charisme',
        'Damon Idris' => 'Leadership',
        'Brad Pitt' => 'Influence et inspiration',
        'Channing Tatum' => 'Motivation et discipline',
        'Matt Le Blanc' => 'Persévérance',
        'Marlon Wayans' => 'Créativité',
        'Omar Sy' => 'Charisme et influence',
        'Sydney Poitier' => 'Leadership et inspiration',
    ],

    'Litterature' => [
        'Albert Camus' => 'Réflexion et courage',
        'Amanda Gorman' => 'Personnalité unique',
        'Angela Davis' => 'Engagement et force',
        'Assa Traore' => 'Courage et persévérance',
        'Chimamanda Ngozi' => 'Créativité et engagement',
        'Emile Zola' => 'Personnalité unique',
        'George Orwell' => 'Réflexion et vision',
        'Greta Thunberg' => 'Personnalité unique',
        'Leila Slimani' => 'Personnalité unique',
        'Malcolm X' => 'Leadership et combat',
        'Martin Luther King' => 'Leadership et inspiration',
        'Maya Angelou' => 'Inspiration et engagement',
        'Simone de Beauvoir' => 'Leadership et réflexion',
        'Ruby Bridges' => 'Courage et détermination',
        'Victor Hugo' => 'Personnalité unique',
        'Virginie Despentes' => 'Audace et créativité',
    ],

    'Musique' => [
        'Adele' => 'Sincérité et émotion',
        'Alicia Keys' => 'Sérénité et authenticité',
        'Amel Bent' => 'Résilience et authenticité',
        'Ariana Grande' => 'Persévérance et énergie',
        'Aya Nakamura' => 'Originalité et charisme',
        'Beyonce' => 'Confiance et puissance',
        'Britney Spears' => 'Popularité et persévérance',
        'Bruno Mars' => 'Charisme et performance',
        'Dadju' => 'Musicalité et charisme',
        'Ed Sheeran' => 'Créativité et sensibilité',
        'John Legend' => 'Charisme et engagement social',
        'Kendrick Lamar' => 'Réflexion sociale et créativité',
        'Mariah Carey' => 'Performance et excellence',
        'Nicki Minaj' => 'Confiance et audace',
        'Rihanna' => 'Créativité et indépendance',
        'Shakira' => 'Énergie et créativité',
        'Tal' => 'Motivation et persévérance',
        'Taylor Swift' => 'Créativité et expression personnelle',
        'Usher' => 'Discipline et talent',
    ],

];
// Récupère toutes les stars depuis la DB
$stmt = $pdo->query("SELECT DISTINCT nom, categorie FROM stars ORDER BY categorie, nom ASC");
$stars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Toutes les Stars</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f5f8; padding: 40px; color: #333; }
h1 { text-align: center; margin-bottom: 50px; }
h2 { margin-top: 40px; border-bottom: 2px solid #ccc; padding-bottom: 10px; color: #2f76ff; }
.star-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
.star { background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
.star:hover { transform: translateY(-5px); }
.star p { margin: 5px 0; }
</style>
</head>
<body>

<h1>Toutes les Stars</h1>

<?php
$currentCat = '';
foreach ($stars as $star) {
    $nom = $star['nom'];
    $categorie = $star['categorie'];
    $qualite = $types_personnalite[$categorie][$nom] ?? "Personnalité unique";

    // Affiche le titre de la catégorie si changement
    if ($categorie !== $currentCat) {
        if ($currentCat !== '') echo "</div>"; // ferme le div précédent
        echo "<h2>" . htmlspecialchars($categorie) . "</h2>";
        echo "<div class='star-list'>";
        $currentCat = $categorie;
    }
    echo "<div class='star'>";
    echo "<p><strong>" . htmlspecialchars($nom) . "</strong></p>";
    echo "<p>→ " . htmlspecialchars($qualite) . "</p>";
    echo "</div>";
}
echo "</div>";
?>

</body>
</html>
