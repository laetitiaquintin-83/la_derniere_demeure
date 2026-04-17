<?php

class CartModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCartDetails(array $cartSession): array
    {
        $cartDetails = [];
        $total = 0;

        if (empty($cartSession)) {
            return [
                'panier_details' => $cartDetails,
                'total' => $total,
            ];
        }

        $statement = $this->pdo->prepare("SELECT id, nom, prix FROM catalogue_funeraire WHERE id = ?");

        foreach ($cartSession as $id => $quantity) {
            $statement->execute([(int) $id]);
            $product = $statement->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $product['quantite'] = (int) $quantity;
                $product['sous_total'] = (float) $product['prix'] * (int) $quantity;
                $cartDetails[] = $product;
                $total += $product['sous_total'];
            }
        }

        return [
            'panier_details' => $cartDetails,
            'total' => $total,
        ];
    }

    public function removeItemFromCart(int $productId): void
    {
        if (isset($_SESSION['panier'][$productId])) {
            unset($_SESSION['panier'][$productId]);
        }
    }
}