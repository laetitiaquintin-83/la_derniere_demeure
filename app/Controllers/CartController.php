<?php

class CartController
{
    private CartModel $model;

    public function __construct(CartModel $model)
    {
        $this->model = $model;
    }

    public function index(): array
    {
        if (isset($_GET['remove'])) {
            if (!isset($_GET['token']) || !validerTokenCSRF($_GET['token'])) {
                die('Erreur de sécurité : Sceau de suppression invalide.');
            }

            $productId = (int) $_GET['remove'];
            $this->model->removeItemFromCart($productId);

            header('Location: panier.php');
            exit;
        }

        $cartSession = $_SESSION['panier'] ?? [];
        $cartData = $this->model->getCartDetails($cartSession);

        return [
            'nombre_articles' => array_sum($cartSession),
            'panier_details' => $cartData['panier_details'],
            'total' => $cartData['total'],
        ];
    }
}