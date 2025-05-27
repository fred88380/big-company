<?php
require 'includes/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    http_response_code(403);
    echo "Accès refusé.";
    exit();
}

// Récupérer tous les utilisateurs
$stmt = $db->query("SELECT id, email, password FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Admin - Tous les utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f6fb;
        }

        .company-title {
            color: #0d6efd;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .dashboard-card {
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
            border: none;
            border-radius: 16px;
        }
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
    <div class="container mt-5">
        <h2 class="mb-4 text-primary">Tous les utilisateurs</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Mot de passe hashé</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td style="font-family:monospace;"><?= htmlspecialchars($user['password']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Retour</a>
    </div>
    <footer class="text-center mt-5 mb-2 text-muted small">
        &copy; <?= date('Y') ?> My Big Company
    </footer>
</body>

</html>