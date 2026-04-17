<?php

class LogoutController
{
    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
                http_response_code(403);
                die('Erreur de sécurité : jeton CSRF invalide.');
            }

            log_audit_event('LOGOUT', 'admin_auth', null, ['admin_connecte' => true], ['admin_connecte' => false]);

            session_unset();
            session_destroy();

            header('Location: index.php');
            exit;
        }
    }
}