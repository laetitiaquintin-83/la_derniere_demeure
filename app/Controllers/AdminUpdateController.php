<?php

class AdminUpdateController
{
    private AdminDashboardModel $model;

    public function __construct(AdminDashboardModel $model)
    {
        $this->model = $model;
    }

    public function edit(): array
    {
        if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
            header('Location: /login.php');
            exit;
        }

        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            die('Erreur : ID invalide.');
        }

        $id = (int) $_GET['id'];
        $categories = $this->model->getCatalogueCategories();
        $produit = $this->model->getCatalogueProductById($id);

        if (!$produit) {
            die('Le cercueil a disparu dans les brumes...');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
                die('Erreur de sécurité : jeton CSRF invalide.');
            }

            $oldValues = [
                'nom' => $produit['nom'],
                'categorie' => $produit['categorie'],
                'essence_bois' => $produit['essence_bois'] ?? null,
                'couleur_velours' => $produit['couleur_velours'] ?? null,
                'prix' => $produit['prix'],
                'stock' => $produit['stock'],
                'description' => $produit['description'],
                'image_path' => $produit['image_path'],
            ];

            $nom = trim($_POST['nom'] ?? '');
            $categorie = trim($_POST['categorie'] ?? '');
            $essenceBois = trim($_POST['essence_bois'] ?? '');
            $couleurVelours = trim($_POST['couleur_velours'] ?? '');
            $prix = $_POST['prix'] ?? 0;
            $stock = $_POST['stock'] ?? '';
            $description = trim($_POST['description'] ?? '');

            if ($nom === '' || $categorie === '' || $prix === '' || $stock === '') {
                die('Erreur : le nom, la catégorie, le prix et le stock sont obligatoires.');
            }

            if (!is_numeric($stock) || (int) $stock < 0) {
                die('Erreur : le stock doit être un nombre entier positif ou nul.');
            }

            if (!is_dir(CATALOGUE_DIR)) {
                mkdir(CATALOGUE_DIR, 0777, true);
            }

            $imagePath = (string) ($produit['image_path'] ?? '');
            $oldImagePath = $imagePath;
            $newImageUploaded = false;

            if (!empty($_POST['image_path_manuel'])) {
                $manualInput = trim($_POST['image_path_manuel']);
                $manualFile = basename($manualInput);
                $candidate = rtrim(CATALOGUE_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $manualFile;
                $candidateReal = realpath($candidate);
                $allowedReal = realpath(CATALOGUE_DIR);

                if ($candidateReal === false || $allowedReal === false || strpos($candidateReal, $allowedReal) !== 0) {
                    die('Erreur : chemin image manuel invalide.');
                }

                $imagePath = 'images/catalogue/' . $manualFile;
            } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $validation = validate_image_upload($_FILES['image'], MAX_UPLOAD_SIZE);

                if (!$validation['valid']) {
                    die($validation['error']);
                }

                $fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $validation['extension'];
                $targetFile = rtrim(CATALOGUE_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    die('Erreur lors du transfert de l’image.');
                }

                $imagePath = 'images/catalogue/' . $fileName;
                $newImageUploaded = true;
            }

            $success = $this->model->updateCatalogueProduct($id, [
                'nom' => $nom,
                'categorie' => $categorie,
                'essence_bois' => $essenceBois,
                'couleur_velours' => $couleurVelours,
                'prix' => (float) $prix,
                'stock' => (int) $stock,
                'description' => $description,
                'image_path' => $imagePath,
            ]);

            if ($success) {
                log_audit_event('UPDATE', 'catalogue_funeraire', $id, $oldValues, [
                    'nom' => $nom,
                    'categorie' => $categorie,
                    'essence_bois' => $essenceBois,
                    'couleur_velours' => $couleurVelours,
                    'prix' => (float) $prix,
                    'stock' => (int) $stock,
                    'description' => $description,
                    'image_path' => $imagePath,
                ]);

                if ($newImageUploaded && !empty($oldImagePath)) {
                    $oldReal = $this->resolvePublicImagePath($oldImagePath);
                    $allowedReal = realpath(CATALOGUE_DIR);

                    if ($oldReal && $allowedReal && strpos($oldReal, $allowedReal) === 0 && file_exists($oldReal)) {
                        unlink($oldReal);
                    }
                }

                header('Location: /gestion.php?success=1');
                exit;
            }
        }

        return [
            'categories' => $categories,
            'produit' => $produit,
            'csrf_token' => genererTokenCSRF(),
        ];
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
