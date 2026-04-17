<?php
// helpers.php - Fonctions utilitaires pour éviter la duplication de code
// Ces functions démontrent la maîtrise des bonnes pratiques

require_once __DIR__ . '/constantes.php';

// ==========================================
// ÉCHAPPEMENT ET SANITIZATION
// ==========================================

/**
 * Échappe une chaîne pour une sortie HTML sécurisée
 * @param string $input Texte à échapper
 * @return string Texte échappé
 */
function escape_html($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * Récupère et valide un ID de reqête GET/POST
 * @param string $key Clé du paramètre
 * @param string $method 'GET' ou 'POST'
 * @return int|null ID valide ou null
 */
function get_safe_id($key, $method = 'POST') {
    $source = ($method === 'GET') ? $_GET : $_POST;
    $value = $source[$key] ?? null;
    return (ctype_digit(strval($value))) ? (int)$value : null;
}

/**
 * Récupère une valeur POST avec défaut, échappée et trimée
 * @param string $key Clé du paramètre
 * @param string $default Valeur par défaut
 * @return string Valeur échappée
 */
function get_post_safe($key, $default = '') {
    $value = $_POST[$key] ?? $default;
    return escape_html(trim($value));
}

/**
 * Valide un nom de personne (caractères alphabétiques et accents uniquement)
 * @param string $name Nom à valider
 * @param int $min Longueur minimum
 * @param int $max Longueur maximum
 * @return string|false Nom validé ou false si invalide
 */
function validate_person_name($name, $min = 3, $max = 50) {
    $name = trim($name);
    $length = strlen($name);
    
    if ($length < $min || $length > $max) {
        return false;
    }
    
    // Autoriser lettres, espaces, tirets, accents
    if (!preg_match('/^[a-zA-Zéèêàâôûç\s\-\']+$/', $name)) {
        return false;
    }
    
    return escape_html($name);
}

// ==========================================
// VALIDATION PAIEMENT
// ==========================================

/**
 * Valide un numéro de carte bancaire avec l'algorithme Luhn
 * @param string $cardNumber Numéro de carte
 * @return bool true si valide
 */
function validate_card_number($cardNumber) {
    $cardNumber = preg_replace('/\D/', '', $cardNumber);
    
    if (!preg_match('/^[0-9]{13,19}$/', $cardNumber)) {
        return false;
    }
    
    // Algorithme Luhn
    $sum = 0;
    $parity = strlen($cardNumber) % 2;
    
    for ($i = 0; $i < strlen($cardNumber); $i++) {
        $digit = (int)$cardNumber[$i];
        if ($i % 2 == $parity) {
            $digit *= 2;
        }
        if ($digit > 9) {
            $digit -= 9;
        }
        $sum += $digit;
    }
    
    return ($sum % 10) == 0;
}

/**
 * Valide une date d'expiration de carte (format MM/YY)
 * @param string $expiryDate Date au format MM/YY
 * @return bool true si valide et non expirée
 */
function validate_card_expiry($expiryDate) {
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiryDate)) {
        return false;
    }
    
    list($month, $year2) = explode('/', $expiryDate);
    $fullYear = 2000 + (int)$year2;
    $currentMonth = (int)date('m');
    $currentYear = (int)date('Y');
    
    // Vérifier que la carte n'est pas expirée
    if ($fullYear < $currentYear || ($fullYear == $currentYear && $month < $currentMonth)) {
        return false;
    }
    
    return true;
}

/**
 * Valide un code CVV (3 ou 4 chiffres)
 * @param string $cvv Code CVV
 * @return bool true si valide
 */
function validate_cvv($cvv) {
    $cvv = preg_replace('/\D/', '', $cvv);
    return preg_match('/^[0-9]{3,4}$/', $cvv) !== 0;
}

// ==========================================
// VALIDATION FICHIERS
// ==========================================

/**
 * Valide un upload de fichier image
 * @param array $fileArray $_FILES['key']
 * @param int $maxSize Taille maximale en bytes
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validate_image_upload($fileArray, $maxSize = null) {
    if (!is_array($fileArray)) {
        return ['valid' => false, 'error' => 'Pas d\'upload détecté.'];
    }
    
    if ($maxSize === null) {
        $maxSize = MAX_UPLOAD_SIZE;
    }
    
    // Vérifier le code d'erreur
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'Erreur lors de l\'upload.'];
    }
    
    // Vérifier l'extension
    $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXTENSIONS)) {
        return ['valid' => false, 'error' => 'Extension non autorisée.'];
    }
    
    // Vérifier la taille
    if ($fileArray['size'] > $maxSize) {
        return ['valid' => false, 'error' => 'Fichier trop volumineux.'];
    }
    
    // Vérifier la VRAIE MIME type (côté serveur)
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $real_mime = finfo_file($finfo, $fileArray['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($real_mime, ALLOWED_IMAGE_MIMES)) {
            return ['valid' => false, 'error' => 'Type de fichier invalide.'];
        }
    }
    
    return ['valid' => true, 'error' => null, 'extension' => $ext];
}

// ==========================================
// FORMATAGE AFFICHAGE
// ==========================================

/**
 * Formate un prix pour l'affichage (€)
 * @param float $price Prix
 * @param bool $withCurrency Inclure le symbole €
 * @return string Prix formaté
 */
function format_price($price, $withCurrency = true) {
    $formatted = number_format((float)$price, 2, ',', ' ');
    return $withCurrency ? $formatted . ' €' : $formatted;
}

/**
 * Formate une date pour l'affichage
 * @param string $date Date au format YYYY-MM-DD ou timestamp
 * @param string $format Format de sortie (défaut: d/m/Y)
 * @return string Date formatée
 */
function format_date($date, $format = 'd/m/Y') {
    if (is_numeric($date)) {
        return date($format, (int)$date);
    }
    return date($format, strtotime($date));
}

// ==========================================
// NOTIFICATIONS
// ==========================================

/**
 * Crée une notification pour le frontend (via JSON AJAX)
 * @param bool $success true = succès, false = erreur
 * @param string $message Message à afficher
 * @param array $extra Données additionnelles
 * @return array Notification formatée
 */
function create_json_response($success, $message, $extra = []) {
    return array_merge([
        'success' => (bool)$success,
        'message' => escape_html($message),
        'timestamp' => time()
    ], $extra);
}

// ==========================================
// SÉCURITÉ AVANCÉE
// ==========================================

/**
 * Génère un jeton sécurisé pour les formulaires / URLs
 * @param int $length Longueur du jeton (bytes)
 * @return string Jeton hexadécimal
 */
function generate_secure_token($length = 16) {
    return bin2hex(random_bytes($length));
}

/**
 * Valide qu'une quantité de produit est acceptable
 * @param int $quantity Quantité à vérifier
 * @param int $max Quantité maximale
 * @return bool true si acceptable
 */
function validate_quantity($quantity, $max = CART_ITEM_MAX_QUANTITY) {
    $qty = (int)$quantity;
    return $qty > 0 && $qty <= $max;
}

// ==========================================
// RATE LIMITING - Prévenir brute force
// ==========================================

/**
 * Vérifie et enregistre les tentatives (rate limiting)
 * Prévient les attaques par brute force
 * @param string $key Clé unique (ex: "login_admin")
 * @param int $maxAttempts Nombre max de tentatives
 * @param int $windowSeconds Fenêtre de temps
 * @return array ['allowed' => bool, 'attempts' => int, 'wait_seconds' => int]
 */
function check_rate_limit($key, $maxAttempts = 5, $windowSeconds = 300) {
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    
    $currentTime = time();
    $sessionKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION['rate_limit'][$sessionKey])) {
        $_SESSION['rate_limit'][$sessionKey] = [
            'attempts' => 0,
            'window_start' => $currentTime,
            'blocked_until' => 0
        ];
    }
    
    $record = &$_SESSION['rate_limit'][$sessionKey];
    
    // Si bloqué
    if ($currentTime < $record['blocked_until']) {
        $waitSeconds = $record['blocked_until'] - $currentTime;
        return [
            'allowed' => false,
            'attempts' => $record['attempts'],
            'wait_seconds' => $waitSeconds
        ];
    }
    
    // Si fenêtre expirée
    if ($currentTime - $record['window_start'] > $windowSeconds) {
        $record['attempts'] = 0;
        $record['window_start'] = $currentTime;
        $record['blocked_until'] = 0;
    }
    
    $record['attempts']++;
    
    // Si limite atteinte
    if ($record['attempts'] > $maxAttempts) {
        $record['blocked_until'] = $currentTime + 300;
        error_log("Rate limit dépassé: $key");
        return [
            'allowed' => false,
            'attempts' => $record['attempts'],
            'wait_seconds' => 300
        ];
    }
    
    return [
        'allowed' => true,
        'attempts' => $record['attempts'],
        'wait_seconds' => 0
    ];
}

/**
 * Réinitialise le rate limit après succès
 * @param string $key Clé du rate limit
 */
function reset_rate_limit($key) {
    $sessionKey = 'rate_limit_' . md5($key);
    if (isset($_SESSION['rate_limit'][$sessionKey])) {
        unset($_SESSION['rate_limit'][$sessionKey]);
    }
}

/**
 * Enregistre un événement de sécurité dans la table d'audit.
 * @param string $action
 * @param string $resourceType
 * @param string|int|null $resourceId
 * @param mixed $oldValues
 * @param mixed $newValues
 * @return bool
 */
function log_audit_event($action, $resourceType, $resourceId = null, $oldValues = null, $newValues = null) {
    global $pdo;

    if (!isset($pdo) || !($pdo instanceof PDO)) {
        return false;
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO audit_log (action, resource_type, resource_id, actor, ip_address, old_values, new_values)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $action,
            $resourceType,
            $resourceId !== null ? (string)$resourceId : null,
            $_SESSION['admin_connecte'] ? 'admin' : 'public',
            $_SERVER['REMOTE_ADDR'] ?? null,
            $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
        ]);

        return true;
    } catch (Throwable $e) {
        error_log('Audit log error: ' . $e->getMessage());
        return false;
    }
}
