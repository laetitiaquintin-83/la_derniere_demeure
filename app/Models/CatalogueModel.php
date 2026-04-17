<?php

class CatalogueModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPoeticTitles(): array
    {
        return [
            'Cercueil' => 'Vaisseaux de Mémoire',
            'Cercueils' => 'Vaisseaux de Mémoire',
            'Urne' => 'Le Souffle des Anciens',
            'Urnes' => 'Le Souffle des Anciens',
            'Stèle' => "Les Gardiens de l'Éternité",
            'Stèles' => "Les Gardiens de l'Éternité",
            'Fleurs' => "L'Offrande Éternelle",
            'Hommages Floraux' => "L'Offrande Éternelle",
            'Univers Passion' => "L'Écho d'une Vie",
            'Animaux' => 'Le Repos des Fidèles',
        ];
    }

    public function getCategories(): array
    {
        try {
            $statement = $this->pdo->query(
                "SELECT DISTINCT categorie
                 FROM catalogue_funeraire
                 WHERE categorie IS NOT NULL AND categorie != ''
                 ORDER BY categorie ASC"
            );

            $categories = $statement->fetchAll(PDO::FETCH_COLUMN);
            $poeticTitles = $this->getPoeticTitles();
            $filteredCategories = [];
            $titlesSeen = [];

            foreach ($categories as $category) {
                $title = $poeticTitles[$category] ?? $category;
                if (!in_array($title, $titlesSeen, true)) {
                    $filteredCategories[] = $category;
                    $titlesSeen[] = $title;
                }
            }

            return $filteredCategories;
        } catch (Throwable $exception) {
            error_log('Erreur catalogue: ' . $exception->getMessage());

            return [];
        }
    }

    public function getSections(array $categories): array
    {
        $sections = [];
        $poeticTitles = $this->getPoeticTitles();

        foreach ($categories as $category) {
            $statement = $this->pdo->prepare(
                "SELECT *
                 FROM catalogue_funeraire
                 WHERE categorie = ?
                 ORDER BY id DESC"
            );
            $statement->execute([$category]);

            $products = [];
            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $product) {
                $product['image_path'] = $this->normalizeImagePath((string) ($product['image_path'] ?? ''));
                $products[] = $product;
            }

            $sections[$category] = [
                'title' => $poeticTitles[$category] ?? $category,
                'products' => $products,
            ];
        }

        return $sections;
    }

    public function resolveRequestedCategory(?string $requestedCategory, array $availableCategories): ?string
    {
        if ($requestedCategory === null || $requestedCategory === '') {
            return null;
        }

        return in_array($requestedCategory, $availableCategories, true) ? $requestedCategory : null;
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