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

$univers_en_avant = [];
foreach (array_slice($categories_db, 0, 4) as $cat) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM catalogue_funeraire WHERE categorie = ?");
    $countStmt->execute([$cat]);
    $total_articles = (int) $countStmt->fetchColumn();

    $sampleStmt = $pdo->prepare("SELECT nom, image_path, prix FROM catalogue_funeraire WHERE categorie = ? ORDER BY id DESC LIMIT 1");
    $sampleStmt->execute([$cat]);
    $produit_en_vedette = $sampleStmt->fetch(PDO::FETCH_ASSOC) ?: null;

    $univers_en_avant[] = [
        'categorie' => $cat,
        'titre' => $titres_poetiques[$cat] ?? $cat,
        'total' => $total_articles,
        'produit' => $produit_en_vedette,
    ];
}
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
    <style>
        .hero-home {
            min-height: 100vh;
            height: auto;
            padding-bottom: 60px;
            position: relative;
            isolation: isolate;
            background:
                radial-gradient(circle at 15% 18%, rgba(212, 175, 55, 0.16), transparent 0 22%),
                radial-gradient(circle at 88% 14%, rgba(255, 255, 255, 0.08), transparent 0 18%),
                linear-gradient(180deg, rgba(5, 5, 5, 0.58), rgba(5, 5, 5, 0.88)),
                url('images/brume.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .hero-home::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(115deg, rgba(5, 5, 5, 0.1) 0%, rgba(5, 5, 5, 0) 38%, rgba(5, 5, 5, 0.48) 100%),
                linear-gradient(90deg, rgba(212, 175, 55, 0.08), transparent 22%, transparent 78%, rgba(212, 175, 55, 0.04));
            pointer-events: none;
            z-index: 0;
        }

        .hero-home::after {
            content: "";
            position: absolute;
            inset: auto 10% 8% auto;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.2), transparent 70%);
            filter: blur(10px);
            pointer-events: none;
            z-index: 0;
        }

        .hero-home .hero-content {
            width: min(1280px, calc(100% - 48px));
            padding: 120px 0 30px;
            position: relative;
            z-index: 1;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
            gap: 56px;
            align-items: center;
        }

        .hero-copy {
            text-align: left;
            padding: 24px 0;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border: 1px solid rgba(212, 175, 55, 0.35);
            background: rgba(5, 5, 5, 0.45);
            color: #f5dfaa;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 0.72rem;
            font-family: 'Cinzel', serif;
            border-radius: 999px;
            margin-bottom: 34px;
            backdrop-filter: blur(8px);
        }

        .hero-copy .citation {
            display: inline-block;
            margin-bottom: 10px;
        }

        .hero-copy h1 {
            margin-top: 0;
            margin-bottom: 14px;
        }

        .hero-copy .sous-titre {
            margin-top: 8px;
            margin-bottom: 0;
        }

        .hero-intro {
            max-width: 620px;
            margin: 34px 0 0;
            color: #eadcb8;
            font-size: 1.08rem;
            line-height: 2.05;
            letter-spacing: 0.45px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 38px;
        }

        .hero-actions .cta-primary,
        .hero-actions .cta-secondary,
        .univers-cta a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 22px;
            border-radius: 999px;
            font-family: 'Cinzel', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }

        .cta-primary {
            background: linear-gradient(135deg, #d4af37, #8f6f10);
            color: #050505;
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.22);
        }

        .cta-secondary {
            border: 1px solid rgba(212, 175, 55, 0.4);
            background: rgba(5, 5, 5, 0.45);
            color: #f8e7bf;
        }

        .hero-actions a:hover,
        .univers-cta a:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
        }

        .hero-kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 42px;
        }

        .hero-kpi {
            padding: 20px 18px;
            background: rgba(5, 5, 5, 0.46);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 22px;
            backdrop-filter: blur(10px);
        }

        .hero-kpi strong {
            display: block;
            color: #fff;
            font-family: 'Cinzel', serif;
            font-size: 0.98rem;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .hero-kpi span {
            color: #d1be92;
            font-size: 0.86rem;
            line-height: 1.65;
        }

        .hero-panel {
            position: relative;
            padding: 34px;
            border-radius: 30px;
            background: linear-gradient(180deg, rgba(18, 12, 4, 0.96), rgba(5, 5, 5, 0.88));
            border: 1px solid rgba(212, 175, 55, 0.18);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.45);
            overflow: hidden;
        }

        .hero-panel::before {
            content: "";
            position: absolute;
            inset: -20% auto auto -20%;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.25), transparent 70%);
            pointer-events: none;
        }

        .hero-panel::after {
            content: "";
            position: absolute;
            inset: auto -25% -30% auto;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.06), transparent 68%);
            pointer-events: none;
        }

        .hero-panel-image {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 360px;
            border-radius: 26px;
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.07), rgba(5, 5, 5, 0.12));
            border: 1px solid rgba(212, 175, 55, 0.12);
        }

        .hero-logo-large {
            width: min(250px, 58vw);
            max-width: 250px;
            filter: drop-shadow(0 0 30px rgba(212, 175, 55, 0.45));
        }

        .hero-card-copy {
            position: relative;
            z-index: 1;
            margin-top: 28px;
        }

        .hero-card-copy .citation {
            margin-bottom: 22px;
            display: block;
            text-align: left;
            line-height: 1.9;
        }

        .hero-card-list {
            display: grid;
            gap: 12px;
        }

        .hero-card-list div {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.03);
            color: #eadbb7;
            border: 1px solid rgba(212, 175, 55, 0.12);
        }

        .hero-card-list div span:first-child {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(212, 175, 55, 0.08);
            flex: 0 0 28px;
        }

        .univers-section {
            padding: 140px 20px 110px;
            background:
                radial-gradient(circle at top left, rgba(212, 175, 55, 0.08), transparent 30%),
                radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.04), transparent 28%),
                linear-gradient(180deg, #050505 0%, #090909 100%);
        }

        .univers-section .section-intro {
            max-width: 820px;
            margin: 0 auto;
            text-align: center;
            color: #d8c08a;
            line-height: 2;
            letter-spacing: 0.35px;
            font-size: 1.02rem;
        }

        .univers-grid {
            max-width: 1280px;
            margin: 56px auto 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 26px;
        }

        .univers-card {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            background: linear-gradient(180deg, rgba(18, 12, 4, 0.92), rgba(5, 5, 5, 0.98));
            border: 1px solid rgba(212, 175, 55, 0.16);
            box-shadow: 0 22px 50px rgba(0, 0, 0, 0.3);
            min-height: 100%;
            transform: translateY(0);
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
        }

        .univers-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.45);
            border-color: rgba(212, 175, 55, 0.38);
        }

        .univers-card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
            filter: saturate(0.88) contrast(1.05);
        }

        .univers-card-body {
            padding: 26px;
        }

        .univers-card-meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
            color: #e1c784;
            font-family: 'Cinzel', serif;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 0.72rem;
        }

        .univers-card h3 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 14px;
            line-height: 1.32;
        }

        .univers-card p {
            color: #cdb88b;
            line-height: 1.9;
            margin-bottom: 18px;
            min-height: 92px;
        }

        .univers-card .card-link {
            display: inline-flex;
            color: #f5dfaa;
            font-family: 'Cinzel', serif;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 0.72rem;
        }

        .univers-card .card-link::after {
            content: "↗";
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .univers-card:hover .card-link::after {
            transform: translate(2px, -2px);
        }

        .signal-section {
            padding: 0 20px 150px;
        }

        .signal-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .signal-card {
            padding: 34px;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(5, 5, 5, 0.95));
            border: 1px solid rgba(212, 175, 55, 0.12);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .signal-card:hover {
            transform: translateY(-4px);
            border-color: rgba(212, 175, 55, 0.3);
        }

        .signal-card .feature-icon {
            margin-bottom: 14px;
            display: inline-flex;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.08);
        }

        .signal-card h3 {
            color: #fff;
            font-family: 'Cinzel', serif;
            margin-bottom: 14px;
            letter-spacing: 1px;
            font-size: 1.2rem;
        }

        .signal-card p {
            color: #cdb88b;
            line-height: 1.95;
        }

        @media (max-width: 900px) {
            .hero-grid,
            .signal-grid {
                grid-template-columns: 1fr;
            }

            .hero-copy {
                text-align: center;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-kpis {
                grid-template-columns: 1fr;
            }

            .hero-card-copy .citation {
                text-align: center;
            }

            .hero-home .hero-content {
                width: calc(100% - 28px);
                padding-top: 88px;
            }

            .univers-section {
                padding-top: 110px;
            }
        }
    </style>
</head>
<body>
    <header class="hero-section hero-home">
        <nav>
            <a href="index.php" class="active">✦ Accueil</a>
            <a href="catalogue.php">✿ Catalogue</a>
            <a href="foret.php">✾ Sanctuaire</a>
            <a href="repos_des_fideles.php">✤ Repos des Fidèles</a>
            <a href="ceremonies.php">❦ L'Art de l'Adieu</a>
            <a href="contact.php">❋ Conciergerie</a>

            <a href="panier.php" style="margin-left: auto;">✵ Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="lock-link">◇ Quitter</a>
            <?php else: ?>
                <a href="login.php" class="lock-link">◆ Entrer</a>
            <?php endif; ?>
        </nav>
        
        <div class="hero-content">
            <div class="hero-grid">
                <div class="hero-copy">
                    <span class="hero-eyebrow">Rituel de passage · écrin de mémoire</span>
                    <span class="citation">"La mort n'est pas une fin, c'est une métamorphose"</span>
                    <h1>LA DERNIÈRE<br>DEMEURE</h1>
                    <h2 class="sous-titre">L'Éternité pour Écrin</h2>
                    <p class="hero-intro">
                        Une entrée plus ample, plus lente, pensée comme un seuil. Ici, chaque univers se découvre comme un geste,
                        chaque objet comme une présence, chaque mot comme une offrande.
                    </p>
                    <div class="hero-actions">
                        <a href="#univers" class="cta-primary">Traverser les univers</a>
                        <a href="contact.php" class="cta-secondary">Composer un hommage</a>
                    </div>
                    <div class="hero-kpis">
                        <div class="hero-kpi">
                            <strong>Silence</strong>
                            <span>Une présence sobre, tenue, qui laisse respirer le souvenir.</span>
                        </div>
                        <div class="hero-kpi">
                            <strong>Rituel</strong>
                            <span>Des gestes simples pour accompagner l'adieu avec justesse.</span>
                        </div>
                        <div class="hero-kpi">
                            <strong>Présence</strong>
                            <span>Des matières, des fleurs et des formes qui prolongent l'hommage.</span>
                        </div>
                    </div>
                </div>

                <div class="hero-panel">
                    <div class="hero-panel-image">
                        <img src="images/logo.svg" alt="La Dernière Demeure" class="hero-logo hero-logo-large">
                    </div>
                    <div class="hero-card-copy">
                        <span class="citation">"Nous façonnons des passages calmes, des présences claires, des adieux qui ne s'effacent pas."</span>
                        <div class="hero-card-list">
                            <div><span>✦</span><span>Une porte d'entrée plus lente, plus ample, plus cérémonielle</span></div>
                            <div><span>✾</span><span>Un sanctuaire visuel qui respire et laisse place au silence</span></div>
                            <div><span>✵</span><span>Des repères nets pour rejoindre les univers sans perdre l'atmosphère</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="scroll-indicator">
                <span>Entrer dans l'univers</span>
                <div class="arrow"></div>
            </div>
        </div>
    </header>

    <section id="univers" class="univers-section">
        <h2 class="prestige-title" style="text-align: center; margin-bottom: 18px;">Les seuils de la maison</h2>
        <p class="section-intro">
            Une sélection resserrée pour laisser chaque univers exister pleinement.
            L'accueil n'expose pas une liste: il ouvre des seuils, des ambiances et des présences.
        </p>

        <div class="univers-grid">
            <?php foreach ($univers_en_avant as $univers): ?>
                <article class="univers-card">
                    <?php if (!empty($univers['produit']['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($univers['produit']['image_path']); ?>" alt="<?php echo htmlspecialchars($univers['titre']); ?>">
                    <?php else: ?>
                        <div style="height: 240px; background: linear-gradient(135deg, rgba(212,175,55,0.12), rgba(5,5,5,0.75));"></div>
                    <?php endif; ?>
                    <div class="univers-card-body">
                        <div class="univers-card-meta">
                            <span><?php echo htmlspecialchars($univers['categorie']); ?></span>
                            <span><?php echo $univers['total']; ?> pièce<?php echo $univers['total'] > 1 ? 's' : ''; ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($univers['titre']); ?></h3>
                        <p>
                            <?php if (!empty($univers['produit'])): ?>
                                <?php echo htmlspecialchars($univers['produit']['nom']); ?>
                                <br>
                                <strong style="color: #fff;">À partir de <?php echo number_format((float) $univers['produit']['prix'], 2, ',', ' '); ?> €</strong>
                            <?php else: ?>
                                Un univers à découvrir dans la maison.
                            <?php endif; ?>
                        </p>
                        <a class="card-link" href="catalogue.php?cat=<?php echo urlencode($univers['categorie']); ?>#catalogue">Traverser ce seuil</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="univers-cta" style="text-align: center; margin-top: 36px;">
            <a href="catalogue.php" class="cta-primary">Parcourir l'Offrande</a>
        </div>
    </section>

    <section class="signal-section">
        <div class="signal-grid">
            <div class="signal-card">
                <span class="feature-icon">❦</span>
                <h3>Cérémonies</h3>
                <p>Des parcours composés avec retenue, pour que la forme accompagne l'émotion au lieu de la couvrir.</p>
            </div>
            <div class="signal-card">
                <span class="feature-icon">✾</span>
                <h3>Sanctuaire</h3>
                <p>Un lieu de respiration, plus contemplatif, où l'image et la matière laissent de l'espace au silence.</p>
            </div>
            <div class="signal-card">
                <span class="feature-icon">❋</span>
                <h3>Conciergerie</h3>
                <p>Un point d'entrée discret pour demander un accompagnement, une écoute et une orientation humaine.</p>
            </div>
        </div>
    </section>

    <div id="toast" class="toast"></div>

    <script src="script.js"></script>

    <?php include 'footer.php'; ?>
</body>
</html>