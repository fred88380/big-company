<?php
session_start();
require 'db.php';

// Vérification des données
if (empty($_POST['email']) || empty($_POST['password'])) {
    header("Location: index.html?error=1");
    exit;
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if ($user && password_verify($_POST['password'], $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    header("Location: dashboard.php");
} else {
    header("Location: index.html?error=2");
}