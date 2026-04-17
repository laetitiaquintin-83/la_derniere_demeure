<?php

class StockController
{
    private InventoryModel $model;

    public function __construct(InventoryModel $model)
    {
        $this->model = $model;
    }

    public function update(): void
    {
        if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
            header('Location: login.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: gestion.php');
            exit;
        }

        if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }

        if (!isset($_POST['id']) || !ctype_digit(strval($_POST['id']))) {
            die('Erreur : ID invalide.');
        }

        $id = (int) $_POST['id'];
        $adjustment = (int) ($_POST['quantity'] ?? 0);

        if ($adjustment < -999 || $adjustment > 999) {
            die('Erreur : l\'ajustement est hors limites.');
        }

        try {
            $this->model->updateStock($id, $adjustment);
            header('Location: gestion.php?msg=stock_mis_a_jour');
            exit;
        } catch (PDOException $exception) {
            error_log('Erreur ajustement stock : ' . $exception->getMessage());
            die('Une erreur est survenue lors de la mise à jour des réserves.');
        }
    }
}