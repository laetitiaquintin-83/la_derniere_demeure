<?php 
session_start(); // Indispensable pour lire le panier !
require_once 'config.php'; 

// Calcul du nombre total d'articles pour le compteur du menu
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

// 1. Récupération des catégories existantes en base de données pour les filtres
$catStmt = $pdo->query("SELECT DISTINCT categorie FROM catalogue_funeraire WHERE categorie IS NOT NULL AND categorie != '' ORDER BY categorie ASC");
$categories_db = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// 2. Association des catégories à tes magnifiques titres poétiques
$titres_poetiques = [
    'Cercueil'             => 'Vaisseaux de Mémoire',
    'Cercueils'            => 'Vaisseaux de Mémoire',
    'Urne'                 => 'Le Souffle des Anciens',
    'Urnes'                => 'Le Souffle des Anciens',
    'Stèle'                => "Les Gardiens de l'Éternité",
    'Stèles'               => "Les Gardiens de l'Éternité",
    'Fleurs'               => "L'Offrande Éternelle",
    'Hommages Floraux'     => "L'Offrande Éternelle",
    'Univers Passion'      => "L'Écho d'une Vie",
    'Animaux'              => "Le Repos des Fidèles"
];

// Dédupliquer les catégories qui partagent le même titre poétique
$categories_avec_titre = [];
$titres_vus = [];
foreach ($categories_db as $cat) {
    $titre = $titres_poetiques[$cat] ?? $cat;
    if (!in_array($titre, $titres_vus)) {
        $categories_avec_titre[] = $cat;
        $titres_vus[] = $titre;
    }
}
$categories_db = $categories_avec_titre;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>La Dernière Demeure | L'Éternité pour Écrin</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="hero-section">
        <nav>
            <a href="index.php" class="active">🏠 Accueil</a>
            <a href="images/catalogue.php">📜 Le Catalogue</a>
            <a href="images/foret.php">🌿 Le Sanctuaire</a>
            <a href="repos_des_fideles.php">🐾 Repos des Fidèles</a>
            <a href="ceremonies.php">🕯️ L'Art de l'Adieu</a>
            <a href="contact.php">📞 Conciergerie</a>
            
            <?php 
            // VÉRIFICATION ADMIN : Ces liens n'apparaîtront que si l'utilisateur est admin
            if(isset($_SESSION['admin_connecte']) && $_SESSION['admin_connecte']): 
            ?>
                <a href="admin.php">Le Registre</a>
                <a href="gestion.php">L'Inventaire</a>
            <?php endif; ?>

            <a href="panier.php" style="margin-left: auto;">L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="lock-link">🔓 Quitter</a>
            <?php else: ?>
                <a href="login.php" class="lock-link">🔒 Entrer</a>
            <?php endif; ?>
        </nav>
        
        <div class="hero-content">
            <span class="citation">"La mort n'est pas une fin, c'est une métamorphose"</span>
            <h1>LA DERNIÈRE<br>DEMEURE</h1>
            <h2 class="sous-titre">L'Éternité pour Écrin</h2>
            <div class="scroll-indicator">
                <span>Découvrir le passage</span>
                <div class="arrow"></div>
            </div>
        </div>
    </header>

    <section id="catalogue" class="catalogue-section">
        
        <div class="filters-container">
            <a href="?#catalogue" class="btn-filter <?php echo !isset($_GET['cat']) ? 'active' : ''; ?>">Tout l'Univers</a>
            <?php foreach ($categories_db as $cat): ?>
                <a href="?cat=<?php echo urlencode($cat); ?>#catalogue" 
                   class="btn-filter <?php echo (isset($_GET['cat']) && $_GET['cat'] === $cat) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php
        // 3. Logique d'affichage : soit on montre la catégorie filtrée, soit on montre tout
        $categories_to_show = (isset($_GET['cat']) && !empty($_GET['cat'])) ? [$_GET['cat']] : $categories_db;

        // On boucle sur les catégories à afficher
        foreach ($categories_to_show as $cat) :
            // On récupère le titre poétique, ou on affiche le nom brut si non défini
            $titre = $titres_poetiques[$cat] ?? $cat; 

            $query = $pdo->prepare("SELECT * FROM catalogue_funeraire WHERE categorie = ? ORDER BY id DESC");
            $query->execute([$cat]);
            $produits = $query->fetchAll();

            // S'il y a des produits dans cette catégorie, on crée la section
            if (count($produits) > 0) : ?>
                <h2 class="section-title"><?php echo htmlspecialchars($titre); ?></h2>
                
                <div class="product-grid">
                    <?php foreach ($produits as $p) : ?>
                        <div class="produit-card">
                            <span class="category-badge"><?php echo htmlspecialchars($p['categorie']); ?></span>
                            
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['nom']); ?>">
                            </div>
                            
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($p['nom']); ?></h3>
                                
                                <?php 
                                $details = array_filter([$p['essence_bois'], $p['couleur_velours']]);
                                if (!empty($details)): ?>
                                    <p style="color: var(--gold); font-size: 0.75em; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">
                                        <?php echo htmlspecialchars(implode(' | ', $details)); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($p['description'])): ?>
                                    <p class="description"><?php echo htmlspecialchars($p['description']); ?></p>
                                <?php endif; ?>
                                
                                <span class="price"><?php echo number_format($p['prix'], 2, ',', ' '); ?> €</span>
                                <a href="#" class="btn-gold btn-add-cart" 
                                   data-id="<?php echo $p['id']; ?>" 
                                   data-nom="<?php echo htmlspecialchars($p['nom']); ?>" 
                                   data-prix="<?php echo $p['prix']; ?>">
                                   Ajouter à l'Offrande
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; 
        endforeach; ?> 
    </section>

    <section class="forest-section">
        <div class="forest-overlay">
            <h2 class="section-title">Le Sanctuaire des Racines</h2>
            <div class="forest-content">
                <div class="forest-text">
                    <h3>L'Éveil de l'Âme Sylvestre</h3>
                    <p>À la croisée de l'espace naturel et du site funéraire, la <strong>forêt cinéraire</strong> répond aux attentes d'obsèques plus simples, empreintes de poésie et de respect.</p>
                    <p>Ici, le souvenir ne s'inscrit pas dans la pierre, mais dans la sève d'un arbre majestueux, offrant au défunt une métamorphose vers une vie nouvelle.</p>
                    <ul class="gold-list">
                        <li>Retour pur et sacré à la terre</li>
                        <li>Urnes de terre, de sable ou de lin</li>
                        <li>Un sanctuaire vivant, bercé par le vent</li>
                    </ul>
                    <a href="images/foret.php" class="btn-gold">Explorer ce dernier voyage</a>
                </div>
                <div class="forest-image-box">
                    <img src="images/foret.jpg" alt="Forêt Cinéraire Mystique">
                </div>
            </div>
        </div>
    </section>

    <div id="toast" class="toast"></div>

    <script src="script.js"></script>

    <?php include 'footer.php'; ?>
</body>
</html>