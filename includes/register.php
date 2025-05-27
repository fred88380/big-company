<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        // Vérifier si l'email existe déjà
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            echo '<div class="alert alert-danger">Cet email est déjà utilisé.</div>';
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        if ($stmt->execute([$email, $hash])) {
            header('Location: index.html?register=success');
            exit;
        } else {
            echo '<div class="alert alert-danger">Erreur lors de l\'inscription.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
    }
} else {
    http_response_code(405);
    echo "Méthode non autorisée.";
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const eye = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        eye.classList.remove('bi-eye');
        eye.classList.add('bi-eye-slash');
    } else {
        pwd.type = 'password';
        eye.classList.remove('bi-eye-slash');
        eye.classList.add('bi-eye');
    }
});
</script>

<div class="mb-3">
    <label for="password" class="form-label">Mot de passe</label>
    <div class="input-group">
        <input type="password" class="form-control" id="password" name="password" required>
        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <span class="bi bi-eye" id="eyeIcon"></span>
        </button>
    </div>
</div>