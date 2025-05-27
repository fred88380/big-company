<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

// Traitement de l'ajout d'utilisateur
$add_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    // Compter le nombre d'utilisateurs existants
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    $nbUsers = $stmt->fetchColumn();

    if ($nbUsers >= 5) {
        $add_message = '<div class="alert alert-danger">Nombre maximum d\'utilisateurs atteint (5).</div>';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email && $password) {
            // Vérifier si l'email existe déjà
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $add_message = '<div class="alert alert-danger">Cet email est déjà utilisé.</div>';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                if ($stmt->execute([$email, $hash])) {
                    $add_message = '<div class="alert alert-success">Utilisateur ajouté avec succès.</div>';
                } else {
                    $add_message = '<div class="alert alert-danger">Erreur lors de l\'ajout.</div>';
                }
            }
        } else {
            $add_message = '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
        }
    }
}

// Récupération des utilisateurs
try {
    $stmt = $db->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (!empty($password)) {
        // ... mise à jour ...
    } else {
        $message = '<div class="alert alert-danger">Veuillez entrer un mot de passe.</div>';
    }
}

$success = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'email') {
        $success = '<div class="alert alert-success">Email modifié avec succès.</div>';
    } elseif ($_GET['success'] === 'password') {
        $success = '<div class="alert alert-success">Mot de passe modifié avec succès.</div>';
    }
}

$currentUserId = $_SESSION['user_id']; // Ajout de cette ligne pour récupérer l'ID de l'utilisateur courant
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs - My Big Company</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6fb; }
        .company-title { color: #0d6efd; font-weight: bold; letter-spacing: 2px; }
        .dashboard-card { box-shadow: 0 2px 16px rgba(0,0,0,0.07); border: none; border-radius: 16px; }
        .table thead { background: #0d6efd; color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand company-title d-flex align-items-center" href="#">
                <i class="bi bi-building me-2"></i> My Big Company
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="index.php">Employés</a>
                <a class="nav-link" href="services.php">Services</a>
                <a class="nav-link" href="users.php">Utilisateurs</a>
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <?= $success ?>
        <div class="dashboard-card p-4 bg-white">
            <h2 class="mb-4 text-primary">Liste des utilisateurs</h2>
            <?= $add_message ?>
            <form method="post" class="row g-2 mb-4">
                <input type="hidden" name="add_user" value="1">
                <div class="col-md-5">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="password" name="password" id="addUserPassword" class="form-control" placeholder="Mot de passe" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <span class="bi bi-eye" id="eyeIcon"></span>
                        </button>
                    </div>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="text-end">
                                        <a href="edit_user_email.php?id=<?= urlencode($user['id']) ?>" class="btn btn-sm btn-outline-primary me-1">Modifier</a>
                                        <a href="edit_user_password.php?id=<?= urlencode($user['id']) ?>" class="btn btn-sm btn-outline-secondary me-1">Changer mot de passe</a>
                                        <a href="delete_users.php?id=<?= urlencode($user['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aucun utilisateur trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="dashboard.php" class="btn btn-outline-primary mt-3">Retour au tableau de bord</a>
        </div>
        <?php if ($currentUserId == 1): ?>
            <a href="admin.php" class="btn btn-warning mb-3">
                Accès admin 
            </a>
        <?php endif; ?>
        <footer class="text-center mt-5 mb-2 text-muted small">
            &copy; <?= date('Y') ?> My Big Company
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwd = document.getElementById('addUserPassword');
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
</body>
</html>
