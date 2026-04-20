<?php

class ContactController
{
    public function handleSubmit(): array
    {
        $nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
        $message_succes = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
                $message_succes = "⚠️ Erreur de sécurité. Veuillez réessayer.";
            } else {
                $nom = trim($_POST['nom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');
                $sujet = trim($_POST['sujet'] ?? '');
                $message = trim($_POST['message'] ?? '');

                if ($nom === '' || $email === '' || $sujet === '' || $message === '') {
                    $message_succes = "⚠️ Veuillez remplir tous les champs obligatoires.";
                } else {
                    // Validation basique email
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $message_succes = "⚠️ Veuillez entrer une adresse email valide.";
                    } else {
                        // Pour l'instant, on simule l'envoi. Plus tard, on pourra ajouter mail() ou BDD.
                        $message_succes = "Votre message a été confié à notre scénographe. Nous vous répondrons avec la plus grande discrétion.";
                        log_audit_event('CONTACT_FORM', 'contact', null, null, [
                            'nom' => $nom,
                            'email' => $email,
                            'telephone' => $telephone,
                            'sujet' => $sujet,
                        ]);
                    }
                }
            }
        }

        return [
            'nombre_articles' => $nombre_articles,
            'message_succes' => $message_succes,
            'csrf_token' => genererTokenCSRF(),
        ];
    }
}
