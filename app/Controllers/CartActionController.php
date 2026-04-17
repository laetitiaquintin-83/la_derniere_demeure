<?php

class CartActionController
{
    private CartActionModel $model;

    public function __construct(CartActionModel $model)
    {
        $this->model = $model;
    }

    public function add(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode d\'accès non autorisée.']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Données invalides.']);
            exit;
        }

        if (!isset($data['csrf_token']) || !validerTokenCSRF($data['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Erreur de sécurité : le sceau de la requête est corrompu.']);
            exit;
        }

        if (!isset($data['id']) || !ctype_digit(strval($data['id']))) {
            echo json_encode(['success' => false, 'message' => 'Article introuvable ou format invalide.']);
            exit;
        }

        $id = (int) $data['id'];
        $product = $this->model->findProductById($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Cet article s\'est évaporé des registres.']);
            exit;
        }

        $currentQuantity = isset($_SESSION['panier'][$id]) ? (int) $_SESSION['panier'][$id] : 0;
        if ($currentQuantity >= 10) {
            echo json_encode(['success' => false, 'message' => 'Limite atteinte : maximum 10 unités par article.']);
            exit;
        }

        $totalArticles = $this->model->incrementCartItem($id);

        echo json_encode([
            'success' => true,
            'message' => $product['nom'] . ' a rejoint votre offrande.',
            'total' => $totalArticles,
        ]);
    }
}