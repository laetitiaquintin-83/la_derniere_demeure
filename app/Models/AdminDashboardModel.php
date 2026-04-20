<?php

class AdminDashboardModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addCatalogueItem(array $data): int
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO catalogue_funeraire (nom, description, prix, image_path, categorie, stock)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $statement->execute([
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['image_path'],
            $data['categorie'],
            $data['stock'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getCatalogueCategories(): array
    {
        $statement = $this->pdo->query(
            "SELECT DISTINCT categorie
             FROM catalogue_funeraire
             WHERE categorie IS NOT NULL AND categorie != ''
             ORDER BY categorie ASC"
        );

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getCatalogueProductById(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM catalogue_funeraire WHERE id = ? LIMIT 1");
        $statement->execute([$id]);

        $product = $statement->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }

    public function updateCatalogueProduct(int $id, array $data): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE catalogue_funeraire
             SET nom = ?, categorie = ?, essence_bois = ?, couleur_velours = ?, prix = ?, stock = ?, description = ?, image_path = ?
             WHERE id = ?"
        );

        return $statement->execute([
            $data['nom'],
            $data['categorie'],
            $data['essence_bois'],
            $data['couleur_velours'],
            $data['prix'],
            $data['stock'],
            $data['description'],
            $data['image_path'],
            $id,
        ]);
    }

    public function deleteCatalogueProduct(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM catalogue_funeraire WHERE id = ?");
        $statement->execute([$id]);
    }

    public function getPendingAnimalMessages(): array
    {
        return $this->pdo
            ->query("SELECT * FROM livre_dor_animaux WHERE approuve = 0 ORDER BY date_publication DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveAnimalMessage(int $messageId): void
    {
        $this->pdo->prepare("UPDATE livre_dor_animaux SET approuve = 1 WHERE id = ?")
            ->execute([$messageId]);
    }

    public function deleteAnimalMessage(int $messageId): ?array
    {
        $oldStatement = $this->pdo->prepare("SELECT * FROM livre_dor_animaux WHERE id = ? LIMIT 1");
        $oldStatement->execute([$messageId]);

        $oldMessage = $oldStatement->fetch(PDO::FETCH_ASSOC) ?: null;

        $this->pdo->prepare("DELETE FROM livre_dor_animaux WHERE id = ?")
            ->execute([$messageId]);

        return $oldMessage;
    }

    public function getProductsForInventory(): array
    {
        $statement = $this->pdo->prepare(
            "SELECT id, nom, categorie, prix, stock, image_path
             FROM catalogue_funeraire
             ORDER BY id DESC"
        );
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingRecommandations(): array
    {
        $this->ensureRecommandationsTable();

        return $this->pdo
            ->query("SELECT id, nom, service, message, created_at FROM recommandations_confiance WHERE approuve = 0 ORDER BY created_at DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveRecommandation(int $recommandationId): void
    {
        $this->ensureRecommandationsTable();

        $this->pdo->prepare("UPDATE recommandations_confiance SET approuve = 1 WHERE id = ?")
            ->execute([$recommandationId]);
    }

    public function deleteRecommandation(int $recommandationId): ?array
    {
        $this->ensureRecommandationsTable();

        $oldStatement = $this->pdo->prepare("SELECT * FROM recommandations_confiance WHERE id = ? LIMIT 1");
        $oldStatement->execute([$recommandationId]);

        $oldRecommandation = $oldStatement->fetch(PDO::FETCH_ASSOC) ?: null;

        $this->pdo->prepare("DELETE FROM recommandations_confiance WHERE id = ?")
            ->execute([$recommandationId]);

        return $oldRecommandation;
    }

    private function ensureRecommandationsTable(): void
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS recommandations_confiance (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(120) NOT NULL,
                email VARCHAR(255) NOT NULL,
                service VARCHAR(120) NOT NULL,
                message TEXT NOT NULL,
                consentement TINYINT(1) NOT NULL DEFAULT 0,
                approuve TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_approuve_created (approuve, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }
}