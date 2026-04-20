<?php
/**
 * payment-success.php
 * 
 * Page de confirmation après paiement réussi
 * Affiche les détails de la commande
 */

require_once __DIR__ . '/../../../app/bootstrap.php';

$orderConfirmationController = new OrderConfirmationController();
$data = $orderConfirmationController->view();
extract($data, EXTR_SKIP);
?>
<?php require __DIR__ . '/../../../app/Views/pages/payment-success.php'; ?>





