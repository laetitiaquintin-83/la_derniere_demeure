<?php

class OrderConfirmationController
{
    public function view(): array
    {
        $payment_id = $_GET['session_id'] ?? $_GET['payment_id'] ?? null;

        if (!$payment_id) {
            header('Location: /index.php');
            exit;
        }

        // En production, valider avec Stripe que le paiement est réussi
        // Pour cette démo, on considère que si on est ici, c'est bon

        return [
            'payment_id' => $payment_id,
        ];
    }
}
