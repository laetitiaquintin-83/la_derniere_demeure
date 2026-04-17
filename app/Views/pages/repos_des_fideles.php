<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Le Repos des Fidèles | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .animal-hero {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.6) 0%, rgba(181, 148, 16, 0.1) 100%), 
                        url('images/animaux.jpg') no-repeat center center / cover !important;
            height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            text-align: center;
        }

        .hero-content h1 {
            font-family: 'Cinzel', serif;
            font-size: 4rem;
            color: #d4af37;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
        }

        .hero-content h2 {
            color: #b59410;
            font-size: 1.8rem;
            font-weight: 300;
            margin-top: 15px;
            letter-spacing: 2px;
        }

        .hero-content .citation {
            color: #ccc;
            font-size: 1.3rem;
            font-style: italic;
            display: block;
            margin-bottom: 40px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7);
        }

        /* Section d'hommage */
        .tribute-section {
            background: #050505;
            padding: 80px 40px;
            color: #ddd;
            line-height: 1.9;
        }

        .tribute-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .tribute-title {
            text-align: center;
            color: #d4af37;
            font-size: 2.5rem;
            font-family: 'Cinzel', serif;
            margin-bottom: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .tribute-text {
            font-size: 1.1rem;
            text-align: justify;
            margin-bottom: 40px;
            color: #ccc;
        }

        .tribute-text p {
            margin: 0 0 25px 0;
        }

        /* Section services */
        .services-section {
            background: #0a0a0a;
            padding: 80px 40px;
            border-top: 2px solid #b59410;
        }

        .services-title {
            text-align: center;
            color: #d4af37;
            font-size: 2.5rem;
            font-family: 'Cinzel', serif;
            margin-bottom: 60px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .service-card {
            background: #050505;
            padding: 35px;
            border-left: 4px solid #b59410;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            border-left-color: #d4af37;
            background: #0a0a0a;
            transform: translateX(5px);
        }

        .service-card h3 {
            color: #b59410;
            font-family: 'Cinzel', serif;
            font-size: 1.4rem;
            margin: 0 0 15px 0;
            text-transform: uppercase;
        }

        .service-card p {
            color: #999;
            font-size: 0.95rem;
            line-height: 1.7;
            margin: 0;
        }

        /* Section appel à action */
        .cta-section {
            background: linear-gradient(135deg, rgba(181, 148, 16, 0.15), rgba(0, 0, 0, 0.5));
            padding: 80px 40px;
            text-align: center;
            border-top: 2px solid #b59410;
        }

        .cta-text {
            max-width: 800px;
            margin: 0 auto 40px;
            font-size: 1.2rem;
            color: #ccc;
            font-style: italic;
            line-height: 1.8;
        }

        .btn-contact {
            display: inline-block;
            background: #b59410;
            color: #000;
            padding: 15px 50px;
            text-decoration: none;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            border: 2px solid #b59410;
        }

        .btn-contact:hover {
            background: transparent;
            color: #d4af37;
            border-color: #d4af37;
        }

        /* Navigation */
        nav {
            width: 100%;
            padding: 25px 60px;
            display: flex;
            gap: 50px;
            z-index: 10;
            background: linear-gradient(to bottom, rgba(5, 5, 5, 0.7), transparent);
            justify-content: center;
            align-items: center;
        }

        nav a {
            color: #b3b3b3;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 0.75em;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Cinzel', serif;
            padding: 8px 0;
            border-bottom: 2px solid transparent;
        }

        nav a:hover, nav a.active {
            color: #d4af37;
            border-bottom-color: #d4af37;
        }

        @media (max-width: 768px) {
            .animal-hero {
                height: 60vh;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            nav {
                flex-wrap: wrap;
                gap: 20px;
            }

            .tribute-section, .services-section, .cta-section {
                padding: 50px 20px;
            }
        }

        /* Section urnes */
        .urnes-section {
            background: #050505;
            padding: 80px 40px;
            border-top: 2px solid #b59410;
        }

        .urnes-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .urnes-title {
            text-align: center;
            color: #d4af37;
            font-size: 2.5rem;
            font-family: 'Cinzel', serif;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .urnes-subtitle {
            text-align: center;
            color: #999;
            font-size: 1.1rem;
            margin-bottom: 60px;
            font-style: italic;
        }

        .urnes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 35px;
        }

        .urne-card {
            background: #0a0a0a;
            border: 1px solid #1a1a1a;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .urne-card:hover {
            border-color: #b59410;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(181, 148, 16, 0.2);
        }

        .urne-image {
            width: 100%;
            height: 280px;
            overflow: hidden;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .urne-image img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .urne-card:hover .urne-image img {
            transform: scale(1.05);
        }

        .urne-info {
            padding: 25px;
        }

        .urne-info h3 {
            color: #d4af37;
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
            margin: 0 0 12px 0;
            text-transform: uppercase;
        }

        .urne-details {
            color: #b59410;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 15px 0;
        }

        .urne-description {
            color: #999;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0 0 20px 0;
            min-height: 60px;
        }

        .urne-price {
            display: block;
            color: #d4af37;
            font-size: 1.4rem;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .btn-add-cart.btn-urne {
            display: block;
            width: 100%;
            background: #b59410;
            color: #000;
            padding: 12px;
            text-decoration: none;
            text-align: center;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            border: 2px solid #b59410;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-add-cart.btn-urne:hover {
            background: transparent;
            color: #d4af37;
            border-color: #d4af37;
        }

        @media (max-width: 768px) {
            .urnes-section {
                padding: 50px 20px;
            }

            .urnes-title {
                font-size: 1.8rem;
            }

            .urnes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header class="hero-section animal-hero">
        <nav>
            <a href="index.php" class="active">✦ Accueil</a>
            <a href="catalogue.php">✿ Catalogue</a>
            <a href="foret.php">✾ Le Sanctuaire</a>
            <a href="repos_des_fideles.php">✤ Repos des Fidèles</a>
            <a href="ceremonies.php">❦ L'Art de l'Adieu</a>
            <a href="contact.php">❋ Conciergerie</a>
            <a href="panier.php" style="margin-left: auto;">✵ L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
        </nav>
        
        <div class="hero-content">
            <span class="citation">"Un battement de cœur qui s'éteint, une loyauté qui demeure."</span>
            <h1>LE REPOS<br>DES FIDÈLES</h1>
            <h2>L'Éternité pour nos Compagnons</h2>
        </div>
    </header>

    <!-- SECTION HOMMAGE -->
    <section class="tribute-section">
        <div class="tribute-container">
            <h2 class="tribute-title">Un Hommage à Nos Fidèles Compagnons</h2>
            
            <div class="tribute-text">
                <p>Ils ont partagé notre quotidien avec une présence silencieuse et bienveillante. Chaque jour, à travers leurs regards remplis de tendresse, leurs gestes pleins de grâce, leur amour inconditionnel, ils nous ont enseigné les vraies valeurs du dévouement et de la fidélité.</p>
                
                <p>Nos compagnons ne sont pas de simples créatures. Ils sont les confidents de nos jours heureux, les réconfortants lors de nos peines, les purs reflets d'une innocence que l'humanité a oubliée. Leur loyauté n'a d'égale que l'amour qu'ils nous donnent, jour après jour, sans attendre en retour que notre affection.</p>
                
                <p>Lorsque vient le moment de leur dernier repos, nous croyons fermement qu'une telle tendresse mérite un hommage digne, un passage respectueux vers l'éternité. Car au sein de La Dernière Demeure, nous savons que la mort n'est pas une fin—c'est une métamorphose, une transformation où le cœur continue de battre dans nos souvenirs.</p>
                
                <p>Le Repos des Fidèles n'est pas un simple cimetière. C'est un sanctuaire du souvenir, un lieu où la nature embrasse à jamais ceux qui ont illuminé nos existences.</p>
            </div>
        </div>
    </section>

    <!-- SECTION SERVICES -->
    <section class="services-section">
        <h2 class="services-title">Notre Approche Bienveillante</h2>
        
        <div class="services-grid">
            <div class="service-card">
                <h3>✾ Place au Cœur de la Nature</h3>
                <p>Le Repos des Fidèles n'est pas un simple cimetière animalier. C'est une extension de notre sanctuaire forestier où chaque compagnon retrouve le cycle naturel de la vie, sous la protection des grands arbres millénaires.</p>
            </div>

            <div class="service-card">
                <h3>🕊️ Crémation Respectueuse</h3>
                <p>En collaboration avec le groupe Incinéris, nous garantissons une traçabilité totale et un accompagnement empreint d'empathie. Nous privilégions les urnes biodégradables qui nourrissent la terre du sanctuaire.</p>
            </div>

            <div class="service-card">
                <h3>🕯️ Le Rituel d'Adieu</h3>
                <p>Nous organisons des cérémonies personnalisées pour honorer le lien unique qui vous unissait. Un moment de recueillement privé au milieu des bois pour un dernier hommage émotionnel et paisible.</p>
            </div>

            <div class="service-card">
                <h3>🪦 Urnes Commémoratives</h3>
                <p>Découvrez notre sélection d'urnes spécialement conçues pour honorer vos compagnons. Des créations poétiques qui reflètent la beauté de la vie qu'ils ont vécue à vos côtés.</p>
            </div>

            <div class="service-card">
                <h3>📖 Murs du Souvenir</h3>
                <p>Gravez le nom aimé de votre fidèle compagnon sur notre Mur du Souvenir. Un endroit durable où sa mémoire vivra à jamais parmi les fleurs et les arbres du sanctuaire.</p>
            </div>

            <div class="service-card">
                <h3>💌 Soutien et Conseil</h3>
                <p>Notre équipe de conciergerie est formée pour vous accompagner avec délicatesse lors de ce moment difficile. Vos questions, vos émotions, vos besoins—nous les écoutons et les respectons.</p>
            </div>
        </div>
    </section>

    <!-- SECTION URNES -->
    <section class="urnes-section">
        <div class="urnes-container">
            <h2 class="urnes-title">Nos Urnes Commémoratives</h2>
            <p class="urnes-subtitle">Découvrez notre collection d'urnes poétiques pour honorer vos fidèles compagnons</p>
            
            <div class="urnes-grid">
                <?php
                // Récupération des urnes pour animaux
                $query = $pdo->prepare("SELECT * FROM catalogue_funeraire WHERE categorie = 'Animaux' ORDER BY id DESC");
                $query->execute();
                $urnes = $query->fetchAll();

                if (count($urnes) > 0) {
                    foreach ($urnes as $urne) : ?>
                        <div class="urne-card">
                            <div class="urne-image">
                                <img src="<?php echo htmlspecialchars($urne['image_path']); ?>" alt="<?php echo htmlspecialchars($urne['nom']); ?>">
                            </div>
                            
                            <div class="urne-info">
                                <h3><?php echo htmlspecialchars($urne['nom']); ?></h3>
                                
                                <?php 
                                $details = array_filter([$urne['essence_bois'], $urne['couleur_velours']]);
                                if (!empty($details)): ?>
                                    <p class="urne-details"><?php echo htmlspecialchars(implode(' • ', $details)); ?></p>
                                <?php endif; ?>

                                <?php if (!empty($urne['description'])): ?>
                                    <p class="urne-description"><?php echo htmlspecialchars($urne['description']); ?></p>
                                <?php endif; ?>
                                
                                <span class="urne-price"><?php echo number_format($urne['prix'], 2, ',', ' '); ?> €</span>
                                <a href="#" class="btn-add-cart btn-urne" 
                                   data-id="<?php echo htmlspecialchars($urne['id']); ?>" 
                                   data-nom="<?php echo htmlspecialchars($urne['nom']); ?>" 
                                   data-prix="<?php echo htmlspecialchars($urne['prix']); ?>">
                                   Ajouter à l'Offrande
                                </a>
                            </div>
                        </div>
                    <?php endforeach;
                } else {
                    echo '<p style="grid-column: 1 / -1; text-align: center; color: #999;">Nos urnes pour vos compagnons seront bientôt disponibles.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- SECTION JARDIN DES SOUVENIRS -->
    <section class="jardin-souvenirs" style="background: #050505; padding: 60px 0; margin-top: 50px; border-top: 1px dashed #d4af37;">
        <div class="container" style="max-width: 800px; margin: 0 auto; text-align: center; padding: 0 40px;">
            <h2 style="font-family: 'Cinzel', serif; color: #d4af37; font-size: 2rem;">Le Jardin des Souvenirs</h2>
            <p style="font-style: italic; color: #888; margin-bottom: 30px;">Laissez une trace, un mot, une pensée pour ceux qui ne sont plus là.</p>

            <form action="traitement_jardin.php" method="POST" enctype="multipart/form-data" style="background: #111; padding: 30px; border: 1px solid #222; margin-top: 30px; margin-bottom: 50px;">
                <!-- 🔐 TOKEN CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(genererTokenCSRF()); ?>">
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <input type="text" name="nom_proprietaire" placeholder="Votre Nom" required style="flex: 1; padding: 10px; background: #000; border: 1px solid #333; color: white;">
                    <input type="text" name="nom_animal" placeholder="Nom de votre compagnon" required style="flex: 1; padding: 10px; background: #000; border: 1px solid #333; color: white;">
                </div>
                <textarea name="message" placeholder="Votre message d'adieu..." rows="4" required style="width: 100%; padding: 10px; background: #000; border: 1px solid #333; color: white; margin-bottom: 15px; box-sizing: border-box;"></textarea>
                
                <div style="margin-bottom: 15px;">
                    <label style="color: #d4af37; font-size: 0.8rem; display: block; margin-bottom: 5px;">Joindre un portrait (Optionnel)</label>
                    <input type="file" name="photo_compagnon" accept="image/*" style="color: #888;">
                </div>
                
                <button type="submit" class="btn-crypt" style="padding: 10px 30px; background: #d4af37; color: black; border: none; font-family: 'Cinzel', serif; cursor: pointer; font-weight: bold; text-transform: uppercase;">Déposer une pensée</button>
            </form>

            <h3 style="font-family: 'Cinzel', serif; color: #d4af37; font-size: 1.5rem; margin-bottom: 30px;">Les Témoignages Déposés</h3>
            
            <div class="memorial-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <?php
                $messages = $pdo->query("SELECT * FROM livre_dor_animaux WHERE approuve = 1 ORDER BY date_publication DESC")->fetchAll();
                if (count($messages) > 0) {
                    foreach ($messages as $m): ?>
                        <div class="memorial-card" style="background: #111; border: 1px solid #222; padding: 20px; text-align: center;">
                            <?php if ($m['photo_path']): ?>
                                <img src="<?php echo htmlspecialchars($m['photo_path']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #d4af37; margin-bottom: 15px;">
                            <?php endif; ?>
                            
                            <h3 style="color: #d4af37; font-family: 'Cinzel'; margin: 0;"><?php echo htmlspecialchars_decode($m['nom_animal']); ?></h3>
                            <p style="font-size: 0.8rem; color: #666; margin-bottom: 10px;">Compagnon de <?php echo htmlspecialchars_decode($m['nom_proprietaire']); ?></p>
                            <p style="font-style: italic; color: #bbb;">"<?php echo htmlspecialchars_decode($m['message']); ?>"</p>
                        </div>
                    <?php endforeach;
                } else {
                    echo '<p style="grid-column: 1 / -1; text-align: center; color: #999; padding: 40px;">Soyez le premier à déposer une pensée...</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- SECTION CTA -->
    <section class="cta-section">
        <p class="cta-text">
            "Car l'amour que nous partageons avec nos compagnons ne connaît pas de frontière, pas même celle de l'éternité. Chez La Dernière Demeure, nous transformons le deuil en souvenir, la perte en héritage intemporel."
        </p>
        <a href="contact.php" class="btn-contact">Entrer en Contact</a>
    </section>

    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>

