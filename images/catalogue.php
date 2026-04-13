<?php
session_start(); // Indispensable ici aussi pour lire le panier !

// On utilise ton fichier de configuration pour garder la connexion sécurisée
require_once '../config.php';

// Calcul du nombre total d'articles pour le compteur du menu
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

// 1. Récupération des catégories existantes pour les filtres
$catStmt = $pdo->query("SELECT DISTINCT categorie FROM catalogue_funeraire WHERE categorie IS NOT NULL AND categorie != '' ORDER BY categorie ASC");
$categories_db = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// 2. Association des catégories à tes titres poétiques
$titres_poetiques = [
    'Cercueil'             => 'Vaisseaux de Mémoire',
    'Cercueils'            => 'Vaisseaux de Mémoire',
    'Urne'                 => 'Le Souffle des Anciens',
    'Urnes'                => 'Le Souffle des Anciens',
    'Stèle'                => "Les Gardiens de l'Éternité",
    'Stèles'               => "Les Gardiens de l'Éternité",
    'Reliquaires & Stèles' => "Les Gardiens de l'Éternité",
    'Fleurs'               => "L'Offrande Éternelle",
    'Hommages Floraux'     => "L'Offrande Éternelle",
    'Univers Passion'      => "L'Écho d'une Vie"
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
    <title>Le Catalogue | La Dernière Demeure</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body"> 
    <header class="admin-nav">
        <nav>
            <a href="../index.php">🏠 Accueil</a>
            <a href="catalogue.php" class="active">📜 Le Catalogue</a>
            <a href="foret.php">🌿 Le Sanctuaire</a>
            <a href="../ceremonies.php">🕯️ L'Art de l'Adieu</a>
            <a href="../contact.php">📞 Conciergerie</a>
            <a href="../panier.php" style="margin-left: auto;">♧️ L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
        </nav>
    </header>

    <section id="catalogue" class="catalogue-section" style="padding-top: 50px;">
        <h1 class="section-title">Notre Catalogue d'Exception</h1>
        
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
        // 3. Logique d'affichage : catégorie filtrée ou tout
        $categories_to_show = (isset($_GET['cat']) && !empty($_GET['cat'])) ? [$_GET['cat']] : $categories_db;

        foreach ($categories_to_show as $cat) :
            $titre = $titres_poetiques[$cat] ?? $cat; 

            $query = $pdo->prepare("SELECT * FROM catalogue_funeraire WHERE categorie = ? ORDER BY id DESC");
            $query->execute([$cat]);
            $produits = $query->fetchAll();

            if (count($produits) > 0) : ?>
                <h2 class="section-title" style="font-size: 1.5em; margin-top: 40px; color: #fff;"><?php echo htmlspecialchars($titre); ?></h2>
                
                <div class="product-grid">
                    <?php foreach ($produits as $p) : ?>
                        <div class="produit-card">
                            <span class="category-badge"><?php echo htmlspecialchars($p['categorie']); ?></span>
                            
                            <div class="product-image">
                                <img src="<?php 
                                    $path = htmlspecialchars($p['image_path']);
                                    // Corriger le chemin si c'est un chemin absolu depuis images/
                                    if (strpos($path, 'images/') === 0) {
                                        $path = substr($path, 7); // Enlever 'images/' 
                                    }
                                    echo $path;
                                ?>" alt="<?php echo htmlspecialchars($p['nom']); ?>">
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

                                <p class="description"><?php echo htmlspecialchars($p['description']); ?></p>
                                
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

    <div id="toast" class="toast"></div>

    <script src="../script.js"></script>
</body>
</html>