<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - My Big Company</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6fb; }
        .company-title { color: #0d6efd; font-weight: bold; letter-spacing: 2px; }
        .dashboard-card { box-shadow: 0 2px 16px rgba(0,0,0,0.07); border: none; border-radius: 16px; }
        .dashboard-btn { min-width: 220px; font-size: 1.1rem; }
        .welcome { font-size: 2rem; font-weight: bold; color: #0d6efd; }
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="dashboard-card p-5 text-center bg-white">
                    <div class="welcome mb-4">Bienvenue sur le tableau de bord</div>
                    <div class="mb-4 text-muted">Gérez facilement vos employés, services et utilisateurs.</div>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mb-4">
                        <a href="index.php" class="btn btn-primary dashboard-btn">👨‍💼 Gérer les employés</a>
                        <a href="services.php" class="btn btn-success dashboard-btn">🏢 Gérer les services</a>
                        <a href="users.php" class="btn btn-info dashboard-btn text-white">👥 Gérer les utilisateurs</a>
                    </div>
                    <a href="logout.php" class="btn btn-outline-danger">Se déconnecter</a>
                </div>
            </div>
        </div>
        <footer class="text-center mt-5 mb-2 text-muted small">
            &copy; <?= date('Y') ?> My Big Company
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
