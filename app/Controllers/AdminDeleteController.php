<?php

class AdminDeleteController
{
    private AdminDashboardModel $model;

    public function __construct(AdminDashboardModel $model)
    {
        $this->model = $model;
    }

    public function delete(): void
    {
        if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
            header('Location: /login.php');
            exit;
        }

        if (!isset($_GET['token']) || !validerTokenCSRF($_GET['token'])) {
            die('Erreur de sécurité : Le sceau de suppression est invalide ou a expiré.');
        }

        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            die("Erreur : L'identifiant de l'article est corrompu.");
        }

        $id = (int) $_GET['id'];

        try {
            $produit = $this->model->getCatalogueProductById($id);

            if (!$produit) {
                die("L'article a déjà rejoint le néant...");
            }

            $imagePath = $this->resolvePublicImagePath((string) ($produit['image_path'] ?? ''));
            if ($imagePath && file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }

            $this->model->deleteCatalogueProduct($id);

            header('Location: /gestion.php?msg=suppression_ok');
            exit;
        } catch (PDOException $exception) {
            error_log('Erreur lors de la suppression : ' . $exception->getMessage());
            die("Une force obscure a empêché la suppression de l'article.");
        }
    }

    private function resolvePublicImagePath(string $relativePath): ?string
    {
        $relativePath = ltrim(trim($relativePath), '/');

        if ($relativePath === '') {
            return null;
        }

        $fullPath = PROJECT_ROOT . '/public/' . $relativePath;

        return realpath($fullPath) ?: null;
    }
}
