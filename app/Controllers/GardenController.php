<?php

class GardenController
{
    private GardenModel $model;

    public function __construct(GardenModel $model)
    {
        $this->model = $model;
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: repos_des_fideles.php');
            exit;
        }

        if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
            http_response_code(403);
            die('⚠️ Erreur de sécurité CSRF. Veuillez réessayer.');
        }

        $nomProprietaire = htmlspecialchars($_POST['nom_proprietaire']);
        $nomAnimal = htmlspecialchars($_POST['nom_animal']);
        $message = htmlspecialchars($_POST['message']);

        $photoPath = null;
        if (isset($_FILES['photo_compagnon']) && $_FILES['photo_compagnon']['error'] === 0) {
            $directory = 'images/souvenirs/';
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $extension = strtolower(pathinfo($_FILES['photo_compagnon']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($extension, $allowedExtensions, true) && $_FILES['photo_compagnon']['size'] <= 3 * 1024 * 1024) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $realMime = finfo_file($finfo, $_FILES['photo_compagnon']['tmp_name']);
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (in_array($realMime, $allowedMimes, true)) {
                    $filename = 'souvenir_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                    $fullPath = $directory . $filename;

                    if (move_uploaded_file($_FILES['photo_compagnon']['tmp_name'], $fullPath)) {
                        $photoPath = $fullPath;
                    }
                }
            }
        }

        $this->model->addMessage([
            'nom_proprietaire' => $nomProprietaire,
            'nom_animal' => $nomAnimal,
            'message' => $message,
        ], $photoPath);

        header('Location: repos_des_fideles.php?status=success');
        exit;
    }
}