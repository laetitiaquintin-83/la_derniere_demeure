<?php

class AdminDashboardController
{
    private AdminDashboardModel $model;

    public function __construct(AdminDashboardModel $model)
    {
        $this->model = $model;
    }

    public function index(): array
    {
        if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
            header('Location: login.php');
            exit;
        }

        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
                $message = 'Erreur de sécurité : le sceau CSRF est invalide.';
                $messageType = 'error';
            } elseif (isset($_POST['moderation_action'])) {
                [$message, $messageType] = $this->handleModeration();
            } elseif (isset($_POST['ajouter_article'])) {
                [$message, $messageType] = $this->handleAddArticle();
            }
        }

        return [
            'message' => $message,
            'messageType' => $messageType,
            'csrf_token' => genererTokenCSRF(),
            'nombre_articles' => isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0,
            'attente' => $this->model->getPendingAnimalMessages(),
        ];
    }

    private function handleModeration(): array
    {
        $messageId = $_POST['message_id'] ?? '';

        if (!ctype_digit((string) $messageId)) {
            return ['Identifiant de message invalide.', 'error'];
        }

        $messageId = (int) $messageId;

        if ($_POST['moderation_action'] === 'approve') {
            $this->model->approveAnimalMessage($messageId);
            log_audit_event('APPROVE', 'livre_dor_animaux', $messageId, null, ['approuve' => 1]);
            return ['La pensée a été scellée dans le Jardin.', 'success'];
        }

        if ($_POST['moderation_action'] === 'delete') {
            $oldMessage = $this->model->deleteAnimalMessage($messageId);
            log_audit_event('DELETE', 'livre_dor_animaux', $messageId, $oldMessage, null);
            return ['La pensée a été bannie du registre.', 'error'];
        }

        return ['Action de modération inconnue.', 'error'];
    }

    private function handleAddArticle(): array
    {
        $nom = trim($_POST['nom'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        $prix = $_POST['prix'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if ($nom === '' || $categorie === '' || $prix === '' || $stock === '') {
            return ['Erreur : tous les champs obligatoires doivent être complétés.', 'error'];
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return ['Erreur : une image est requise.', 'error'];
        }

        $targetDir = __DIR__ . '/../../images/catalogue/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $validation = validate_image_upload($_FILES['image'], 5 * 1024 * 1024);
        if (!$validation['valid']) {
            return [$validation['error'], 'error'];
        }

        $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $validation['extension'];
        $targetFile = 'images/catalogue/' . $fileName;
        $realPath = realpath($targetDir) . DIRECTORY_SEPARATOR . $fileName;

        if (strpos($realPath, realpath($targetDir)) !== 0) {
            return ['Erreur de sécurité : Chemin invalide (tentative de traversée répertoire).', 'error'];
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $realPath)) {
            return ['Erreur lors de l’enregistrement de l’image.', 'error'];
        }

        try {
            $itemId = $this->model->addCatalogueItem([
                'nom' => $nom,
                'description' => $description,
                'prix' => (float) $prix,
                'image_path' => $targetFile,
                'categorie' => $categorie,
                'stock' => (int) $stock,
            ]);

            log_audit_event('CREATE', 'catalogue_funeraire', $itemId, null, [
                'nom' => $nom,
                'categorie' => $categorie,
                'prix' => (float) $prix,
                'stock' => (int) $stock,
                'image_path' => $targetFile,
            ]);

            return ["L'article « $nom » a été ajouté avec succès.", 'success'];
        } catch (PDOException $exception) {
            if (is_file($realPath)) {
                unlink($realPath);
            }

            return ['Erreur SQL : ' . $exception->getMessage(), 'error'];
        }
    }
}