<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=bigcompany;charset=utf8', 'swades', 'Kylian250510?');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>

