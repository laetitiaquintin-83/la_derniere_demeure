<?php

class InventoryViewController
{
    private AdminDashboardModel $model;

    public function __construct(AdminDashboardModel $model)
    {
        $this->model = $model;
    }

    public function view(): array
    {
        // Vérifier authentification admin
        if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
            header('Location: /login.php');
            exit;
        }

        $nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
        $produits = $this->model->getProductsForInventory();

        return [
            'nombre_articles' => $nombre_articles,
            'produits' => $produits,
        ];
    }
}
