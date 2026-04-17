<?php
// constantes.php - Centralisation des constantes du projet
// Démontre une bonne architecture pour les jeunes développeurs

// ==========================================
// CHEMINS ET RÉPERTOIRES
// ==========================================
define('BASE_URL', 'http://localhost/la_derniere_demeure/');
define('PROJECT_ROOT', dirname(__DIR__, 2));
define('IMAGES_DIR', PROJECT_ROOT . '/images/');
define('UPLOADS_DIR', IMAGES_DIR . 'souvenirs/');
define('CATALOGUE_DIR', IMAGES_DIR . 'catalogue/');

// ==========================================
// SÉCURITÉ ET VALIDATION
// ==========================================
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
define('ALLOWED_IMAGE_MIMES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// ==========================================
// CLÉS DE SESSION
// ==========================================
define('SESSION_KEY_ADMIN', 'admin_connecte');
define('SESSION_KEY_CSRF', 'csrf_token');
define('SESSION_KEY_CART', 'panier');
define('SESSION_KEY_USER_ID', 'user_id');

// ==========================================
// LIMITES DE SÉCURITÉ
// ==========================================
define('CART_ITEM_MAX_QUANTITY', 10);
define('RATE_LIMIT_AJAX_MS', 500);
define('CSRF_TOKEN_LENGTH', 32); // bytes

// ==========================================
// CATÉGORIES DE PRODUITS
// ==========================================
define('PRODUCT_CATEGORIES', [
    'Cercueils' => 'Vaisseaux de Mémoire',
    'Urnes' => 'Le Souffle des Anciens',
    'Stèles' => "Les Gardiens de l'Éternité",
    'Hommages Floraux' => "L'Offrande Éternelle",
    'Animaux' => "Le Repos des Fidèles"
]);

// ==========================================
// MESSAGES D'ERREUR STANDARDISÉS
// ==========================================
define('ERROR_CSRF_INVALID', "Erreur de sécurité : le sceau de la requête est corrompu.");
define('ERROR_AUTH_REQUIRED', "Authentification requise. Veuillez vous connecter.");
define('ERROR_FILE_INVALID', "Erreur : le fichier uploadé n'est pas valide.");
define('ERROR_FILE_TOO_LARGE', "Erreur : la taille du fichier dépasse " . (MAX_UPLOAD_SIZE / 1024 / 1024) . "MB.");
define('ERROR_INVALID_METHOD', "Méthode d'accès non autorisée.");

// ==========================================
// MESSAGES DE SUCCÈS STANDARDISÉS
// ==========================================
define('SUCCESS_ADDED_CART', "L'offrande a été ajoutée à votre panier.");
define('SUCCESS_LOGIN', "Bienvenue au registre de la crypte.");
define('SUCCESS_CREATED', "L'article a été créé avec succès.");
