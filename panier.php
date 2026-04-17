<?php
require_once __DIR__ . '/app/bootstrap.php';

$cartController = new CartController(new CartModel($pdo));
$cartData = $cartController->index();
extract($cartData, EXTR_SKIP);
?>
<?php require __DIR__ . '/app/Views/pages/panier.php'; ?>

