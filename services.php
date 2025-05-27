<?php
session_start();
require 'includes/db.php';
require 'includes/service.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

// Gestion ajout/modification d'un service
$message = '';
if (isset($_POST['formAddEdit'])) {
    $name = trim($_POST['name'] ?? '');
    $id = $_POST['id_department'] ?? '';

    if ($name) {
        if ($id) {
            if (updateDepartment($db, $id, $name)) {
                $message = '<div class="alert alert-success">Service modifié avec succès.</div>';
            } else {
                $message = '<div class="alert alert-danger">Erreur lors de la modification.</div>';
            }
        } else {
            if (createDepartment($db, $name)) {
                $message = '<div class="alert alert-success">Service ajouté avec succès.</div>';
            } else {
                $message = '<div class="alert alert-danger">Erreur lors de l\'ajout.</div>';
            }
        }
    }
}

// Gestion suppression
if (!empty($_GET["id"]) && !empty($_GET["action"]) && $_GET["action"] == "effacer" && $_GET["id"] > 0) {
    if (deleteDepartment($db, $_GET["id"])) {
        $message = '<div class="alert alert-success">Service supprimé avec succès.</div>';
    } else {
        $message = '<div class="alert alert-danger">Impossible de supprimer ce service (employés rattachés ?).</div>';
    }
}

// Gestion édition
if (!empty($_GET["action"]) && $_GET["action"] == "editer" && !empty($_GET["id"])) {
    $titre = "Éditer";
    $departmentData = null;
    foreach (getAllDepartments($db) as $dep) {
        if ($dep['id'] == $_GET["id"]) {
            $departmentData = $dep;
            break;
        }
    }
    if (!$departmentData) {
        $message = '<div class="alert alert-danger">Service introuvable.</div>';
        $titre = "Ajouter";
        $departmentData = ["id" => "", "name" => ""];
    }
} else {
    $titre = "Ajouter";
    $departmentData = ["id" => "", "name" => ""];
}

$departments = getAllDepartments($db);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Services - My Big Company</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <?= $message ?>
        <div class="row g-4">
            <!-- Formulaire service -->
            <div class="col-lg-4">
                <div class="dashboard-card p-4 bg-white mb-4">
                    <h4 class="mb-3 text-primary"><?= $titre ?> un service</h4>
                    <form method="post" action="services.php" novalidate>
                        <input type="hidden" name="id_department" value="<?= htmlspecialchars($departmentData["id"]) ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du service</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= htmlspecialchars($departmentData["name"]) ?>" required minlength="2" maxlength="50">
                        </div>
                        <button type="submit" name="formAddEdit" class="btn btn-primary w-100"><?= $titre ?></button>
                    </form>
                </div>
            </div>
            <!-- Liste des services -->
            <div class="col-lg-8">
                <div class="dashboard-card p-4 bg-white">
                    <h2 class="mb-4 text-primary">Liste des services</h2>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom du service</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (count($departments)): ?>
                                <?php foreach ($departments as $dep): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($dep['id']) ?></td>
                                        <td><?= htmlspecialchars($dep['name']) ?></td>
                                        <td class="text-end">
                                            <a href="services.php?action=editer&id=<?= $dep['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Éditer</a>
                                            <a href="services.php?action=effacer&id=<?= $dep['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce service ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun service trouvé.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
