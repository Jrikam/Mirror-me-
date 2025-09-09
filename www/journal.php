
<?php
session_start();
require_once 'pdo.php';

$user_id = $_SESSION['user_id'] ?? 1;

// -------------------- Vérification uploads --------------------
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true); // crée le dossier si inexistant
}
if (!is_writable($uploadDir)) {
    chmod($uploadDir, 0775); // met les droits en écriture
}

// -------------------------------------------------------------

// Supprimer un journal
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT audio_path FROM journal_entries WHERE id=? AND utilisateur_id=?");
    $stmt->execute([$id, $user_id]);
    $audio = $stmt->fetchColumn();
    if ($audio && file_exists(__DIR__ . '/' . $audio)) {
        @unlink(__DIR__ . '/' . $audio);
    }

    $stmt = $pdo->prepare("DELETE FROM journal_entries WHERE id=? AND utilisateur_id=?");
    $stmt->execute([$id, $user_id]);
    header("Location: journal.php");
    exit;
}

// Enregistrer un journal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu'] ?? '');
    $audioFile = null;

    if (!empty($_POST['audio'])) {
        $data = preg_replace('#^data:audio/\w+;base64,#i', '', $_POST['audio']);
        $audioData = base64_decode($data);

        $audioFilename = 'audio_' . uniqid() . '.mp3';
        $audioFilePath = $uploadDir . '/' . $audioFilename;  // chemin absolu pour PHP
        $audioFile = 'uploads/' . $audioFilename;            // chemin relatif pour BDD

        if (file_put_contents($audioFilePath, $audioData) === false) {
            die('Erreur : impossible d\'enregistrer le fichier audio.');
        }
    }

    if ($contenu || $audioFile) {
        $stmt = $pdo->prepare(
            "INSERT INTO journal_entries (utilisateur_id, contenu, audio_path, created_at) 
             VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$user_id, $contenu, $audioFile]);
    }

    header("Location: journal.php");
    exit;
}

// Récupérer les derniers journaux
$stmt = $pdo->prepare("SELECT * FROM journal_entries WHERE utilisateur_id=? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$journal_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Journal Mirror Me</title>
<style>
body{font-family:Arial;padding:30px;background:#f9f9f9;}
textarea{width:100%;height:80px;padding:10px;border-radius:5px;border:1px solid #ccc;resize:vertical;}
button{padding:10px 20px;margin:5px;border:none;border-radius:5px;cursor:pointer;background:#333;color:#fff;}
button:hover{background:#555;}
.journal{margin-top:30px;}
.entry{background:#fff;border:1px solid #ccc;border-radius:5px;padding:15px;margin-bottom:15px;}
.controls{margin-top:10px;}
a{color:#00bcd4;text-decoration:none;}
a:hover{text-decoration:underline;}
audio{display:block;margin-top:10px;}
</style>
</head>
<body>
<h1>📝 Mon Journal Mirror Me</h1>

<form method="post">
<textarea name="contenu" placeholder="Écris ton journal ici..."></textarea><br><br>
<button type="button" id="startRecord">🎙️ Démarrer l'enregistrement</button>
<button type="button" id="stopRecord" disabled>⏹️ Stop</button>
<button type="button" id="resetRecord" disabled>🗑️ Supprimer audio</button>
<audio id="player" controls></audio>
<input type="hidden" name="audio" id="audioInput"><br><br>
<button type="submit">Enregistrer</button>
</form>

<div class="journal">
<?php foreach($journal_entries as $entry): ?>
<div class="entry">
<p><strong><?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?></strong></p>
<?php if($entry['contenu']): ?><p><?= nl2br(htmlspecialchars($entry['contenu'])) ?></p><?php endif; ?>
<?php if($entry['audio_path']): ?>
<audio controls><source src="<?= htmlspecialchars($entry['audio_path']) ?>" type="audio/mpeg"></audio>
<?php endif; ?>
<div class="controls"><a href="?delete=<?= $entry['id'] ?>" onclick="return confirm('Supprimer ce journal ?')">🗑️ Supprimer</a></div>
</div>
<?php endforeach; ?>
</div>

<script>
let mediaRecorder,audioChunks=[];
const startBtn=document.getElementById('startRecord');
const stopBtn=document.getElementById('stopRecord');
const resetBtn=document.getElementById('resetRecord');
const player=document.getElementById('player');
const audioInput=document.getElementById('audioInput');

startBtn.onclick=async ()=>{
    const stream=await navigator.mediaDevices.getUserMedia({audio:true});
    mediaRecorder=new MediaRecorder(stream);
    audioChunks=[];
    mediaRecorder.ondataavailable=e=>{if(e.data.size>0) audioChunks.push(e.data)};
    mediaRecorder.onstop=()=>{
        const blob=new Blob(audioChunks,{type:'audio/mpeg'});
        player.src=URL.createObjectURL(blob);
        const reader=new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend=()=>{audioInput.value=reader.result;resetBtn.disabled=false;};
    };
    mediaRecorder.start();
    startBtn.disabled=true;
    stopBtn.disabled=false;
};

stopBtn.onclick=()=>{mediaRecorder.stop();startBtn.disabled=false;stopBtn.disabled=true;};
resetBtn.onclick=()=>{audioInput.value='';player.src='';resetBtn.disabled=true;};
</script>
</body>
</html>
