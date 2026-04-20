<?php

class RecommandationController
{
    private RecommandationModel $model;

    public function __construct(RecommandationModel $model)
    {
        $this->model = $model;
    }

    public function index(): array
    {
        $this->model->ensureTable();

        $nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
        $flash_message = '';
        $flash_type = '';

        $form_data = [
            'nom' => '',
            'email' => '',
            'service' => '',
            'message' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
                $flash_message = 'Erreur de securite: le formulaire a expire, veuillez reessayer.';
                $flash_type = 'error';
            } else {
                $nom = trim($_POST['nom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $service = trim($_POST['service'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $consentement = isset($_POST['consentement']) ? 1 : 0;

                $form_data = [
                    'nom' => $nom,
                    'email' => $email,
                    'service' => $service,
                    'message' => $message,
                ];

                if ($nom === '' || $email === '' || $service === '' || $message === '') {
                    $flash_message = 'Merci de remplir tous les champs obligatoires.';
                    $flash_type = 'error';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $flash_message = 'Adresse email invalide.';
                    $flash_type = 'error';
                } elseif ($consentement !== 1) {
                    $flash_message = 'Le consentement est requis pour publier votre temoignage.';
                    $flash_type = 'error';
                } else {
                    $this->model->addRecommandation([
                        'nom' => substr($nom, 0, 120),
                        'email' => substr($email, 0, 255),
                        'service' => substr($service, 0, 120),
                        'message' => substr($message, 0, 1200),
                        'consentement' => $consentement,
                    ]);

                    log_audit_event('RECOMMANDATION_CREATE', 'recommandations_confiance', null, null, [
                        'nom' => $nom,
                        'service' => $service,
                    ]);

                    $flash_message = 'Merci pour votre confiance. Votre temoignage est en attente de validation par notre equipe.';
                    $flash_type = 'success';
                    $form_data = [
                        'nom' => '',
                        'email' => '',
                        'service' => '',
                        'message' => '',
                    ];
                }
            }
        }

        $hero_image = is_file(PROJECT_ROOT . '/public/images/hommage.png')
            ? 'images/hommage.png'
            : 'images/foret.jpg';

        return [
            'nombre_articles' => $nombre_articles,
            'temoignages' => $this->model->getApprovedRecommandations(9),
            'form_data' => $form_data,
            'flash_message' => $flash_message,
            'flash_type' => $flash_type,
            'hero_image' => $hero_image,
            'csrf_token' => genererTokenCSRF(),
        ];
    }
}
