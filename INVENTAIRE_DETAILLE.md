# 📋 INVENTAIRE DÉTAILLÉ - Implémentations VS Besoins

**Comparaison complète: Ce qui est implémenté vs. Ce qui manque**
**Statut:** Document historique. L'état courant du projet est résumé dans [SYNTHESE_SECURITE.md](SYNTHESE_SECURITE.md).

---

## 1. OWASP A01:2021 – SQL INJECTION

### État d'Implémentation

| Aspect                   |      Implémenté      |             Lacune             |
| ------------------------ | :------------------: | :----------------------------: |
| **Requêtes préparées**   |        ✅ Oui        |           ❌ Aucune            |
| **Paramètres liés**      |        ✅ Oui        |           ❌ Aucune            |
| **Validation input**     |     ✅ Partielle     | ⚠️ Certains $\_GET non validés |
| **PDO errors**           | ✅ ERRMODE_EXCEPTION |             ✅ Bon             |
| **Sauvegarde des creds** |        ❌ Non        |           ✅ À faire           |

### Détails Implémentés ✅

**Fichier: config.php (L25-41)**

```php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
```

✅ Bonne pratique: UTF-8 explicite, exception mode activé

**Fichier: login.php (L15)**

```php
$stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
```

✅ Requête préparée avec paramètres liés

**Fichier: panier.php (L26)**

```php
$stmt = $pdo->prepare("SELECT id, nom, prix FROM catalogue_funeraire WHERE id = ?");
```

✅ Requête préparée correct

### Lacunes Identifiées ⚠️

1. **gestion.php (L16-20)**: Validation fragile des catégories
   ```php
   // Utilise whitelist, c'est OK, mais fragile
   if (in_array($_GET['cat'], $categories_db)) {
       $categories_to_show = [$_GET['cat']];
   }
   ```
   **Fix:** Améliorer validation (regex + type check)

---

## 2. OWASP A02:2021 – CRYPTOGRAPHIC FAILURES

### État d'Implémentation

| Aspect                     | Implémenté |           Lacune           |
| -------------------------- | :--------: | :------------------------: |
| **Chiffrement en transit** |   ❌ Non   |   ⚠️ Pas de HTTPS forcé    |
| **Chiffrement au repos**   |   ❌ Non   |  ✅ À faire en production  |
| **Gestion de clés**        |   ❌ Non   |         ✅ À faire         |
| **HASHING mots de passe**  |   ✅ Oui   | ✅ PASSWORD_BCRYPT utilisé |
| **Tokens CSRF**            |   ✅ Oui   |  ✅ bin2hex(random_bytes)  |

### Détails Implémentés ✅

**Fichier: config.php (L34)**

```php
$default_hash = password_hash('cerbere', PASSWORD_BCRYPT);
```

✅ BCRYPT utilisé (bon choix)

**Fichier: login.php (L19)**

```php
if ($result && password_verify($mdp_saisi, $result['password_hash'])) {
```

✅ Vérification sécurisée avec password_verify()

**Fichier: config.php (L53)**

```php
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
```

✅ Génération de token robuste

### Lacunes Identifiées ⚠️

1. **Pas de HTTPS forcé**

   ```php
   // MANQUE:
   if (!isset($_SERVER['HTTPS'])) {
       header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
       exit;
   }
   ```

2. **Pas de chiffrement PII**
   - Aucun chiffrement pour données sensibles
   - Stockage en texte clair basiquement

3. **HSTS absent**
   ```php
   // MANQUE:
   header('Strict-Transport-Security: max-age=31536000');
   ```

---

## 3. OWASP A03:2021 – INJECTION (Injections variées)

### État d'Implémentation

| Type d'Injection        |         Implémenté          |      Lacune      |
| ----------------------- | :-------------------------: | :--------------: |
| **SQL Injection**       |         ✅ Préparé          |    ❌ Aucune     |
| **Command Injection**   | ✅ Aucune commande exécutée | ✅ Pas de risque |
| **LDAP Injection**      |    ✅ N/A (pas de LDAP)     |       N/A        |
| **XML/XPath Injection** |           ✅ N/A            |       N/A        |
| **Path Traversal**      |  ✅ Validé avec realpath()  |      ✅ Bon      |

### Détails Implémentés ✅

**Fichier: supprimer.php (L23-24)**

```php
$real_path = realpath($target_dir) . DIRECTORY_SEPARATOR . $file_name;
if (strpos($real_path, realpath($target_dir)) !== 0) {
    // ❌ Path traversal tentée
}
```

✅ Bonne prévention de traversée répertoire

**Fichier: admin.php (L67-76)**

```php
// Path traversal check
$real_path = realpath($target_dir) . DIRECTORY_SEPARATOR . $file_name;
if (strpos($real_path, realpath($target_dir)) !== 0) {
    die("Erreur de sécurité : Chemin invalide (tentative de traversée répertoire).");
}
```

✅ Prévention PATH TRAVERSAL correcte

---

## 4. OWASP A04:2021 – INSECURE DESIGN (Conception insécurisée)

### État d'Implémentation

| Aspect                       | Implémenté |     Lacune     |
| ---------------------------- | :--------: | :------------: |
| **Architecture de sécurité** | ⚠️ Basique | ⚠️ À renforcer |
| **Threat modeling**          |   ❌ Non   |   ✅ À faire   |
| **Control mapping**          | ⚠️ Partial | ⚠️ À compléter |
| **Testing de sécurité**      |   ❌ Non   |   ✅ À faire   |
| **Documentation sécurité**   |   ❌ Non   |   ✅ À faire   |

### Problèmes Identifiés ⚠️

1. **Pages sans authentification accessibles publiquement**
   - contact.php: ❌ Pas d'auth
   - repos_des_fideles.php: ❌ Pas d'auth
   - **Risque:** Spam illimité

2. **Pas de Rate Limiting**
   - ❌ login.php: Aucun limite de tentatives de login
   - ❌ contact.php: Aucune limite de soumissions
   - **Risque:** Brute force, DDoS

3. **Pas d'audit trail**
   - ❌ Aucune table d'audit
   - ❌ Aucune traçabilité des modifications
   - **Risque:** Fraude non détectée

---

## 5. OWASP A05:2021 – BROKEN ACCESS CONTROL

### État d'Implémentation

| Aspect                      | Implémenté |            Lacune            |
| --------------------------- | :--------: | :--------------------------: |
| **Authentification**        |   ✅ Oui   | ❌ Sur pages admin seulement |
| **Autorisation**            | ⚠️ Basique |   ⚠️ Binaire (admin/user)    |
| **RBAC**                    |   ❌ Non   |       ✅ À implémenter       |
| **Direct Object Reference** | ✅ Vérifié |   ✅ Requêtes paramétrées    |

### Détails Implémentés ✅

**Fichier: admin.php (L3-6)**

```php
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}
```

✅ Vérification d'authentification présente

**Fichier: gestion.php (L3-6)**

```php
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}
```

✅ Même protection partout

### Lacunes Identifiées ⚠️

1. **Pages publiques sans validation**
   - contact.php: ❌ Aucune authentification
   - repos_des_fideles.php: ❌ Aucune authentification
   - **Risque:** N'importe qui peut soumettre

2. **Pas de granularité des rôles**
   - Utilisateur: admin_connecte = true/false
   - ❌ Pas de rôles (super-admin, modérateur, etc.)
   - ❌ Pas de permissions par ressource

3. **logout.php sans validation CSRF**
   ```php
   // MANQUE: Validation CSRF
   session_destroy();
   ```
   **Risque:** Force logout via CSRF

---

## 6. OWASP A06:2021 – VULNERABLE & OUTDATED COMPONENTS

### État d'Implémentation

| Aspect                    | Implémenté |        Lacune        |
| ------------------------- | :--------: | :------------------: |
| **Composer**              |   ❌ Non   |    ✅ À utiliser     |
| **Dependencies tracking** |   ❌ Non   |   ✅ À implémenter   |
| **Version updates**       |   ❌ Non   | ✅ À mettre en place |
| **Security patches**      |   ❌ Non   |   ✅ À automatiser   |

### Recommandations

1. **Initialiser Composer**

   ```bash
   composer init
   composer require stripe/stripe-php
   composer require intervention/image
   ```

2. **Vérifier les vulnérabilités**
   ```bash
   composer require --dev sensiolabs/security-checker
   vendor/bin/security-checker security:check composer.lock
   ```

---

## 7. OWASP A07:2021 – AUTHENTICATION FAILURES

### État d'Implémentation

| Aspect                 |     Implémenté     |        Lacune         |
| ---------------------- | :----------------: | :-------------------: |
| **Password hashing**   | ✅ PASSWORD_BCRYPT |        ✅ Bon         |
| **Session management** |    ✅ Sécurisé     | ✅ HTTPOnly, SameSite |
| **Rate limiting**      |       ❌ Non       |      ✅ À faire       |
| **Account lockout**    |       ❌ Non       |      ✅ À faire       |
| **MFA**                |       ❌ Non       | ⚠️ Optionnel pour PME |

### Détails Implémentés ✅

**Fichier: login.php (L19-22)**

```php
if ($result && password_verify($mdp_saisi, $result['password_hash'])) {
    session_regenerate_id(true);  // ✅ Régénération
    $_SESSION['admin_connecte'] = true;
}
```

✅ Régénération de session après login

**Fichier: config.php (L9-14)**

```php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
session_start();
```

✅ Cookies sécurisés

### Lacunes Identifiées ⚠️

1. **Pas de rate limiting sur login**
   - ❌ Aucune limite de tentatives
   - **Risque:** Brute force possible

2. **Pas de compte lockout**
   - ❌ Si 10 tentatives échouées → pas de blocage
   - **Risque:** Attaques brute force réussies

3. **Mot de passe par défaut jamais changé**
   ```php
   // config.php
   $default_hash = password_hash('cerbere', PASSWORD_BCRYPT);
   ```
   **Risque:** CRITIQUE en production

---

## 8. OWASP A08:2021 – SOFTWARE & DATA INTEGRITY FAILURES

### État d'Implémentation

| Aspect                    | Implémenté  |            Lacune            |
| ------------------------- | :---------: | :--------------------------: |
| **Transactions BD**       |   ✅ Oui    | ✅ Bon (traitement_paiement) |
| **Intégrité des données** | ✅ Présente |   ✅ Vérifications valides   |
| **Updates sécurisés**     |   ❌ Non    |          ✅ À faire          |
| **Checksum/Hash**         |   ❌ Non    |          ✅ À faire          |

### Détails Implémentés ✅

**Fichier: traitement_paiement.php (L63-87)**

```php
$pdo->beginTransaction();
// Vérifications...
$pdo->commit();
// Ou en cas d'erreur:
if ($pdo->inTransaction()) {
    $pdo->rollBack();
}
```

✅ Transactions correctes

---

## 9. OWASP A09:2021 – LOGGING & MONITORING FAILURES

### État d'Implémenté

| Aspect          |   Implémenté   |   Lacune   |
| --------------- | :------------: | :--------: |
| **Logging**     | ⚠️ error_log() | ⚠️ Minimal |
| **Monitoring**  |     ❌ Non     | ✅ À faire |
| **Alertes**     |     ❌ Non     | ✅ À faire |
| **Audit trail** |     ❌ Non     | ✅ À faire |

### Détails Implémentés ⚠️

**Fichier: traitement_paiement.php (L84)**

```php
error_log("Erreur de commande : " . $e->getMessage());
```

⚠️ Logging basique, pas de structuration

**Fichier: supprimer.php (L46)**

```php
error_log("Erreur lors de l'anéantissement : " . $e->getMessage());
```

⚠️ Minimal

### Lacunes Identifiées ❌

1. **Pas de table audit**
   - ❌ Aucune trace des modifications
   - **Risque:** Fraude indétectable

2. **Pas de monitoring des erreurs**
   - ❌ Les erreurs vont juste dans error.log
   - ❌ Pas d'alertes

3. **Pas de métriques de sécurité**
   - ❌ Pas de tracking des tentatives de login échouées
   - ❌ Pas de tracking des uploads

---

## 10. OWASP A10:2021 – SERVER-SIDE REQUEST FORGERY (SSRF)

### État d'Implémentation

| Aspect                 | Implémenté |              Lacune               |
| ---------------------- | :--------: | :-------------------------------: |
| **Validation URLs**    |   ❌ Non   |       ✅ Aucune URL externe       |
| **Restriction réseau** |   ❌ Non   | ✅ N/A (pas de requêtes externes) |
| **Rate limiting**      |   ❌ Non   |            ✅ À faire             |

### Statut

✅ **SAFE** - Aucune requête HTTP sortante détectée  
✅ Pas d'inclusion de fichiers distants  
✅ Pas de URLs utilisateur à risque

---

## RÉSUMÉ INVENTAIRE PAR DOMAINE

### SQL INJECTION 🟢

```
Préparation        ✅ 100% (tous les fichiers)
Paramètres         ✅ 100%
Validation input   ⚠️ 80% (gestion.php à améliorer)
─────────────────────────────────
Score: 9/10
```

### XSS 🟡

```
Output escaping    ✅ 90% (contact.php missing)
Input validation   ✅ 85% (modifier.php missing)
MIME types         ✅ 95%
─────────────────────────────────
Score: 8/10
```

### CSRF 🟡

```
Token generation   ✅ 100%
Token validation   ⚠️ 50% (contact, repos missing)
Hidden fields      ✅ 90%
─────────────────────────────────
Score: 7/10
```

### AUTHENTIFICATION 🟢

```
Password hashing   ✅ 100%
Session mgmt       ✅ 100%
Rate limiting      ❌ 0%
MFA               ❌ 0%
─────────────────────────────────
Score: 8/10
```

### DONNÉES SENSIBLES 🔴

```
Encryption         ❌ 0%
Card storage       ⚠️ WARNING (violation PCI)
API integration    ❌ 0%
─────────────────────────────────
Score: 4/10 ⚠️ CRITIQUE
```

### CONTRÔLE D'ACCÈS 🟡

```
Auth pages         ✅ 100% (admin pages)
Auth public        ❌ 0% (contact, repos)
RBAC              ❌ 0%
─────────────────────────────────
Score: 6/10
```

### CONFIGURATION 🔴

```
Secrets (.env)     ❌ 0%
Headers sécurité   ❌ 0%
HTTPS              ❌ 0%
─────────────────────────────────
Score: 5/10 ⚠️ À CORRIGER
```

### AUDIT & LOGS 🔴

```
Audit trail        ❌ 0%
Error logging      ⚠️ 20%
Monitoring         ❌ 0%
─────────────────────────────────
Score: 5/10
```

### FILE UPLOADS 🟢

```
Extension check    ✅ 100%
MIME validation    ✅ 85% (modifier.php incomplete)
Path traversal     ✅ 100%
Size limits        ✅ 100%
─────────────────────────────────
Score: 8/10
```

### TRANSACTIONS 🟢

```
Atomicity          ✅ 100% (paiement)
Consistency        ✅ 100%
Integrity          ✅ 100%
─────────────────────────────────
Score: 9/10
```

---

## MATRICES IMPLÉMENTATION/BESOIN

### Fichier: config.php

| Besoin              | Implémenté |  %   | Note               |
| ------------------- | :--------: | :--: | ------------------ |
| Connexion sécurisée |     ✅     | 50%  | Manque pwd BD      |
| Sessions sécurisées |     ✅     | 100% | HTTPOnly, SameSite |
| CSRF token          |     ✅     | 100% | Bon                |
| Error handling      |     ⚠️     | 30%  | À améliorer        |
| Headers sécurité    |     ❌     |  0%  | À ajouter          |
| Env variables       |     ❌     |  0%  | À implémenter      |

---

### Fichier: login.php

| Besoin           | Implémenté |  %   | Note       |
| ---------------- | :--------: | :--: | ---------- |
| Password hashing |     ✅     | 100% | BCRYPT bon |
| Session regen    |     ✅     | 100% | Fait       |
| CSRF protection  |     ✅     | 100% | Présent    |
| Rate limiting    |     ❌     |  0%  | À ajouter  |
| Account lockout  |     ❌     |  0%  | À faire    |

---

### Fichier: traitement_paiement.php

| Besoin          | Implémenté |  %   | Note     |
| --------------- | :--------: | :--: | -------- |
| Validation Luhn |     ✅     | 100% | Bon      |
| Transactions    |     ✅     | 100% | Bon      |
| CSRF            |     ✅     | 100% | Présent  |
| Chiffrement     |     ❌     |  0%  | CRITIQUE |
| API Paiement    |     ❌     |  0%  | CRITIQUE |
| Logging         |     ⚠️     | 50%  | Minimal  |

---

### Fichier: contact.php

| Besoin           | Implémenté |  %  | Note      |
| ---------------- | :--------: | :-: | --------- |
| CSRF             |     ❌     | 0%  | À ajouter |
| Validation       |     ❌     | 0%  | À ajouter |
| Rate limiting    |     ❌     | 0%  | À ajouter |
| XSS protection   |     ❌     | 0%  | À ajouter |
| Email validation |     ❌     | 0%  | À ajouter |

---

### Fichier: repos_des_fideles.php + traitement_jardin.php

| Besoin            | Implémenté |  %  | Note      |
| ----------------- | :--------: | :-: | --------- |
| CSRF              |     ❌     | 0%  | À ajouter |
| Upload validation |     ✅     | 85% | MIME OK   |
| Rate limiting     |     ❌     | 0%  | À ajouter |
| Modération        |     ❌     | 0%  | À ajouter |
| XSS output        |     ❌     | 0%  | À ajouter |

---

## CONCLUSION

```
IMPLÉMENTATION GLOBALE: 57%

Bien fait (80-100%):
✅ config.php            70%
✅ login.php             80%
✅ admin.php             80%
✅ panier.php            80%
✅ supprimer.php         80%
✅ update_stock.php      80%

À améliorer (50-79%):
⚠️ helpers.php           80% (documentation)
⚠️ gestion.php           70%
⚠️ modifier.php          60%
⚠️ traitement_jardin.php 50%

À corriger (0-49%):
❌ contact.php           10% (CRITIQUE)
❌ traitement_paiement   40% (CRITIQUE)
❌ logout.php            50%
❌ Audit trail           0% (MANQUANT)
❌ Rate limiting         0% (MANQUANT)
```

---

**Fin de l'inventaire détaillé**
