<?php
/**
 * payment-success.php
 * 
 * Page de confirmation après paiement réussi
 * Affiche les détails de la commande
 */

require_once __DIR__ . '/../../../app/bootstrap.php';

$payment_id = $_GET['session_id'] ?? $_GET['payment_id'] ?? null;

if (!$payment_id) {
    header('Location: /index.php');
    exit;
}

// En production, valider avec Stripe que le paiement est réussi
// Pour cette démo, on considère que si on est ici, c'est bon
?>
<?php require __DIR__ . '/../../../app/Views/pages/payment-success.php'; ?>





