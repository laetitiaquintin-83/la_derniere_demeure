# 🏛️ La Dernière Demeure - E-commerce Funéraire

## 📋 Vue d'ensemble

**La Dernière Demeure** est une application web full-stack complète dédiée au secteur des services funéraires. Ce projet démontre une maîtrise des technologies web modernes avec une approche sécurisée et professionnelle.

### 🎯 Objectif du projet

Créer une plateforme e-commerce permettant :

- **Au client** : parcourir un catalogue de produits funéraires, consulter les services, ajouter des articles au panier et effectuer des achats
- **À l'administrateur** : gérer le catalogue (ajout, modification, suppression de produits), contrôler les stocks, et traiter les commandes

---

## 🧭 Commencer Ici (version claire)

Si tu veux modifier une **page visible** :

- 1) Entrée HTTP à la racine (`index.php`, `catalogue.php`, `contact.php`, etc.)
- 2) Rendu HTML dans `app/Views/pages/`
- 3) Logique dans `app/Controllers/`
- 4) Requêtes SQL / données dans `app/Models/`

Si tu veux modifier la **sécurité / config** :

- `config.php`, `helpers.php`, `constantes.php`, `app/bootstrap.php`

Si tu veux modifier les **styles et scripts** :

- `style.css`, `script.js`, `images/`

Si tu veux lire les **livrables / audits** :

- `docs/README.md` puis les rapports dans `docs/`

Si tu veux lancer des **outils locaux** :

- `tools/README.md`

---

## 🛠️ Stack Technique

| Composant           | Technologie                       | Version/Détails |
| ------------------- | --------------------------------- | --------------- |
| **Backend**         | PHP                               | 7.4+            |
| **Frontend**        | HTML5, CSS3, JavaScript (Vanilla) | ES6+            |
| **Base de données** | MySQL                             | 8.0+            |
| **Serveur local**   | Laragon/XAMPP                     | Apache + PHP    |
| **Gestion BD**      | PDO (prepared statements)         | Paramètres liés |

---

## 🎓 Compétences Démontrées

### 1. **Sécurité Web** 🔐

- **Protection CSRF** : tokens générés et validés pour chaque formulaire
- **Authentification sécurisée** : `password_verify()` avec hachage bcrypt
- **Injection SQL** : éradiquée grâce aux prepared statements PDO
- **Validation des données** : contrôle des types et des formats en entrée
- **Gestion des sessions** : `session_regenerate_id()` après authentification

### 2. **Architecture & Design Patterns**

- **Séparation des responsabilités** : logique métier, présentation, données
- **MVC simplifié** : fichiers dédiés pour chaque fonctionnalité
- **DRY (Don't Repeat Yourself)** : réutilisation du `config.php` et fonctions communes
- **PRG Pattern (Post/Redirect/Get)** : évite les doublons de données

### 3. **Bonnes Pratiques PHP**

- **PDO abstraction** : indépendant du système de base de données
- **Transactions SQL** : garantit la cohérence des stocks lors des commandes
- **Gestion des erreurs** : try/catch pour les exceptions PDO
- **Code commenté** : documentation explicite des décisions techniques

### 4. **Frontend & UX**

- **Responsive Design** : adapté à mobile, tablette, desktop
- **Fetch API (AJAX)** : gestion asynchrone du panier sans rechargement
- **Intersection Observer** : animations des cartes produits au scroll
- **Toast notifications** : retours utilisateur en temps réel
- **Curseur personnalisé** : thème graphique cohérent

### 5. **Gestion de Données**

- **Mise à jour de stocks** : décrémentation sécurisée lors des commandes
- **Panier persistant** : stocké en session avec validation BD
- **Filtrage par catégories** : requêtes dynamiques et optimisées
- **Calculs monétaires** : formatage cohérent des prix (locale fr)

---

## 📁 Structure du Projet

```
la_derniere_demeure/
│
├── 🌐 Entrées publiques (front-controllers)
│   ├── index.php
│   ├── catalogue.php
│   ├── panier.php
│   ├── login.php / logout.php
│   ├── admin.php / gestion.php / modifier.php / supprimer.php
│   ├── contact.php / foret.php / ceremonies.php / repos_des_fideles.php
│   └── payment-form.php / payment-success.php / create-checkout-session.php
│
├── 🧠 MVC (app/)
│   ├── app/bootstrap.php
│   ├── app/Controllers/        🎛️ Contrôleurs
│   ├── app/Models/             🗃️ Modèles
│   └── app/Views/pages/        🖼️ Vues pages
│
├── ⚙️ Configuration et utilitaires runtime
│   ├── config.php / helpers.php / constantes.php
│   ├── update_stock.php / ajouter_panier.php / traitement_jardin.php
│   └── process-payment.php / traitement_paiement.php
│
├── 🎨 Assets
│   ├── style.css / script.js
│   ├── images/
│   └── footer.php
│
├── 📚 Documentation projet
│   ├── README.md               📖 Point d'entrée principal
│   └── docs/                   🗂️ Livrables, audits, checklists, synthèses
│
└── 🧪 Outils locaux
    └── tools/                  🔧 Scripts et fichiers de test
```

---

## 🚀 Installation & Setup

### Prérequis

- PHP 7.4+
- MySQL 8.0+
- Laragon ou XAMPP installé

### Étapes d'installation

#### 1️⃣ Cloner/Placer les fichiers

```bash
# Copier le dossier dans le répertoire web de Laragon
cd C:\laragon\www\
# Placer le projet : c:\laragon\www\la_derniere_demeure
```

#### 2️⃣ Créer la base de données MySQL

```sql
CREATE DATABASE la_derniere_demeure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE la_derniere_demeure;

-- Table des produits
CREATE TABLE catalogue_funeraire (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL UNIQUE,
    categorie VARCHAR(100),
    essence_bois VARCHAR(100),
    couleur_velours VARCHAR(100),
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Index pour performances
CREATE INDEX idx_categorie ON catalogue_funeraire(categorie);
CREATE INDEX idx_prix ON catalogue_funeraire(prix);
```

#### 3️⃣ Configurer config.php

```php
// config.php
$host = 'localhost';
$dbname = 'la_derniere_demeure';
$user = 'root';
$pass = '';  // Vide pour Laragon/XAMPP local
```

#### 4️⃣ Configuration du mot de passe admin

```bash
# Mot de passe admin actuel : cerbere
# Hash bcrypt stocké dans login.php :
$2y$10$g4b7j.qM8tjFXDsA5g1efOnAg5hrtHmUPYAituen0cM4BPzGcA7Aa

# Pour générer un nouveau hash en CLI :
$ php -r "echo password_hash('votre_mdp', PASSWORD_BCRYPT, ['cost' => 10]);"
# Puis remplacer $hash_sauvegarde dans login.php
```

#### 5️⃣ Démarrer l'application

```bash
# Lancer Laragon
# Accéder à http://localhost/la_derniere_demeure/

# Identifiants de connexion admin :
# Mot de passe : cerbere
```

---

## 🎯 Fonctionnalités Principales

### 👥 Côté Client

#### 📖 Catalogue de produits

- Affichage avec grille responsive
- Filtrage par catégories : **Cercueils, Urnes, Stèles, Fleurs, Univers Passion, Animaux**
- Animations des cartes au scroll (Intersection Observer) - **sauf sur la page Cérémonies**
- Titres poétiques pour chaque catégorie : "Vaisseaux de Mémoire", "Le Souffle des Anciens", etc.
- Images optimisées du catalogue

#### 🛒 Gestion du panier

- Ajout/suppression d'articles en temps réel (AJAX)
- Compteur dynamique dans la barre de navigation
- Persistance en session utilisateur
- Validation des stocks avant commande

#### 💳 Paiement

- Formulaire de commande avec validation
- Traitement des transactionsSQL (atomicité garantie)
- Mise à jour automatique des stocks
- Confirmation de commande

#### ✨ Sections thématiques

- **Le Sanctuaire des Racines** : présentation de la forêt cinéraire avec effets de flottement sur les images
- **L'Art de l'Adieu** : rituels et cérémonies avec effets mystiques (voile lumineux, balayage de lumière)
- **Le Repos des Fidèles** : section dédiée aux compagnons animaux avec urnes commémoratives
- **Toast notifications** : retours utilisateur en temps réel

---

### 🔧 Côté Administrateur

#### ➕ Ajouter un produit

- Formulaire complet (nom, catégorie, prix, stock, essence, description)
- Upload d'images avec validation type/taille
- Nommage sécurisé avec timestamp aléatoire
- Protection CSRF obligatoire

#### 📋 Gestion de l'inventaire

- Dashboard d'inventaire avec tous les produits
- Vue d'ensemble du stock (En Stock / Critique / Épuisé)
- Miniatures des produits pour identification rapide

#### ✏️ Modifier un produit

- Édition de tous les champs sauf l'image (par défaut)
- **Nouveau** : Possibilité de lier une image renommée manuellement via chemin
- Ou télécharger une nouvelle image
- Validation complète des données

#### 🗑️ Supprimer un produit

- Suppression avec confirmation
- Token CSRF pour prévention attaques
- Suppression logique de la base de données

---

## 🔒 Aspects Sécurité Implémentés

### Sessions & Authentification

```php
// ✅ Session admin unifiée
session_start();
$_SESSION['admin_connecte'] = true;  // Utilisé partout (cohérent)

// ✅ Vérification au chargement des pages admin
if (!isset($_SESSION['admin_connecte']) || !$_SESSION['admin_connecte']) {
    header('Location: login.php');
    exit;
}

// ✅ Déconnexion sécurisée
session_destroy();
```

### Protections Supplémentaires

```php
// ✅ 1. Protection CSRF
genererTokenCSRF();    // Création du token par formulaire
validerTokenCSRF($token);  // Validation stricte

// ✅ 2. Authentification sécurisée
password_verify($mdp_saisi, $hash_sauvegarde);
session_regenerate_id(true);  // Prévention fixation session

// ✅ 3. Prepared Statements (Injection SQL)
$stmt = $pdo->prepare("SELECT * FROM catalogue_funeraire WHERE id = ?");
$stmt->execute([$id]);

// ✅ 4. Validation des entrées
ctype_digit(strval($id));  // Vérifier type numérique
filter_var($email, FILTER_VALIDATE_EMAIL);  // Email valide
!is_numeric($prix) || $prix > 999999.99;  // Plage logique

// ✅ 5. Transactions pour cohérence données
$pdo->beginTransaction();
// ... modifications ...
$pdo->commit();  // Ou rollback() en cas erreur

// ✅ 6. Gestion des erreurs sécurisée
try {
    // Code
} catch (PDOException $e) {
    // Ne JAMAIS exposer $e->getMessage() à l'utilisateur
    error_log($e->getMessage());  // Enregistrement serveur
    die("Erreur système. Contact administrateur.");
}
```

validerTokenCSRF($token); // Validation stricte

// ✅ 2. Authentification sécurisée
password_verify($mdp_saisi, $hash_sauvegarde);
session_regenerate_id(true); // Prévention fixation session

// ✅ 3. Prepared Statements (Injection SQL)
$stmt = $pdo->prepare("SELECT * FROM catalogue_funeraire WHERE id = ?");
$stmt->execute([$id]);

// ✅ 4. Validation des entrées
ctype_digit(strval($id));  // Vérifier type numérique
filter_var($email, FILTER_VALIDATE_EMAIL); // Email valide
!is_numeric($prix) || $prix > 999999.99; // Plage logique

// ✅ 5. Transactions pour cohérence données
$pdo->beginTransaction();
// ... modifications ...
$pdo->commit(); // Ou rollback() en cas erreur

// ✅ 6. Gestion des erreurs sécurisée
try {
// Code
} catch (PDOException $e) {
    // Ne JAMAIS exposer $e->getMessage() à l'utilisateur
    error_log($e->getMessage()); // Enregistrement serveur
die("Erreur système. Contact administrateur.");
}

```

---

## 🔄 Flux de Commande (Transactionnel)

```

1. Client ajoute articles au panier (session)
   ↓
2. Clique "Passer commande"
   ↓
3. Formulaire paiement avec validation CSRF
   ↓
4. Vérification du panier (non-vide)
   ↓
5. Début transaction SQL
   ├→ Vérification stock (clause WHERE stock >= ?)
   ├→ Décrémentation du stock (UPDATE)
   ├→ Si problème → ROLLBACK (annulation)
   └→ Si succès → COMMIT (enregistrement)
   ↓
6. Vider le panier (session)
   ↓
7. Redirection + message confirmation

```

---

## � Changelog - Mises à Jour Récentes (v1.1)

### ✨ Nouvelles Fonctionnalités
- **Repos des Fidèles** : Nouvelle page dédiée aux compagnons animaux
  - Section hommage poétique
  - 6 services spécialisés avec cartes élégantes
  - Galerie d'urnes commémoratives (catégorie "Animaux")
  - Boutons "Ajouter à l'Offrande" fonctionnels
  - Design responsive avec animations

- **Footer réutilisable** : `footer.php` intégré sur toutes les pages publiques
  - Navigation cohérente avec liens vers toutes les sections
  - Informations de contact et mentions légales
  - Design sombre harmonisé au reste du site

### 🎨 Améliorations Visuelles
- **Effets sur les images du Sanctuaire** :
  - Animation de flottement subtile (6s)
  - Zoom au survol (1.05x) avec filtre luminosité
  - Overlay doré qui apparaît au hover

- **Effets sur les images des Cérémonies** :
  - Voile lumineux radial doré (halo mystique)
  - Balayage de lumière continu (4s)
  - Animation de respiration (8s)
  - Saturation améliorée au hover

- **Centrage des images des urnes** :
  - Flexbox centering automatique
  - `object-fit: contain` pour aucun recadrage
  - Images bien proportionnées dans leurs containers

### 🐛 Corrections & Nettoyage
- **Système d'authentification admin** 🔐
  - Correction du hash bcrypt pour mot de passe "cerbere"
  - Synchronisation des variables de session (`$_SESSION['admin_connecte']`)
  - Login système entièrement fonctionnel

- **Refonte catégories produits** 📦
  - Suppression de "Reliquaires & Stèles" (doublon)
  - Remplacement par "Stèles" seul
  - Ajout de "Animaux" pour la section Repos des Fidèles
  - Mapping des titres poétiques mis à jour

- **Animations optimisées** ⚡
  - Animation progressive (Intersection Observer) **limitée à la page Cérémonies**
  - Toutes les autres pages chargent les éléments sans animation progressive
  - Amélioration de la performance

### 📁 Fichiers Créés/Modifiés
| Fichier | Type | Description |
|---------|------|-------------|
| `repos_des_fideles.php` | ✨ CRÉÉ | Page complète "Repos des Fidèles" avec urnes |
| `footer.php` | ✨ CRÉÉ | Template footer réutilisable |
| `index.php` | 🔧 MODIFIÉ | Ajout lien Repos des Fidèles, fix session |
| `ceremonies.php` | 🎨 MODIFIÉ | Effets mystiques sur les images |
| `images/foret.php` | 🎨 MODIFIÉ | Effets de flottement + overlay |
| `admin.php` | 📦 MODIFIÉ | Catégories mises à jour (Stèles, Animaux) |
| `login.php` | 🔐 MODIFIÉ | Hash bcrypt corrigé |
| `script.js` | ⚡ MODIFIÉ | Animation progressive limitée à Cérémonies |
| `style.css` | — | Styles existants (non modifié) |

---

## 🎯 État du Projet

### ✅ Prêt pour Production
- [x] Système de login sécurisé et fonctionnel
- [x] Gestion complète du panier (AJAX)
- [x] Administration du catalogue (CRUD)
- [x] Transactions SQL atomiques
- [x] Protection CSRF/SQL injection
- [x] Design responsive et mobile-friendly
- [x] Toutes les pages publiques intégrées
- [x] Sections thématiques avec animations

### 🟡 À Considérer
- [ ] Déploiement sur serveur de production
- [ ] Email de confirmation de commande
- [ ] Intégration paiement réelle (Stripe)
- [ ] Système multi-administrateurs avec rôles
- [ ] Historique de commandes client

---

## �💡 Améliorations Futures

### Court terme 🟢
- [ ] Email de confirmation de commande
- [ ] Gestion de plusieurs administrateurs avec rôles
- [ ] Historique des commandes client
- [ ] Recherche de produits par mot-clé

### Moyen terme 🟡
- [ ] Intégration API paiement réelle (Stripe, PayPal)
- [ ] Dashboard statistiques (ventes, produits populaires)
- [ ] Système de newsletter
- [ ] Avis/commentaires clients

### Long terme 🔴
- [ ] Migration vers framework (Laravel, Symfony)
- [ ] API REST avec authentification JWT
- [ ] App mobilePHP React Native
- [ ] CDN pour images optimisées
- [ ] Caching avec Redis

---

## 📊 Points Clés pour l'Examen

### ✅ Qualités à valoriser

1. **Code professionnel** : commentaires, nommage cohérent, structure claire
2. **Sécurité prioritaire** : CSRF, injection SQL, authentification robuste
3. **Expérience utilisateur** : retours temps réel, responsive design, thème cohérent
4. **Gestion d'erreurs** : try/catch, validations multi-niveaux
5. **Bonnes pratiques DB** : prepared statements, transactions, indexes

### 🎤 Points à présenter à l'oral

- **Architecture** : Séparation logique des responsabilités
- **Sécurité** : Démonstration des 5 protections implémentées
- **Thématique** : Design et copywriting cohérent (poétique funéraire)
- **Responsive** : Test sur différentes résolutions
- **Transactions** : Expliquer comment les stocks ne peuvent pas être oversold

---

## 📞 Support & Questions Fréquentes

### Q: Pourquoi PDO et pas ORM comme Eloquent?
**R:** PDO offre un excellent équilibre entre contrôle bas-niveau et abstraction, idéal pour démontrer une compréhension des databases en junior.

### Q: Comment générer le mot de passe admin?
**R:** `php -r "echo password_hash('cerbere', PASSWORD_BCRYPT);"`

### Q: Les images ne se chargent pas ?
**R:** Vérifier que le chemin dans `image_path` correspond exactement au fichier réel dans `images/catalogue/`

### Q: Comment importer des produits en masse?
**R:** Créer un script PHP qui boucle sur des données CSV/JSON et insère via PDO.

---

## 📄 Licence

Projet étudiant - Librement utilisable à des fins pédagogiques.

---

## 👨‍💻 Développeur

**Réalisé pour** : Examen Développeur Web Junior
**Date** : 2026
**Statut** : ✅ Production-Ready

---

## 🏆 Résumé pour l'Entretien

> *"La Dernière Demeure est une application e-commerce full-stack qui démontre une maîtrise des technologies web fondamentales avec une forte emphase sur la sécurité. Le projet implémente CORS protections, transactions SQL atomiques, authentification sécurisée par bcrypt, et une architecture modulaire, tout en maintenant une excellente expérience utilisateur. Elle est prête pour une production aux faibles volumes ou comme base pour une migration future vers un framework moderne."*

---

**Bonne chance pour votre examen! 🍀**
#   l a _ d e r n i e r e _ d e m e u r e 
 
 
```
