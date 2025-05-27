<?php
session_start();
require 'includes/db.php';

// Vérification de la session utilisateur (ou adapte selon ton système)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Vous devez être connecté pour accéder à cette page.";
    exit();
}

$id = $_GET['id'] ?? $_SESSION['user_id'];
$message = '';

// Récupération des infos utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));
    if (!empty($email)) {
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetchColumn() > 0) {
            $message = '<div class="alert alert-danger">Cet email est déjà utilisé.</div>';
        } else {
            $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $id]);
            // Mettre à jour la session si besoin
            if (isset($_SESSION['user'])) {
                $_SESSION['user']['email'] = $email;
            }
            header("Location: users.php?success=email");
            exit();
        }
    } else {
        $message = '<div class="alert alert-danger">Veuillez entrer un email.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier l'email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
    <div class="container mt-5" style="max-width:500px;">
        <h2 class="mb-4 text-primary text-center">Modifier l'email</h2>
        <?= $message ?>
        <form method="post" action="edit_user_email.php?id=<?= $id ?>">
            <div class="mb-3">
                <label class="form-label">Nouvel email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
            <a href="users.php" class="btn btn-outline-secondary w-100 mt-2">Retour</a>
        </form>
    </div>
    <footer class="text-center mt-5 mb-2 text-muted small">
        &copy; <?= date('Y') ?> My Big Company
    </footer>
</body>

</html>