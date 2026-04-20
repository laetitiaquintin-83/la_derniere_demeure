<?php
/**
 * payment-form.php
 * 
 * Formulaire de Paiement Sécurisé avec Stripe Elements
 * 
 * Les données sensibles (n° carte, CVV) ne sont JAMAIS stockées sur ce serveur
 * Stripe s'occupe de tout via son formulaire tokenisé
 */

require_once __DIR__ . '/../../../app/bootstrap.php';

$paymentFormController = new PaymentFormController();
$data = $paymentFormController->view();
extract($data, EXTR_SKIP);
?>
<?php require __DIR__ . '/../../../app/Views/pages/payment-form.php'; ?>





