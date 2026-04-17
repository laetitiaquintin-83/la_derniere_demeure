<?php
/**
 * payment-form.php
 * 
 * Formulaire de Paiement Sécurisé avec Stripe Elements
 * 
 * Les données sensibles (n° carte, CVV) ne sont JAMAIS stockées sur ce serveur
 * Stripe s'occupe de tout via son formulaire tokenisé
 */

require_once __DIR__ . '/app/bootstrap.php';

$payment_id = $_GET['payment_id'] ?? null;

// Valider l'ID de paiement
if (!$payment_id || !isset($_SESSION['pending_payment']) || $_SESSION['pending_payment']['id'] !== $payment_id) {
    header('Location: panier.php');
    exit;
}

$payment = $_SESSION['pending_payment'];
$montant = number_format($payment['amount'], 2, ',', ' ');
?>
<?php require __DIR__ . '/app/Views/pages/payment-form.php'; ?>


