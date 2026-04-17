<?php
require_once __DIR__ . '/app/bootstrap.php';

$catalogueController = new CatalogueController(new CatalogueModel($pdo));
$catalogueData = $catalogueController->index();
extract($catalogueData, EXTR_SKIP);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Le Catalogue | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body"> 
    <header class="admin-nav">
        <nav>
            <a href="index.php">✦ Accueil</a>
            <a href="catalogue.php" class="active">✿ Le Catalogue</a>
            <a href="foret.php">✾ Le Sanctuaire</a>
            <a href="ceremonies.php">❦ L'Art de l'Adieu</a>
            <a href="contact.php">❋ Conciergerie</a>
            <a href="panier.php" style="margin-left: auto;">✵ L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
        </nav>
    </header>

    <section id="catalogue" class="catalogue-section" style="padding-top: 50px;">
        <h1 class="section-title">Notre Catalogue d'Exception</h1>
        
        <div class="filters-container">
            <a href="?#catalogue" class="btn-filter <?php echo $selected_category === null ? 'active' : ''; ?>">Tout l'Univers</a>
            <?php foreach ($categories_db as $cat): ?>
                <a href="?cat=<?php echo urlencode($cat); ?>#catalogue" 
                   class="btn-filter <?php echo ($selected_category === $cat) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php foreach ($categories_to_show as $cat) :
            $section = $catalogue_sections[$cat] ?? ['title' => $titres_poetiques[$cat] ?? $cat, 'products' => []];
            $produits = $section['products'];

            if (count($produits) > 0) : ?>
                <h2 class="section-title" style="font-size: 1.5em; margin-top: 40px; color: #fff;"><?php echo htmlspecialchars($section['title']); ?></h2>
                
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

                                <p class="description"><?php echo htmlspecialchars($p['description']); ?></p>
                                
                                <span class="price"><?php echo number_format($p['prix'], 2, ',', ' '); ?> €</span>
                                <a href="#" class="btn-gold btn-add-cart" 
                                   data-id="<?php echo htmlspecialchars($p['id']); ?>" 
                                   data-nom="<?php echo htmlspecialchars($p['nom']); ?>" 
                                   data-prix="<?php echo htmlspecialchars($p['prix']); ?>">
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

    <script src="script.js"></script>

    <?php include 'footer.php'; ?>
</body>
</html>