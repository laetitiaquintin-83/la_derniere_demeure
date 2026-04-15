<?php
// On inclut la configuration (qui s'occupe déjà de démarrer la session proprement)
require_once 'config.php';

// On prévient le navigateur qu'on lui répond en format JSON
header('Content-Type: application/json');

// 1. On s'assure que la requête est bien envoyée en POST (et non en tapant l'URL dans le navigateur)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode d\'accès non autorisée.']);
    exit;
}

// On récupère les données envoyées par JavaScript (AJAX)
$data = json_decode(file_get_contents('php://input'), true);

// 2. Validation du jeton CSRF (Excellente pratique pour l'examen)
if (!isset($data['csrf_token']) || !validerTokenCSRF($data['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Erreur de sécurité : le sceau de la requête est corrompu.']);
    exit;
}

// 3. Validation de l'ID de la relique
if (!isset($data['id']) || !ctype_digit(strval($data['id']))) {
    echo json_encode(['success' => false, 'message' => 'Relique introuvable ou format invalide.']);
    exit;
}

$id = (int)$data['id'];

// 4. On vérifie que la relique existe bien dans le registre
$stmt = $pdo->prepare("SELECT nom FROM catalogue_funeraire WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if ($produit) {
    // Initialiser le panier s'il n'existe pas
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // SÉCURITÉ: Limiter la quantité par article (max 10 unités)
    $quantity = isset($_SESSION['panier'][$id]) ? $_SESSION['panier'][$id] : 0;
    if ($quantity >= 10) {
        echo json_encode(['success' => false, 'message' => 'Limite atteinte : maximum 10 unités par article.']);
        exit;
    }

    // Ajouter un exemplaire ou créer l'entrée
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]++;
    } else {
        $_SESSION['panier'][$id] = 1;
    }

    // Compter le nombre total d'articles dans le panier
    $totalArticles = array_sum($_SESSION['panier']);

    // On renvoie un succès avec le nouveau total pour mettre à jour le compteur
    echo json_encode([
        'success' => true, 
        'message' => $produit['nom'] . ' a rejoint votre offrande.',
        'total' => $totalArticles
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Cette relique s\'est évaporée des registres.']);
}