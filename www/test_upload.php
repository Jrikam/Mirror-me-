<?php
$uploadDir = __DIR__ . '/uploads';

echo "Chemin : $uploadDir<br>";
echo "Existe ? " . (is_dir($uploadDir) ? 'oui' : 'non') . "<br>";
echo "Writable ? " . (is_writable($uploadDir) ? 'oui' : 'non') . "<br>";
echo "Utilisateur PHP : " . exec('whoami') . "<br>";

$testFile = $uploadDir . '/test.txt';

