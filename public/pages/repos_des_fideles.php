<?php
require_once __DIR__ . '/../../app/bootstrap.php';

// Calcul du compteur panier
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<?php require __DIR__ . '/../../app/Views/pages/repos_des_fideles.php'; ?>



