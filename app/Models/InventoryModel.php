<?php

class InventoryModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateStock(int $id, int $adjustment): void
    {
        $statement = $this->pdo->prepare("UPDATE catalogue_funeraire SET stock = GREATEST(0, stock + ?) WHERE id = ?");
        $statement->execute([$adjustment, $id]);
    }
}