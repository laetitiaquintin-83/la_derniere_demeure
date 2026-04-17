<?php
require_once __DIR__ . '/../app/bootstrap.php';

if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$produits = (new AdminDashboardModel($pdo))->getProductsForInventory();
?>
<?php require __DIR__ . '/../app/Views/pages/gestion.php'; ?>


