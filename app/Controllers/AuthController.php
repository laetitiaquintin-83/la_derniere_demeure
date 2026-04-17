<?php

class AuthController
{
    private AuthModel $model;

    public function __construct(AuthModel $model)
    {
        $this->model = $model;
    }

    public function login(): array
    {
        $error = '';

        if (!empty($_SESSION['admin_connecte']) && $_SESSION['admin_connecte'] === true) {
            header('Location: admin.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenSubmitted = $_POST['csrf_token'] ?? '';
            if (!validerTokenCSRF($tokenSubmitted)) {
                die('Erreur de sécurité : Jeton invalide. Le sceau a été corrompu.');
            }

            $rateLimit = check_rate_limit('login_admin', 5, 300);
            if (!$rateLimit['allowed']) {
                http_response_code(429);
                die('⏳ Trop de tentatives. Réessayez plus tard.');
            }

            $username = trim($_POST['username'] ?? 'admin');
            $password = $_POST['mot_de_passe'] ?? '';

            if ($password === '') {
                $error = 'Le mot de passe est obligatoire.';
            } else {
                try {
                    $adminUser = $this->model->findAdminUser($username);

                    if ($adminUser && password_verify($password, $adminUser['password_hash'])) {
                        reset_rate_limit('login_admin');
                        session_regenerate_id(true);
                        $_SESSION['admin_connecte'] = true;
                        log_audit_event('LOGIN_SUCCESS', 'admin_auth', (int) ($adminUser['id'] ?? 0), null, ['username' => $username]);

                        header('Location: admin.php');
                        exit;
                    }

                    $error = 'Accès refusé. Sceau incorrect.';
                } catch (PDOException $exception) {
                    $error = 'Erreur système. Veuillez réessayer.';
                }
            }
        }

        return [
            'erreur' => $error,
        ];
    }
}