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
}