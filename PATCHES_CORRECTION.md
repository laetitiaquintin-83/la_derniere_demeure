# 🔧 PATCHES DE CORRECTION - La Dernière Demeure

Ce document fournit les patches de code prêts à appliquer pour corriger les vulnérabilités identifiées.

---

## 📋 Table des Corrections

1. [Correction Base de Données](#1-sécuriser-la-base-de-données)
2. [Correction Rate Limiting](#2-ajouter-rate-limiting)
3. [Correction CSRF Contact](#3-ajouter-csrf-à-contactphp)
4. [Correction CSRF Repos](#4-ajouter-csrf-à-traitement_jardinphp)
5. [Correction Headers Sécurité](#5-ajouter-headers-de-sécurité-globaux)
6. [Correction XSS Modifier](#6-corriger-xss-dans-modifierphp)
7. [Correction MIME Modifier](#7-améliorer-validation-mime-dans-modifierphp)
8. [Correction Paiement](#8-corriger-traitement_paiementphp)
9. [Correction Audit Trail](#9-implémenter-audit-trail)
10. [Correction Logout CSRF](#10-ajouter-csrf-à-logoutphp)

---

## 1. Sécuriser la Base de Données

### Étape 1: Créer fichier `.env`

**Nouveau fichier:** `.env`

```ini
# Configuration Base de Données
DB_HOST=localhost
DB_USER=demeure_user
DB_PASSWORD=VotreMotDePasseForte123!@#
DB_NAME=la_derniere_demeure

# Configuration Application
APP_ENV=production
APP_DEBUG=false
SESSION_LIFETIME=3600

# Clés de Sécurité (si utilisation d'API)
RECAPTCHA_SITEKEY=votre_sitekey
RECAPTCHA_SECRET=votre_secret
STRIPE_PUBLIC_KEY=pk_...
STRIPE_SECRET_KEY=sk_...
```

**Nouveau fichier:** `.env.example` (pour Git)

```ini
# Configuration Base de Données
DB_HOST=localhost
DB_USER=demeure_user
DB_PASSWORD=
DB_NAME=la_derniere_demeure

# Configuration Application
APP_ENV=production
APP_DEBUG=false
SESSION_LIFETIME=3600

# Clés de Sécurité
RECAPTCHA_SITEKEY=
RECAPTCHA_SECRET=
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
```

### Étape 2: Modifier `config.php`

**Avant:**

```php
<?php
// config.php - Configuration centralisée du projet

// 0. Charger les constantes et fonctions utilitaires
require_once __DIR__ . '/constantes.php';
require_once __DIR__ . '/helpers.php';

// 1. Sécurisation des cookies de session (AVANT le session_start)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// 1.1. Initialiser admin_connecte à false si non défini (garantit un visiteur par défaut)
if (!isset($_SESSION['admin_connecte'])) {
    $_SESSION['admin_connecte'] = false;
}

// 2. Connexion BDD
$host   = 'localhost';
$dbname = 'la_derniere_demeure';
$user   = 'root';
$pass   = '';
```

**Après:**

```php
<?php
// config.php - Configuration centralisée du projet

// ==========================================
// CHARGER LES CONSTANTES ET ENVIRONNEMENT
// ==========================================

// 0. Charger le fichier .env
if (file_exists(__DIR__ . '/.env')) {
    $env_file = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_file as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// 0.1 Charger les constantes et fonctions utilitaires
require_once __DIR__ . '/constantes.php';
require_once __DIR__ . '/helpers.php';

// ==========================================
// CONFIGURATION DE SÉCURITÉ GLOBALE
// ==========================================

// 0.2 Désactiver l'affichage des erreurs en production
if ($_ENV['APP_ENV'] === 'production') {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/error.log');
}

// 1. Sécurisation des cookies de session (AVANT le session_start)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);      // ✅ HTTPOnly
    ini_set('session.use_only_cookies', 1);     // ✅ Cookies uniquement
    ini_set('session.cookie_samesite', 'Lax');  // ✅ SameSite
    ini_set('session.gc_maxlifetime', $_ENV['SESSION_LIFETIME'] ?? 3600);
    session_start();
}

// 1.1. Initialiser admin_connecte à false si non défini (garantit un visiteur par défaut)
if (!isset($_SESSION['admin_connecte'])) {
    $_SESSION['admin_connecte'] = false;
}

// ==========================================
// HEADERS DE SÉCURITÉ
// ==========================================

// 2. Ajouter headers de sécurité HTTP
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CSP (Content Security Policy)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; img-src 'self' data: https:; connect-src 'self'; frame-src https://www.google.com/recaptcha/;");

// HSTS (activer en production avec HTTPS)
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

// ==========================================
// CONNEXION BASE DE DONNÉES
// ==========================================

// 3. Connexion BDD avec variables d'environnement
$host   = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'la_derniere_demeure';
$user   = $_ENV['DB_USER'] ?? 'demeure_user';
$pass   = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
```

### Étape 3: Créer l'utilisateur MySQL

**SQL à exécuter dans MySQL:**

```sql
-- Créer l'utilisateur dédié
CREATE USER 'demeure_user'@'localhost' IDENTIFIED BY 'VotreMotDePasseForte123!@#';

-- Accorder les droits (SANS GRANT OPTION)
GRANT SELECT, INSERT, UPDATE, DELETE ON la_derniere_demeure.* TO 'demeure_user'@'localhost';

-- Rafraîchir les privilèges
FLUSH PRIVILEGES;

-- Vérifier que l'utilisateur a été créé
SELECT USER, HOST FROM mysql.user WHERE USER = 'demeure_user';
```

---

## 2. Ajouter Rate Limiting

### Nouveau fichier: `rate_limiter.php`

```php
<?php
/**
 * rate_limiter.php - Système de limitation de taux
 * Empêche les abus (spam, brute force, etc.)
 */

/**
 * Vérifier et enregistrer une action (rate limiting par IP)
 * @param string $action_type Type d'action (contact, login, upload)
 * @param int $limit Nombre de requêtes autorisées
 * @param int $window Fenêtre de temps en secondes
 * @return bool true si action autorisée, false si limite atteinte
 */
function rate_limit_check($action_type, $limit = 5, $window = 300) {
    // Clé unique basée sur l'IP et l'action
    $key = "rate_limit_" . md5($action_type . $_SERVER['REMOTE_ADDR']);

    // Initialiser ou récupérer le compteur
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 0,
            'reset_time' => time() + $window
        ];
    }

    // Réinitialiser si la fenêtre de temps est expirée
    if (time() > $_SESSION[$key]['reset_time']) {
        $_SESSION[$key] = [
            'count' => 0,
            'reset_time' => time() + $window
        ];
    }

    // Vérifier si la limite est atteinte
    if ($_SESSION[$key]['count'] >= $limit) {
        return false;  // Limite atteinte
    }

    // Incrémenter le compteur
    $_SESSION[$key]['count']++;

    return true;  // Action autorisée
}

/**
 * Obtenir le nombre de tentatives restantes
 * @param string $action_type Type d'action
 * @param int $limit Limite totale
 * @return int Nombre de tentatives restantes
 */
function rate_limit_remaining($action_type, $limit = 5) {
    $key = "rate_limit_" . md5($action_type . $_SERVER['REMOTE_ADDR']);
    $count = $_SESSION[$key]['count'] ?? 0;
    return max(0, $limit - $count);
}

/**
 * Obtenir le temps d'attente restant en secondes
 * @param string $action_type Type d'action
 * @return int Secondes avant réinitialisation
 */
function rate_limit_wait_time($action_type) {
    $key = "rate_limit_" . md5($action_type . $_SERVER['REMOTE_ADDR']);
    if (!isset($_SESSION[$key])) return 0;
    return max(0, $_SESSION[$key]['reset_time'] - time());
}
```

### Modifier `contact.php`

**Avant:**

```php
<?php
session_start();
require_once 'config.php';

// ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pour l'instant, on simule l'envoi.
    $message_succes = "Votre message a été confié à notre scénographe. Nous vous répondrons avec la plus grande discrétion.";
}
```

**Après:**

```php
<?php
session_start();
require_once 'config.php';
require_once 'rate_limiter.php';

// ...

$message_succes = '';
$message_erreur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Vérifier le rate limiting
    if (!rate_limit_check('contact_form', 3, 300)) {  // Max 3 soumissions par 5 minutes
        $wait_time = rate_limit_wait_time('contact_form');
        $message_erreur = "Trop de soumissions. Veuillez réessayer dans " . intval($wait_time) . " secondes.";
    }
    // 2. Vérifier le token CSRF
    elseif (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        $message_erreur = "Erreur de sécurité : jeton invalide.";
    }
    // 3. Valider les données
    elseif (empty($_POST['email']) || empty($_POST['message'])) {
        $message_erreur = "Tous les champs sont obligatoires.";
    }
    // 4. Valider le format email
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message_erreur = "Adresse email invalide.";
    }
    else {
        // Traiter le formulaire
        $email = htmlspecialchars($_POST['email']);
        $nom = htmlspecialchars($_POST['nom'] ?? 'Non spécifié');
        $message_text = htmlspecialchars($_POST['message']);

        // Envoyer l'email (ou enregistrer en base)
        // TODO: Implémenter l'envoi d'email

        $message_succes = "Votre message a été confié à notre scénographe. Nous vous répondrons avec la plus grande discrétion.";
    }
}
```

**Dans le formulaire HTML:**

```html
<?php if (!empty($message_erreur)): ?>
<div class="error-msg"><?php echo $message_erreur; ?></div>
<?php endif; ?>

<form method="POST">
  <input
    type="hidden"
    name="csrf_token"
    value="<?php echo genererTokenCSRF(); ?>"
  />

  <!-- Ajouter champs -->
  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" required />
  </div>

  <div class="form-group">
    <label>Message</label>
    <textarea name="message" required></textarea>
  </div>

  <button type="submit">Envoyer</button>
</form>
```

---

## 3. Ajouter CSRF à Contact.php

_Voir patch #2 ci-dessus (déjà inclus)_

---

## 4. Ajouter CSRF à traitement_jardin.php

**Avant:**

```php
<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_p = htmlspecialchars($_POST['nom_proprietaire']);
    $nom_a = htmlspecialchars($_POST['nom_animal']);
    $msg = htmlspecialchars($_POST['message']);
    // ... upload de photo
    $sql = "INSERT INTO livre_dor_animaux (nom_proprietaire, nom_animal, message, photo_path) VALUES (?, ?, ?, ?)";
```

**Après:**

```php
<?php
require_once 'config.php';
require_once 'rate_limiter.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Vérifier rate limiting
    if (!rate_limit_check('animal_tribute', 5, 600)) {  // Max 5 soumissions par 10 min
        die(json_encode(['success' => false, 'message' => 'Trop de soumissions. Réessayez plus tard.'], true));
    }

    // 2. Vérifier CSRF
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        die(json_encode(['success' => false, 'message' => 'Erreur de sécurité CSRF.']));
    }

    // 3. Valider les données
    if (empty($_POST['nom_proprietaire']) || empty($_POST['nom_animal']) || empty($_POST['message'])) {
        die(json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']));
    }

    // 4. Traiter le formulaire
    $nom_p = htmlspecialchars(trim($_POST['nom_proprietaire']), ENT_QUOTES, 'UTF-8');
    $nom_a = htmlspecialchars(trim($_POST['nom_animal']), ENT_QUOTES, 'UTF-8');
    $msg = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');
    // ... reste du code
```

**Dans le formulaire HTML:**

```html
<form method="POST" enctype="multipart/form-data">
  <input
    type="hidden"
    name="csrf_token"
    value="<?php echo genererTokenCSRF(); ?>"
  />
  <!-- Autres champs du formulaire -->
</form>
```

---

## 5. Ajouter Headers de Sécurité Globaux

_Voir patch #1 ci-dessus (déjà inclus dans config.php)_

---

## 6. Corriger XSS dans modifier.php

**Avant:**

```php
$nom = trim($_POST['nom'] ?? '');
$categorie = trim($_POST['categorie'] ?? '');
$essence_bois = trim($_POST['essence_bois'] ?? '');
$couleur_velours = trim($_POST['couleur_velours'] ?? '');
$prix = $_POST['prix'] ?? 0;
$description = trim($_POST['description'] ?? '');

// ... validation ...

$sql = "UPDATE catalogue_funeraire SET nom=?, categorie=?, essence_bois=?, couleur_velours=?, prix=?, description=?, image_path=? WHERE id=?";
```

**Après:**

```php
// ==========================================
// RÉCUPÉRATION ET ÉCHAPPEMENT DES DONNÉES
// ==========================================

$nom = htmlspecialchars(trim($_POST['nom'] ?? ''), ENT_QUOTES, 'UTF-8');
$categorie = htmlspecialchars(trim($_POST['categorie'] ?? ''), ENT_QUOTES, 'UTF-8');
$essence_bois = htmlspecialchars(trim($_POST['essence_bois'] ?? ''), ENT_QUOTES, 'UTF-8');
$couleur_velours = htmlspecialchars(trim($_POST['couleur_velours'] ?? ''), ENT_QUOTES, 'UTF-8');
$prix = (float)($_POST['prix'] ?? 0);
$description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');

// ... validation ...

// Les données sont maintenant sûres pour la base de données
$sql = "UPDATE catalogue_funeraire SET nom=?, categorie=?, essence_bois=?, couleur_velours=?, prix=?, description=?, image_path=? WHERE id=?";
```

---

## 7. Améliorer Validation MIME dans modifier.php

**Avant:**

```php
elseif (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
    $target_dir = "images/catalogue/";
    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $file_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
    $target_file = $target_dir . $file_name;

    // Validation simple
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($file_extension, $allowed) && $_FILES["image"]["size"] < 5000000) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // ...
        }
    }
}
```

**Après:**

```php
elseif (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
    $target_dir = "images/catalogue/";
    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $file_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
    $target_file = $target_dir . $file_name;

    // ==========================================
    // VALIDATION COMPLÈTE DE FICHIER
    // ==========================================

    // 1. Vérifier l'extension
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($file_extension, $allowed_extensions)) {
        die("Erreur : Extension non autorisée. Acceptés: jpg, jpeg, png, webp");
    }

    // 2. Vérifier la taille
    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        die("Erreur : Fichier trop volumineux (max 5MB).");
    }

    // 3. Vérifier le VRAI type MIME (côté serveur)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $real_mime = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
    finfo_close($finfo);

    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($real_mime, $allowed_mimes)) {
        die("Erreur de sécurité : Le fichier uploadé n'est pas une image valide (MIME détecté: $real_mime).");
    }

    // 4. Vérifier la traversée répertoire
    $real_path = realpath($target_dir) . DIRECTORY_SEPARATOR . $file_name;
    if (strpos($real_path, realpath($target_dir)) !== 0) {
        die("Erreur de sécurité : Chemin invalide.");
    }

    // 5. Upload sécurisé
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $real_path)) {
        $ancienne_image = $produit['image_path'];
        $image_path = $target_file;
        $nouvelle_image_uploadee = true;
    } else {
        die("Erreur lors du téléchargement de l'image.");
    }
}
```

---

## 8. Corriger traitement_paiement.php

### ⚠️ IMPORTANT: Ne JAMAIS traiter localement la carte bancaire

**Avant:**

```php
// Validation basique des champs du formulaire
if (empty($_POST['nom_titulaire']) || empty($_POST['numero_carte'])) {
    die("Erreur : Les informations du rituel d'engagement sont incomplètes.");
}

// VALIDATION SÉCURISÉE: Numéro de carte (Luhn algorithm)
$numero_carte = preg_replace('/\D/', '', $_POST['numero_carte'] ?? '');
if (!valideeLuhn($numero_carte)) {
    die("Erreur de sécurité : Numéro de carte invalide (non conforme Luhn).");
}
```

**Après (Solution A: Retirer stockage des cartes):**

```php
// ==========================================
// TRAITEMENT SÉCURISÉ DU PAIEMENT
// ==========================================

// 1. Vérifier le panier
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

// 2. Calculer le montant total (relire depuis BD pour plus de sécurité)
$total = 0;
$stmt_check = $pdo->prepare("SELECT SUM(prix * ?) as montant FROM catalogue_funeraire WHERE id = ?");
foreach ($_SESSION['panier'] as $id => $quantite) {
    $stmt_check->execute([$quantite, $id]);
    $row = $stmt_check->fetch();
    if ($row) $total += $row['montant'];
}

// 3. Créer une commande en attente (sans données carte)
$ordonnance_id = uniqid('order_');
$order_stmt = $pdo->prepare("
    INSERT INTO orders (order_id, user_name, total_amount, status, created_at)
    VALUES (?, ?, ?, 'pending', NOW())
");
$order_stmt->execute([
    $ordonnance_id,
    htmlspecialchars($_POST['nom_titulaire']),
    $total
]);

// 4. Générer un token de paiement sécurisé (optionnel, pour tracking)
$payment_token = bin2hex(random_bytes(32));
$_SESSION['payment_token_' . $ordonnance_id] = $payment_token;

// 5. Rediriger vers le processeur de paiement
// Voir la section Stripe ci-dessous pour l'intégration
```

**Solution B: Intégrer Stripe (Recommandé):**

```php
// INSTALLATION: composer require stripe/stripe-php

require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

try {
    // 1. Tarification (passer en centimes pour Stripe)
    $total_cents = (int)($total * 100);

    // 2. Créer une intention de paiement
    $payment_intent = \Stripe\PaymentIntent::create([
        'amount' => $total_cents,
        'currency' => 'eur',
        'payment_method_types' => ['card'],
        'metadata' => [
            'order_id' => uniqid('order_'),
            'customer_name' => htmlspecialchars($_POST['nom_titulaire'])
        ]
    ]);

    // 3. Retourner le client_secret au frontend
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'client_secret' => $payment_intent->client_secret
    ]);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("Erreur Stripe: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du traitement du paiement.'
    ]);
    exit;
}
```

**Frontend JavaScript (avec Stripe):**

```html
<form id="payment-form">
  <input
    type="hidden"
    name="csrf_token"
    value="<?php echo genererTokenCSRF(); ?>"
  />

  <div id="card-element"></div>
  <button id="submit-btn" type="button">Payer</button>
</form>

<script src="https://js.stripe.com/v3/"></script>
<script>
  const stripe = Stripe('<?php echo getenv('STRIPE_PUBLIC_KEY'); ?>');
  const elements = stripe.elements();
  const cardElement = elements.create('card');
  cardElement.mount('#card-element');

  document.getElementById('submit-btn').addEventListener('click', async (e) => {
      e.preventDefault();

      // Créer le payment method depuis la carte
      const { paymentMethod, error } = await stripe.createPaymentMethod({
          type: 'card',
          card: cardElement
      });

      if (error) {
          console.error(error.message);
      } else {
          // Envoyer le paymentMethod au serveur pour confirmer le paiement
          // (voir backend pour confirmPaymentIntent)
      }
  });
</script>
```

---

## 9. Implémenter Audit Trail

### Nouveau fichier: `audit.php`

```php
<?php
/**
 * audit.php - Système d'audit pour tracer toutes les modifications
 */

// Créer la table d'audit (une seule fois)
function init_audit_table($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS audit_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT,
        action VARCHAR(50) NOT NULL,
        resource_type VARCHAR(50) NOT NULL,
        resource_id INT,
        old_values LONGTEXT,
        new_values LONGTEXT,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_action (action),
        INDEX idx_resource (resource_type, resource_id),
        INDEX idx_created (created_at)
    )");
}

/**
 * Enregistrer une action pour l'audit
 * @param PDO $pdo
 * @param string $action CREATE, READ, UPDATE, DELETE
 * @param string $resource_type Type de ressource (produit, categorie, etc)
 * @param int $resource_id ID de la ressource
 * @param array|null $old_values Anciennes valeurs (pour UPDATE)
 * @param array|null $new_values Nouvelles valeurs
 */
function log_audit($pdo, $action, $resource_type, $resource_id, $old_values = null, $new_values = null) {
    try {
        $admin_id = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        $stmt = $pdo->prepare("
            INSERT INTO audit_log
            (admin_id, action, resource_type, resource_id, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $admin_id,
            $action,
            $resource_type,
            $resource_id,
            $old_values ? json_encode($old_values) : null,
            $new_values ? json_encode($new_values) : null,
            $ip,
            $user_agent
        ]);

        return true;
    } catch (Exception $e) {
        error_log("Erreur audit: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupérer l'historique des modifications d'une ressource
 * @param PDO $pdo
 * @param string $resource_type Type de ressource
 * @param int $resource_id ID de la ressource
 * @param int $limit Limite de résultats
 * @return array Historique
 */
function get_audit_history($pdo, $resource_type, $resource_id, $limit = 50) {
    $stmt = $pdo->prepare("
        SELECT * FROM audit_log
        WHERE resource_type = ? AND resource_id = ?
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$resource_type, $resource_id, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

### Utiliser dans gestion.php

**Modifier gestion.php pour tracer les modifications:**

```php
<?php
require_once 'config.php';
require_once 'audit.php';

// Vérifier l'authentification
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// ... code existant ...

// Récupérer tous les produits
$query = $pdo->prepare("SELECT id, nom, categorie, prix, stock, image_path FROM catalogue_funeraire ORDER BY id DESC");
$query->execute();
$produits = $query->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque produit, ajouter l'historique des modifications
$produits_with_history = [];
foreach ($produits as $produit) {
    $produit['history'] = get_audit_history($pdo, 'produit', $produit['id'], 5);
    // Prendre les 5 dernières modifications
    $produits_with_history[] = $produit;
}
```

### Tracer les modifications dans modifier.php

**Avant de faire UPDATE:**

```php
// LOG: Récupérer les anciennes valeurs
$old_stmt = $pdo->prepare("SELECT * FROM catalogue_funeraire WHERE id = ?");
$old_stmt->execute([$id]);
$old_values = $old_stmt->fetch();

// ... faire les modifications ...

// LOG: Après UPDATE réussi
log_audit($pdo, 'UPDATE', 'produit', $id, $old_values, [
    'nom' => $nom,
    'categorie' => $categorie,
    'prix' => $prix,
    'stock' => $stock ?? null  // Selon ce qui a changé
]);
```

---

## 10. Ajouter CSRF à logout.php

**Avant:**

```php
<?php
session_start();
// On détruit toutes les données de la session (la clé d'accès disparaît)
session_destroy();

// On renvoie vers la page d'accueil
header('Location: index.php');
exit;
?>
```

**Après:**

```php
<?php
require_once 'config.php';

// La session est déjà démarrée dans config.php
// Mais on la vérifie quand même
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// GET: Afficher page de confirmation
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Afficher formulaire de confirmation
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Confirmer la déconnexion | La Dernière Demeure</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="admin-body">
        <div class="admin-container">
            <h1 class="admin-title">Confirmer la Déconnexion</h1>

            <p style="text-align: center; color: #b3b3b3;">
                Êtes-vous certain de vouloir quitter le registre de la crypte?
            </p>

            <form method="POST" style="text-align: center;">
                <input type="hidden" name="csrf_token" value="<?php echo genererTokenCSRF(); ?>">

                <button type="submit" class="btn-gold" style="margin: 20px; padding: 10px 30px;">
                    Confirmer Déconnexion
                </button>

                <a href="gestion.php" class="btn-gold" style="margin: 20px; padding: 10px 30px;">
                    Annuler
                </a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ==========================================
// POST: Traiter la déconnexion
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        die("Erreur de sécurité CSRF. Déconnexion annulée.");
    }

    // LOG: Enregistrer la déconnexion
    error_log("Utilisateur connecté en tant que admin s'est déconnecté de l'IP " . $_SERVER['REMOTE_ADDR']);

    // Détruire la session
    session_destroy();

    // Rediriger vers l'accueil
    header('Location: index.php');
    exit;
}

// Méthode non autorisée
http_response_code(405);
die("Méthode non autorisée.");
?>
```

---

## 📋 CHECKLIST D'IMPLÉMENTATION

- [ ] Créer fichier `.env` et `.env.example`
- [ ] Modifier `config.php` pour charger .env et ajouter headers
- [ ] Créer utilisateur MySQL dédié
- [ ] Créer fichier `rate_limiter.php`
- [ ] Modifier `contact.php` pour ajouter rate limiting et CSRF
- [ ] Modifier `traitement_jardin.php` pour ajouter CSRF
- [ ] Modifier `modifier.php` pour corriger XSS et MIME
- [ ] Remplacer traitement paiement (utiliser Stripe)
- [ ] Créer fichier `audit.php`
- [ ] Modifier `gestion.php` pour utiliser l'audit
- [ ] Modifier `logout.php` pour ajouter CSRF
- [ ] Ajouter `/.env` au `.gitignore`
- [ ] Tester tous les formulaires
- [ ] Vérifier les logs d'erreur
- [ ] Faire une analyse de sécurité complète (après implémentation)

---

**End of patches document.**
