<?php

class CartActionModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findProductById(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT nom FROM catalogue_funeraire WHERE id = ?");
        $statement->execute([$id]);

        $product = $statement->fetch(PDO::FETCH_ASSOC);
        return $product ?: null;
    }

    public function incrementCartItem(int $id): int
    {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        if (isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id]++;
        } else {
            $_SESSION['panier'][$id] = 1;
        }

        return array_sum($_SESSION['panier']);
    }
}