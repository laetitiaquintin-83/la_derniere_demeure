# 🔧 GUIDE CORRECTION RAPIDE - Priorités FIX

## ⏱️ Temps total recommandé: 6-8 heures

---

# CRITIQUE #1: Validation Données Paiement (1h)

**Fichier à modifier**: `traitement_paiement.php`

**Ajouter avant ligne 10:**

```php
<?php
// À AJOUTER en top de traitement_paiement.php

function valider_paiement($data) {
    $erreurs = [];

    // 1. Titulaire: alphabétique + espaces (Dupont, Jean-Marie, O'Brien)
    $nom = trim($data['nom_titulaire'] ?? '');
    if (empty($nom) || !preg_match("/^[a-zA-ZÀ-ÿ\s\-']{2,100}$/u", $nom)) {
        $erreurs['nom'] = "Nom invalide: alphabétique, tirets, espaces autorisés";
    }

    // 2. Numéro carte: 16 chiffres exactement
    $carte = preg_replace('/\s+/', '', $data['numero_carte'] ?? '');
    if (!preg_match("/^\d{16}$/", $carte)) {
        $erreurs['carte'] = "Numéro carte: 16 chiffres requis";
    } else {
        // Luhn algorithm optionnel (valider checksum)
        if (!luhn_check($carte)) {
            $erreurs['carte'] = "Numéro carte invalide (checksum)";
        }
    }

    // 3. Date expiration: MM/YY format, date future
    $date_exp = $data['date_expiration'] ?? '';
    if (!preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $date_exp)) {
        $erreurs['expiration'] = "Format: MM/YY requis";
    } else {
        list($mois, $an) = explode('/', $date_exp);
        $an_complet = 2000 + (int)$an;  // "25" → 2025
        $date_obj = new DateTime("$an_complet-$mois-01");
        if ($date_obj < new DateTime('now')) {
            $erreurs['expiration'] = "Carte expirée";
        }
    }

    // 4. CVV: 3-4 chiffres
    $cvv = $data['cvv'] ?? '';
    if (!preg_match("/^\d{3,4}$/", $cvv)) {
        $erreurs['cvv'] = "CVV: 3-4 chiffres requis";
    }

    return $erreurs;
}

// Luhn algorithm: valider numéro de carte
function luhn_check($num) {
    $sum = 0;
    $numlen = strlen($num);
    for ($i = 0; $i < $numlen; $i++) {
        $digit = (int)$num[$i];
        if (($numlen - $i) % 2 == 0) {
            $digit *= 2;
            if ($digit > 9) $digit -= 9;
        }
        $sum += $digit;
    }
    return ($sum % 10) == 0;
}
?>
```

**Ensuite, modifier le traitement:**

```php
// REMPLACER ligne ~15 (after CSRF check):

// ❌ AVANT:
// if (empty($_POST['nom_titulaire']) || empty($_POST['numero_carte'])) {
//     die("Erreur : Les informations du rituel d'engagement sont incomplètes.");
// }

// ✅ APRÈS:
$erreurs_validation = valider_paiement($_POST);
if (!empty($erreurs_validation)) {
    // Log erreur
    error_log("Paiement invalide: " . json_encode($erreurs_validation));
    echo json_encode([
        'success' => false,
        'erreurs' => $erreurs_validation
    ]);
    exit;
}
```

---

# CRITIQUE #2: Hash Admin en DB (2h)

## Étape 1: Créer table SQL

**Exécuter en PHPMyAdmin ou CLI:**

```sql
USE la_derniere_demeure;

-- 1. Créer table admin_users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Générer hash bcrypt pour "cerbere"
-- En ligne de commande PHP:
-- php -r "echo password_hash('cerbere', PASSWORD_BCRYPT, ['cost' => 10]);"
-- Output: $2y$10$VOTRE_HASH_ICI

-- 3. Insérer admin user
INSERT INTO admin_users (username, password_hash)
VALUES ('cerbere', '$2y$10$dQ04JR2zzMidalMeBMeMiuNgBnSaJBv/PNRYq2fxptuFmGnl1JDO2');
    -- Note: Ce hash est du code de démo! Générer le vôtre avec PHP

-- 4. Index pour perf
CREATE INDEX idx_username ON admin_users(username);
```

---

## Étape 2: Modifier login.php

**REMPLACER tout le contenu:**

```php
<?php
require_once 'config.php';

// Pas de hash en code source!
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Vérification CSRF
    $token_soumis = $_POST['csrf_token'] ?? '';
    if (!validerTokenCSRF($token_soumis)) {
        die("Erreur de sécurité : Jeton invalide.");
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';

    // 2. Valider données
    if (empty($username) || empty($password)) {
        $erreur = "Identifiants requis.";
    } else {
        // 3. Chercher en DB
        try {
            $stmt = $pdo->prepare("SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 4. Vérifier password
            if ($user && password_verify($password, $user['password_hash'])) {
                // Succès: régénérer session
                session_regenerate_id(true);
                $_SESSION['admin_connecte'] = true;
                $_SESSION['admin_id'] = $user['id'];

                // Logger login
                $update_stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$user['id']]);

                header('Location: admin.php');
                exit;
            } else {
                // Échec: rate limiting (anti brute-force)
                sleep(2);  // Ralentir attaquant
                $erreur = "Accès refusé. Identifiants invalides.";
                error_log("Failed login attempt for username: $username");
            }
        } catch (PDOException $e) {
            error_log("Database error in login: " . $e->getMessage());
            $erreur = "Erreur serveur. Veuillez réessayer.";
        }
    }
}

// Le reste du HTML reste identique
?>
<!DOCTYPE html>
<html lang="fr">
<!-- ... même HTML que avant ... -->
<?php endif; ?>
```

**Note**: Récupérer le hash de 'cerbere' via CLI:

```bash
php -r "echo password_hash('cerbere', PASSWORD_BCRYPT, ['cost' => 10]);"
# $ php -r "echo password_hash('cerbere', PASSWORD_BCRYPT, ['cost' => 10]);"
# $2y$10$g4b7j.qM8tjFXDsA5g1efOnAg5hrtHmUPYAituen0cM4BPzGcA7Aa
```

---

# CRITIQUE #3: XSS - htmlspecialchars() Inconsistent (2h)

## Audit des fichiers:

**Fichier**: `index.php` - Ligne 50 (Catégories)

```php
// ❌ AVANT:
<a href="?cat=<?php echo urlencode($cat); ?>#catalogue"
   class="btn-filter <?php echo (isset($_GET['cat']) && $_GET['cat'] === $cat) ? 'active' : ''; ?>">
    <?php echo htmlspecialchars($cat); ?>
</a>

// ✅ APRÈS: Nettoyez IMMÉDIATEMENT $_GET
<?php
// En top de la logique:
$requested_cat = isset($_GET['cat']) ? trim($_GET['cat']) : '';

// Valider contre DB (whitelist)
if (!empty($requested_cat)) {
    $stmt = $pdo->prepare("SELECT categorie FROM catalogue_funeraire WHERE categorie = ? LIMIT 1");
    $stmt->execute([$requested_cat]);
    if (!$stmt->fetch()) {
        $requested_cat = '';  // Catégorie inexistante
    }
}

$categories_to_show = empty($requested_cat) ? $categories_db : [$requested_cat];
?>

<!-- Dans le HTML: -->
<a href="?cat=<?php echo urlencode(htmlspecialchars($cat)); ?>#catalogue"
   class="btn-filter <?php echo isset($_GET['cat']) && $_GET['cat'] === $cat ? 'active' : ''; ?>">
    <?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>
</a>
```

---

**Fichier**: `catalogue.php` - Ligne 80 (Image path)

```php
// ❌ AVANT:
<img src="<?php
    $path = htmlspecialchars($p['image_path']);
    if (strpos($path, 'images/') === 0) {
        $path = substr($path, 7);
    }
    echo $path;
?>" alt="...">

// ✅ APRÈS: Valider avant d'afficher
<?php
// Fonction helper (à mettre dans helpers.php)
function get_safe_image_path($path) {
    // Nettoyer
    $path = trim($path);

    // Valider que c'est dans le bon dossier
    $realpath = realpath($path);
    $uploads_dir = realpath('images/catalogue/');

    if ($realpath === false || strpos($realpath, $uploads_dir) !== 0) {
        return 'images/placeholder.jpg';  // Image par défaut
    }

    return htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
}
?>

<img src="<?php echo get_safe_image_path($p['image_path']); ?>"
     alt="<?php echo htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8'); ?>">
```

---

# CRITIQUE #4: File Upload - Validation Unsafe (1h30)

**Fichier à modifier**: `admin.php` (ligne 55-75)

```php
// ❌ AVANT:
if (!in_array($_FILES["image"]["type"], ['image/jpeg', 'image/png', 'image/webp'])) {
    $message = "Erreur : formats acceptés JPG, PNG ou WebP uniquement.";
    $messageType = "error";
} else {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // ...
    }
}

// ✅ APRÈS:
// 1. Fonction de validation sécurisée
function valider_image_upload($tmp_path, $max_size = 5000000) {
    $erreurs = [];

    // Vérifier existence
    if (!is_uploaded_file($tmp_path)) {
        $erreurs[] = "Fichier invalide";
        return $erreurs;
    }

    // Vérifier taille
    if (filesize($tmp_path) > $max_size) {
        $erreurs[] = "Fichier trop volumineux (max 5MB)";
        return $erreurs;
    }

    // VRAIE validation MIME type (not client-provided!)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp_path);
    finfo_close($finfo);

    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($mime, $allowed_mimes)) {
        $erreurs[] = "Format de fichier invalide: $mime (JPEG, PNG, WebP autorisés)";
        return $erreurs;
    }

    // Vérifier signature fichier (magic bytes)
    $handle = fopen($tmp_path, 'rb');
    $header = fread($handle, 12);
    fclose($handle);

    // JPEG: FF D8 FF
    $jpeg_valid = (substr($header, 0, 3) === "\xFF\xD8\xFF");
    // PNG: 89 50 4E 47
    $png_valid = (substr($header, 0, 4) === "\x89PNG");

    if (!$jpeg_valid && !$png_valid) {
        $erreurs[] = "Signature fichier invalide (fichier corrompu?)";
    }

    return $erreurs;
}

// 2. Utilisation dans admin.php:
$validation_errors = valider_image_upload($_FILES["image"]["tmp_name"]);

if (!empty($validation_errors)) {
    $message = "Validation image échouée: " . implode(', ', $validation_errors);
    $messageType = "error";
} else {
    // 3. Générer nom SÉCURISÉ
    $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $target_file = "images/catalogue/" . $filename;

    // 4. Valider chemin final (anti path traversal)
    $realpath = realpath('images/catalogue/');
    $target_realpath = realpath(dirname($target_file));

    if ($target_realpath !== $realpath) {
        $message = "Chemin de fichier invalide (traversal attempt?)";
        $messageType = "error";
    } else if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Succès!
        $message = "Image uploadée avec succès";
        $messageType = "success";
    } else {
        $message = "Erreur lors du déplacement du fichier";
        $messageType = "error";
    }
}
```

---

# CRITIQUE #5: footer.php Manquant (5min)

**Créer fichier**: `footer.php`

```php
<?php
// footer.php - Pied de page
?>
<footer style="background: #0a0a0a; border-top: 1px solid #D4AF37; padding: 40px 5%; text-align: center; color: #888; margin-top: 80px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <p style="margin-bottom: 20px; font-style: italic; color: #aaa;">
            "Dans l'éternité, la mémoire devient immortelle"
        </p>

        <nav style="margin-bottom: 30px;">
            <a href="index.php" style="color: #D4AF37; margin: 0 15px; text-decoration: none;">Accueil</a>
            <a href="mentions-legales.php" style="color: #D4AF37; margin: 0 15px; text-decoration: none;">Mentions Légales</a>
            <a href="contact.php" style="color: #D4AF37; margin: 0 15px; text-decoration: none;">Contact</a>
        </nav>

        <p style="color: #666; font-size: 0.9em;">
            &copy; 2024 La Dernière Demeure. Tous droits réservés.<br>
            Plateforme funéraire de prestige.
        </p>
    </div>
</footer>
```

---

# CRITIQUE #6: Rate Limiting AJAX (1h)

**Fichier à modifier**: `script.js` (ligne 1-50)

```javascript
// ✅ Ajouter au top de script.js:

const CartManager = {
  lastAddTime: {},
  MAX_PER_ITEM: 10,
  THROTTLE_MS: 500, // 500ms entre 2 adds

  canAdd(itemId) {
    const now = Date.now();
    const last = this.lastAddTime[itemId] || 0;

    // Throttle: pas plus d'1 ajout tous les 500ms
    if (now - last < this.THROTTLE_MS) {
      return { allowed: false, reason: "Trop rapide, attendez..." };
    }

    // Gérer limite max par item
    // (Note: cette limite devrait être côté serveur aussi!)

    this.lastAddTime[itemId] = now;
    return { allowed: true };
  },
};

// Puis dans le event listener (ligne ~20):
boutonsAjout.forEach((bouton) => {
  bouton.addEventListener("click", function (evenement) {
    evenement.preventDefault();

    const idProduit = this.getAttribute("data-id");

    // ✅ Vérifier rate limit
    const check = CartManager.canAdd(idProduit);
    if (!check.allowed) {
      showToast(check.reason, true);
      return;
    }

    // ... reste du fetch ...
  });
});
```

**AUSSI à ajouter**: Limite serveur dans `ajouter_panier.php`:

```php
<?php
// À ajouter après ligne 20 (validation CSRF):

// Rate limiting: max 100 articles total
$current_total = array_sum($_SESSION['panier'] ?? []);
if ($current_total >= 100) {
    echo json_encode([
        'success' => false,
        'message' => 'Limite de panier atteinte (100 articles max)'
    ]);
    exit;
}

// Max 10 du même article
$current_qty = $_SESSION['panier'][$id] ?? 0;
if ($current_qty >= 10) {
    echo json_encode([
        'success' => false,
        'message' => "Maximum 10 de cet article dans le panier"
    ]);
    exit;
}
?>
```

---

# BONUS: Helpers & Constants

**Créer fichier**: `helpers.php`

```php
<?php
// helpers.php - Fonctions utilitaires

// Constants
const SESSION_KEY_ADMIN = 'admin_connecte';
const SESSION_KEY_PANIER = 'panier';
const SESSION_KEY_CSRF = 'csrf_token';
const UPLOAD_DIR = 'images/catalogue/';
const MAX_UPLOAD_SIZE = 5000000;

// Sanitize ID
function sanitize_id($value, $die_on_error = true) {
    if (!ctype_digit(strval($value))) {
        if ($die_on_error) die("ID invalide");
        return null;
    }
    return (int)$value;
}

// Escape HTML
function escape_html($value, $flags = ENT_QUOTES) {
    return htmlspecialchars($value, $flags, 'UTF-8');
}

// Get POST value safely
function get_post($key, $default = '', $sanitize = false) {
    $value = isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    return $sanitize ? escape_html($value) : $value;
}

// Get GET value safely
function get_query($key, $default = '') {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

// Format money
function format_price($amount) {
    return number_format($amount, 2, ',', ' ') . ' €';
}

// Log action
function log_action($category, $message, $data = []) {
    $log_file = 'logs/actions.log';
    if (!is_dir('logs')) mkdir('logs', 0755);

    $timestamp = date('Y-m-d H:i:s');
    $line = "[$timestamp] $category: $message | " . json_encode($data) . "\n";
    file_put_contents($log_file, $line, FILE_APPEND);
}

// Check admin
function check_admin() {
    if (!isset($_SESSION[SESSION_KEY_ADMIN]) || $_SESSION[SESSION_KEY_ADMIN] !== true) {
        header('Location: login.php');
        exit;
    }
}

// Validate CSRF
function check_csrf($token = null) {
    $token = $token ?? ($_POST['csrf_token'] ?? $_GET['token'] ?? '');
    if (!validerTokenCSRF($token)) {
        die("Erreur de sécurité: Token CSRF invalide");
    }
}

?>
```

**Utilisation dans tous les fichiers**:

```php
<?php
// En top du fichier:
require_once 'helpers.php';

// Utiliser:
check_admin();
check_csrf();
$id = sanitize_id($_GET['id']);
echo escape_html($nom);
?>
```

---

# 📋 CHECKLIST DE DÉPLOIEMENT

- [ ] Validation paiement: regex + Luhn check
- [ ] Hash admin: en DB, pas source code
- [ ] XSS: htmlspecialchars partout
- [ ] File upload: fileinfo(), magic bytes
- [ ] footer.php: créé et inclus
- [ ] Rate limiting: AJAX + serveur
- [ ] Helpers: extraits dans fichier
- [ ] Logging: centralisé
- [ ] Tests manuels: tous les formulaires
- [ ] SQL: backup avant déploiement

---

**Temps total estimé**: 6-8 heures de travail  
**Impact**: Note +4 points (~14/20 → 18/20)
