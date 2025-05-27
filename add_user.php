<?php
require_once 'db.php';

$email = 'test@example.com';
$password = password_hash('testdb', PASSWORD_DEFAULT);

// Attention : dans ton db.php, la variable s'appelle $db, pas $pdo
$stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->execute([$email, $password]);

echo "Utilisateur ajouté !";