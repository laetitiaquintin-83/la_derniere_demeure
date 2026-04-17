<?php

class HomePageModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getFeaturedCategories(int $limit = 4): array
    {
        try {
            $statement = $this->pdo->prepare(
                "SELECT DISTINCT categorie
                 FROM catalogue_funeraire
                 WHERE categorie IS NOT NULL AND categorie != ''
                 ORDER BY categorie ASC
                 LIMIT :limit"
            );
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();

            $categories = $statement->fetchAll(PDO::FETCH_COLUMN);
            $categoryImages = [];

            foreach ($categories as $category) {
                $imageStatement = $this->pdo->prepare(
                    "SELECT image_path
                     FROM catalogue_funeraire
                     WHERE categorie = ?
                     ORDER BY id DESC
                     LIMIT 1"
                );
                $imageStatement->execute([$category]);

                $categoryImages[$category] = $this->normalizeImagePath((string) $imageStatement->fetchColumn());
            }

            return [
                'categories' => $categories,
                'category_images' => $categoryImages,
            ];
        } catch (Throwable $exception) {
            error_log('Erreur base de données: ' . $exception->getMessage());

            return [
                'categories' => [],
                'category_images' => [],
            ];
        }
    }

    public function getPoeticTitles(): array
    {
        return [
            'Cercueils' => 'Vaisseaux de Mémoire',
            'Urnes' => 'Le Souffle des Anciens',
            'Fleurs' => 'L\'Offrande Éternelle',
            'Stèles' => 'Les Gardiens de L\'Éternité',
            'Animaux' => 'Le Repos des Fidèles',
        ];
    }

    private function normalizeImagePath(string $rawPath): string
    {
        $rawPath = trim($rawPath);

        if ($rawPath === '') {
            return 'images/default.jpg';
        }

        if (strpos($rawPath, 'images/') === 0) {
            return $rawPath;
        }

        if (strpos($rawPath, 'catalogue/') === 0) {
            return 'images/' . $rawPath;
        }

        return 'images/catalogue/' . basename($rawPath);
    }
}