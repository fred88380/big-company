<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$id = $_GET['id'] ?? $_SESSION['user_id'];

// Récupération des données actuelles
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}

$message = '';
$current_email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $new_nom = $_POST['nom'] ?? $user['nom'];
    $new_prenom = $_POST['prenom'] ?? $user['prenom'];

    $updates = [];
    $params = [];
    $has_error = false;

    // Vérification email uniquement si changement
    if ($new_email !== $current_email) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$new_email, $id]);
        if ($stmt->fetch()) {
            $message .= '<div class="alert alert-danger">Cet email est déjà utilisé par un autre compte.</div>';
            $has_error = true;
        } else {
            $updates[] = "email = ?";
            $params[] = $new_email;
        }
    }

    // Vérification mot de passe
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $message .= '<div class="alert alert-danger">Le mot de passe doit contenir au moins 8 caractères.</div>';
            $has_error = true;
        } else {
            $updates[] = "password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    // Mise à jour nom/prénom
    if ($new_nom !== $user['nom']) {
        $updates[] = "nom = ?";
        $params[] = $new_nom;
    }

    if ($new_prenom !== $user['prenom']) {
        $updates[] = "prenom = ?";
        $params[] = $new_prenom;
    }

    // Exécution des modifications si pas d'erreur
    if (!$has_error && !empty($updates)) {
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $db->prepare($sql);

        if ($stmt->execute($params)) {
            $message .= '<div class="alert alert-success">Profil mis à jour avec succès.</div>';
            // Rafraîchir les données
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $current_email = $user['email'];
        } else {
            $message .= '<div class="alert alert-danger">Erreur lors de la mise à jour de la base de données.</div>';
        }
    } elseif (!$has_error) {
        $message = '<div class="alert alert-info">Aucune modification détectée.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier le profil - My Big Company</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: #f8f9fa;
            padding-top: 20px;
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-save {
            min-width: 120px;
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

    <div class="profile-container">
        <div class="profile-card">
            <h2 class="text-center mb-4">Modifier le profil</h2>

            <?php echo $message; ?>

            <form method="post" class="mt-4">
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom"
                            value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom"
                            value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?= htmlspecialchars($current_email) ?>" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Laisser vide pour ne pas changer">
                    <div class="form-text">Minimum 8 caractères</div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="users.php" class="btn btn-outline-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary btn-save">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="text-center mt-5 mb-2 text-muted small">
        &copy; <?= date('Y') ?> My Big Company
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation côté client
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password && password.length < 8) {
                alert('Le mot de passe doit contenir au moins 8 caractères');
                e.preventDefault();
            }
        });
    </script>
</body>

</html>