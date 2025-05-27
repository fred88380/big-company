<?php
include 'includes/db.php';
include 'includes/employee.php';
include 'includes/service.php';
include 'includes/flash.php';

session_start();

// Gestion de l'ajout ou modification d'un employé
if (isset($_POST["formAddEdit"])) {
    if (!empty($_POST["first_name"]) && !empty($_POST["last_name"]) && !empty($_POST["department"])) {
        if (!empty($_POST["id_employee"])) {
            if (updateEmployee($db, $_POST["id_employee"], $_POST["first_name"], $_POST["last_name"], $_POST["department"])) {
                setFlash("L'employé a été modifié avec succès");
            } else {
                setFlash("Erreur lors de la modification de l'employé", "error");
            }
        } else {
            if (createEmployee($db, $_POST["first_name"], $_POST["last_name"], $_POST["department"])) {
                setFlash("L'employé a été ajouté avec succès");
            } else {
                setFlash("Erreur lors de l'ajout de l'employé", "error");
            }
        }
        header("Location: index.php");
        exit();
    }
}

// Gestion de la suppression d'un employé
if (!empty($_GET["id"]) && !empty($_GET["action"]) && $_GET["action"] == "effacer" && $_GET["id"] > 0) {
    if (deleteEmployee($db, $_GET["id"])) {
        setFlash("L'employé a été supprimé avec succès");
    } else {
        setFlash("Erreur lors de la suppression de l'employé", "error");
    }
    header("Location: index.php");
    exit();
}

// Gestion de l'édition d'un employé
if (!empty($_GET["action"]) && $_GET["action"] == "editer" && !empty($_GET["id"])) {
    $titre = "Editer";
    $employeeData = getEmployeeById($db, $_GET["id"]);
    if (!$employeeData) {
        setFlash("Employé introuvable", "error");
        header("Location: index.php");
        exit();
    }
} else {
    $titre = "Enregistrer";
    $employeeData = [
        "id" => "",
        "first_name" => "",
        "last_name" => "",
        "id_department" => ""
    ];
}

// Place ici la logique d'ajout, édition, suppression, etc.
// Puis récupère les employés et services :
$getDepartments = getAllDepartments($db);
$getEmployees = getAllEmployees($db);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des employés - My Big Company</title>
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
        <?php displayFlash(); ?>
        <div class="row g-4">
            <!-- Formulaire employé -->
            <div class="col-lg-4">
                <div class="dashboard-card p-4 bg-white mb-4">
                    <h4 class="mb-3 text-primary"><?= $titre ?> un employé</h4>
                    <form method="post" action="index.php" novalidate>
                        <input type="hidden" name="id_employee" value="<?= htmlspecialchars($employeeData["id"]) ?>">
                        <div class="mb-3">
                            <label for="department" class="form-label">Service</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Sélectionnez un service</option>
                                <?php foreach ($getDepartments as $department): ?>
                                    <option value="<?= $department["id"] ?>" <?= ($employeeData["id_department"] == $department["id"]) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($department["name"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?= htmlspecialchars($employeeData["first_name"]) ?>" required minlength="2" maxlength="30">
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?= htmlspecialchars($employeeData["last_name"]) ?>" required minlength="2" maxlength="30">
                        </div>
                        <button type="submit" name="formAddEdit" class="btn btn-primary w-100"><?= $titre ?></button>
                    </form>
                </div>
            </div>
            <!-- Liste des employés -->
            <div class="col-lg-8">
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
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $filteredEmployees = $getEmployees;
                            if (!empty($_GET['department_filter'])) {
                                $filteredEmployees = array_filter($getEmployees, function($emp) {
                                    return $emp['id_department'] == $_GET['department_filter'];
                                });
                            }
                            ?>
                            <?php if (count($filteredEmployees)): ?>
                                <?php foreach ($filteredEmployees as $employee): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($employee['employee_id']) ?></td>
                                        <td><?= htmlspecialchars($employee['first_name']) ?></td>
                                        <td><?= htmlspecialchars($employee['last_name']) ?></td>
                                        <td><?= htmlspecialchars($employee['name']) ?></td>
                                        <td class="text-end">
                                            <a href="index.php?action=editer&id=<?= $employee['employee_id'] ?>" class="btn btn-sm btn-outline-primary me-1">Éditer</a>
                                            <a href="index.php?action=effacer&id=<?= $employee['employee_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cet employé ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucun employé trouvé.</td>
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
