<?php 
require_once __DIR__ . '/../../app/bootstrap.php';

// Calcul du nombre total d'articles pour le compteur du menu
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<?php require __DIR__ . '/../../app/Views/pages/ceremonies.php'; ?>



