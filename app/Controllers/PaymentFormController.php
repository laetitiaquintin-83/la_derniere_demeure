<?php

class PaymentFormController
{
    public function view(): array
    {
        $payment_id = $_GET['payment_id'] ?? null;

        // Valider l'ID de paiement en session
        if (!$payment_id || !isset($_SESSION['pending_payment']) || $_SESSION['pending_payment']['id'] !== $payment_id) {
            header('Location: /panier.php');
            exit;
        }

        $payment = $_SESSION['pending_payment'];
        $montant = number_format($payment['amount'], 2, ',', ' ');

        return [
            'payment_id' => $payment_id,
            'payment' => $payment,
            'montant' => $montant,
            'stripe_public_key' => STRIPE_PUBLIC_KEY,
            'csrf_token' => genererTokenCSRF(),
        ];
    }
}
