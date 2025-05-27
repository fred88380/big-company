<?php
session_start();
require 'includes/db.php';
require 'includes/employee.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

// Récupération des employés
$employees = getAllEmployees($db);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Employés - My Big Company</title>
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
        <div class="dashboard-card p-4 bg-white">
            <h2 class="mb-4 text-primary">Liste des employés</h2>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Service</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($employees)): ?>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td><?= htmlspecialchars($emp['employee_id']) ?></td>
                                    <td><?= htmlspecialchars($emp['first_name']) ?></td>
                                    <td><?= htmlspecialchars($emp['last_name']) ?></td>
                                    <td><?= htmlspecialchars($emp['name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aucun employé trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="dashboard.php" class="btn btn-outline-primary mt-3">Retour au tableau de bord</a>
        </div>
        <footer class="text-center mt-5 mb-2 text-muted small">
            &copy; <?= date('Y') ?> My Big Company
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>