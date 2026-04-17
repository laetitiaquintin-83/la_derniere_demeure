<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/bootstrap.php';

$homeController = new HomeController(new HomePageModel($pdo));
$homeData = $homeController->index();
extract($homeData, EXTR_SKIP);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>La Dernière Demeure | L'Éternité pour Écrin</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Cormorant+Garamond:ital,wght@0,300;1,400&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Cormorant+Garamond:ital,wght@0,300;1,400&display=block" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --gold-soft: #c8a24a;
            --gold-bright: #e2c46b;
            --ivory: #f3ebd1;
            --bg: #050505;
        }
        body { 
            margin: 0; 
            background: var(--bg); 
            color: var(--ivory); 
            font-family: 'Cormorant Garamond', serif; 
            overflow-x: hidden;
            padding-top: 0;
        }

        /* --- NAVIGATION --- */
        .header-nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            display: flex; justify-content: space-around; align-items: center;
            padding: 20px 0; background: linear-gradient(to bottom, rgba(0,0,0,0.95), rgba(0,0,0,0.3));
            border-bottom: 1.5px solid rgba(212, 175, 55, 0.3);
            backdrop-filter: blur(12px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(212, 175, 55, 0.08);
        }
        .nav-link { 
            color: var(--gold); text-decoration: none; font-family: 'Cinzel'; font-size: 0.7rem; 
            letter-spacing: 2px; text-transform: uppercase; display: flex; align-items: center; gap: 8px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }
        .nav-link span {
            display: inline-block;
            filter: drop-shadow(0 0 12px rgba(212, 175, 55, 0.4));
            font-size: 1.2em;
            transition: all 0.4s ease;
        }
        .nav-link:hover span {
            transform: scale(1.3) rotate(-8deg);
            filter: drop-shadow(0 0 24px rgba(212, 175, 55, 0.7));
        }
        .admin-entry {
            border: 1px solid rgba(212, 175, 55, 0.45);
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(212, 175, 55, 0.08);
        }
        .admin-entry:hover {
            background: rgba(212, 175, 55, 0.16);
            border-color: rgba(212, 175, 55, 0.7);
        }
        .logo-nav { 
            width: 68px; 
            filter: drop-shadow(0 0 16px rgba(212, 175, 55, 0.4)) drop-shadow(0 4px 8px rgba(0,0,0,0.5));
            transition: all 0.4s ease;
            animation: logoPulse 4s ease-in-out infinite;
        }
        .logo-nav:hover { 
            filter: drop-shadow(0 0 30px rgba(212, 175, 55, 0.7)) drop-shadow(0 6px 12px rgba(0,0,0,0.6));
            transform: scale(1.05);
        }
        @keyframes logoPulse {
            0%, 100% { filter: drop-shadow(0 0 16px rgba(212, 175, 55, 0.4)) drop-shadow(0 4px 8px rgba(0,0,0,0.5)); }
            50% { filter: drop-shadow(0 0 28px rgba(212, 175, 55, 0.6)) drop-shadow(0 4px 8px rgba(0,0,0,0.5)); }
        }

        /* --- HERO MONUMENTAL --- */
        .hero {
            height: 100vh; 
            background: url('images/brume.jpg') no-repeat center center / cover;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;
            position: relative; margin-top: 0;
            width: 100%;
            overflow: hidden;
            background-size: 108%;
            animation: heroPan 24s ease-in-out infinite alternate;
        }
        @keyframes heroPan {
            0% {
                background-position: 48% 52%;
                background-size: 106%;
            }
            100% {
                background-position: 52% 48%;
                background-size: 111%;
            }
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.32));
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            inset: -8% -15%;
            background:
                radial-gradient(circle at 20% 35%, rgba(255, 255, 255, 0.12), transparent 42%),
                radial-gradient(circle at 75% 60%, rgba(255, 255, 255, 0.08), transparent 45%);
            mix-blend-mode: screen;
            opacity: 0.45;
            pointer-events: none;
            animation: mistDrift 18s ease-in-out infinite alternate;
        }
        @keyframes mistDrift {
            0% {
                transform: translateX(-2%) translateY(0) scale(1);
                opacity: 0.35;
            }
            100% {
                transform: translateX(2%) translateY(-1%) scale(1.04);
                opacity: 0.55;
            }
        }
        @keyframes heroGlow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }
        .title-top { 
            font-family: 'Cinzel'; 
            font-size: clamp(4rem, 10vw, 8rem); 
            letter-spacing: 2vw; 
            margin: 0; 
            line-height: 0.9; 
            color: var(--gold-bright);
            text-shadow: 0 0 10px rgba(0,0,0,0.5), 0 0 22px rgba(212, 175, 55, 0.35);
            position: relative;
            z-index: 10;
        }
        @keyframes titleFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .title-bottom { 
            font-family: 'Cinzel'; font-size: clamp(3.5rem, 8vw, 6.5rem); letter-spacing: 1.5vw; 
            margin: -10px 0 0 0; 
            color: var(--gold);
            text-shadow: 0 0 40px rgba(212,175,55,0.4), 0 12px 30px rgba(0,0,0,0.9);
            animation: titleFloat 6s ease-in-out infinite 0.2s;
            position: relative;
            z-index: 2;
        }
        .tagline-gold { 
            font-family: 'Cinzel'; color: var(--gold-soft); letter-spacing: 4px; 
            font-size: clamp(0.9rem, 2vw, 1.5rem); margin: 40px 0 0 0;
            text-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
            animation: fadeInUp 1.2s ease 0.8s both;
            position: relative;
            z-index: 2;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* --- BIENVENUE --- */
        .welcome-container {
            background: linear-gradient(155deg, rgba(20, 18, 15, 0.85), rgba(5, 5, 5, 0.6));
            border: 1.5px solid rgba(212, 175, 55, 0.3);
            padding: 60px 50px; margin: -60px auto 80px; max-width: 850px; 
            backdrop-filter: blur(20px);
            position: relative; z-index: 10;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6), inset 0 0 0 1px rgba(212, 175, 55, 0.15);
            animation: welcomeFadeIn 1.2s ease 0.5s both;
        }
        @keyframes welcomeFadeIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .welcome-container h2 { 
            font-family: 'Cinzel'; color: var(--gold); margin: 0 0 25px 0; 
            font-size: clamp(1.3rem, 3vw, 2rem);
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .quote-italic { 
            font-style: italic; font-size: clamp(1rem, 2.2vw, 1.4rem); 
            margin: 0 0 30px 0; color: #fff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }
        .welcome-container p {
            font-size: clamp(0.95rem, 1.8vw, 1.1rem);
            line-height: 1.8;
            color: rgba(243, 235, 209, 0.85);
        }

        /* --- GRILLE PRODUITS --- */
        .product-section { 
            padding: clamp(60px, 10vw, 100px) 5%; max-width: 1200px; margin: 0 auto;
        }
        .product-section > h2 {
            text-align: center;
            font-family: 'Cinzel';
            color: var(--gold);
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 60px;
        }
        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 40px;
        }
        .product-card { 
            height: 450px; 
            border: 1.5px solid rgba(212, 175, 55, 0.28); 
            position: relative; overflow: hidden; text-decoration: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6), 0 0 40px rgba(212, 175, 55, 0.1);
            transition: all 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: cardAppear 0.8s ease both;
        }
        .product-card:nth-child(2) { animation-delay: 0.15s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:nth-child(4) { animation-delay: 0.45s; }
        @keyframes cardAppear {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .product-card img { 
            width: 100%; height: 100%; object-fit: cover;
            filter: grayscale(60%) brightness(0.5) contrast(1.1);
            transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 40px 100px rgba(212, 175, 55, 0.2), 0 0 60px rgba(212, 175, 55, 0.15);
            border-color: rgba(212, 175, 55, 0.6);
        }
        .product-card:hover img { 
            filter: grayscale(20%) brightness(0.85) contrast(1.2) saturate(1.3);
            transform: scale(1.1);
        }
        .product-info { 
            position: absolute; inset: 0; display: flex; flex-direction: column; 
            justify-content: flex-end; padding: 40px; 
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.85));
            transition: all 0.6s ease;
        }
        .product-card:hover .product-info {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.75));
        }
        .product-info h3 { 
            font-family: 'Cinzel'; color: #fff; margin: 0; 
            font-size: clamp(1.1rem, 2vw, 1.5rem);
            letter-spacing: 1px;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.8);
        }
        .product-label {
            color: var(--gold); font-size: 0.75rem; letter-spacing: 2px;
            text-transform: uppercase; margin-bottom: 8px;
            opacity: 0.8;
            transition: all 0.5s ease;
        }
        .product-card:hover .product-label {
            opacity: 1;
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.5));
        }

        /* --- FOOTER COMPLET --- */
        .footer-main { 
            background: linear-gradient(180deg, #000 0%, #0a0805 100%);
            border-top: 1.5px solid rgba(212, 175, 55, 0.3);
            padding: 100px 8% 50px; 
            box-shadow: inset 0 1px 0 rgba(212, 175, 55, 0.15);
        }
        .footer-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 80px; margin-bottom: 60px; max-width: 1200px; margin-left: auto; margin-right: auto;
        }
        .footer-col h4 { 
            font-family: 'Cinzel'; color: var(--gold); text-transform: uppercase; 
            letter-spacing: 2px; margin: 0 0 20px 0; font-size: 0.95rem;
        }
        .footer-col h4::before {
            content: '◆ '; opacity: 0.6; margin-right: 8px;
        }
        .footer-col ul { list-style: none; padding: 0; margin: 0; }
        .footer-col li { margin-bottom: 12px; }
        .footer-col p { margin: 0 0 8px 0; line-height: 1.6; }
        .footer-col a { 
            color: rgba(243, 235, 209, 0.65); text-decoration: none; 
            transition: all 0.35s ease; font-size: 0.95rem;
            position: relative;
        }
        .footer-col a::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--gold);
            transition: width 0.35s ease;
        }
        .footer-col a:hover { color: var(--gold); }
        .footer-col a:hover::after { width: 100%; }
        .forest-section {
            display: flex;
            align-items: center;
            min-height: 100vh;
            background: #050505;
            overflow: hidden;
            position: relative;
        }
        
        .forest-content {
            flex: 1;
            padding: clamp(40px, 8vw, 80px);
            z-index: 2;
            max-width: 600px;
        }
        
        .forest-content h2 {
            font-family: 'Cinzel';
            color: var(--gold);
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0 0 30px 0;
            line-height: 1.3;
        }
        
        .forest-subtitle {
            font-family: 'Cormorant Garamond';
            font-size: clamp(1.1rem, 2vw, 1.5rem);
            font-style: italic;
            color: rgba(243, 235, 209, 0.85);
            margin-bottom: 40px;
            line-height: 1.8;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }
        
        .forest-text {
            font-family: 'Cormorant Garamond';
            font-size: clamp(0.95rem, 1.8vw, 1.1rem);
            color: rgba(243, 235, 209, 0.75);
            line-height: 1.9;
            margin-bottom: 40px;
        }
        
        .forest-text h3 {
            font-family: 'Cinzel';
            color: var(--gold);
            font-size: clamp(1.2rem, 2vw, 1.6rem);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 30px 0 20px 0;
        }
        
        .forest-text p {
            margin: 0 0 20px 0;
        }
        
        .forest-quote {
            font-style: italic;
            color: var(--gold);
            font-size: clamp(1rem, 1.8vw, 1.3rem);
            margin: 40px 0;
            padding: 20px 0 20px 20px;
            border-left: 2px solid var(--gold);
            text-shadow: 0 0 12px rgba(212, 175, 55, 0.3);
        }
        
        .forest-button {
            display: inline-block;
            padding: 16px 40px;
            border: 1.5px solid var(--gold);
            color: var(--gold);
            text-decoration: none;
            font-family: 'Cinzel';
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            background: transparent;
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(212, 175, 55, 0.2);
        }
        
        .forest-button::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        
        .forest-button:hover {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(212, 175, 55, 0.05));
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.4), inset 0 0 0 1px rgba(212, 175, 55, 0.5);
            color: #fff;
        }
        
        .forest-button:hover::before {
            transform: translateX(100%);
        }
        
        .forest-image-container {
            flex: 1;
            height: 100%;
            position: relative;
            min-height: 600px;
            overflow: hidden;
        }
        
        .forest-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(1) contrast(1.15);
            animation: forestBreathe 6s ease-in-out infinite;
        }
        
        @keyframes forestBreathe {
            0%, 100% { transform: scale(1); filter: brightness(0.9) contrast(1.1); }
            50% { transform: scale(1.05); filter: brightness(1.1) contrast(1.2); }
        }
        
        .fireflies {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }
        
        .firefly {
            position: absolute;
            width: 4px;
            height: 4px;
            background: radial-gradient(circle, #7fffb3 0%, rgba(127, 255, 179, 0.3) 70%);
            border-radius: 50%;
            filter: blur(0.5px) drop-shadow(0 0 6px #7fffb3);
            animation: fireflyFloat 8s ease-in-out infinite;
        }
        
        @keyframes fireflyFloat {
            0% { 
                opacity: 0;
                transform: translateY(100px) translateX(0);
            }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% {
                opacity: 0;
                transform: translateY(-100px) translateX(50px);
            }
        }
        
        .firefly:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 7s; }
        .firefly:nth-child(2) { left: 20%; animation-delay: 1s; animation-duration: 8s; }
        .firefly:nth-child(3) { left: 30%; animation-delay: 2s; animation-duration: 7.5s; }
        .firefly:nth-child(4) { left: 40%; animation-delay: 0.5s; animation-duration: 8.5s; }
        .firefly:nth-child(5) { left: 50%; animation-delay: 1.5s; animation-duration: 7.2s; }
        .firefly:nth-child(6) { left: 60%; animation-delay: 2.5s; animation-duration: 8s; }
        .firefly:nth-child(7) { left: 70%; animation-delay: 0.8s; animation-duration: 7.8s; }
        .firefly:nth-child(8) { left: 80%; animation-delay: 1.8s; animation-duration: 8.2s; }
        .firefly:nth-child(9) { left: 90%; animation-delay: 2.2s; animation-duration: 7.5s; }
        
        @media (max-width: 1000px) {
            .forest-section {
                flex-direction: column;
                min-height: auto;
            }
            .forest-image-container {
                min-height: 400px;
                order: -1;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .hero,
            .hero::after,
            .title-bottom,
            .tagline-gold,
            .logo-nav,
            .forest-image-container img,
            .firefly {
                animation: none !important;
            }
        }
    </style>
</head>
<body>

    <nav class="header-nav">
        <div style="display:flex; gap:25px;">
            <a href="catalogue.php" class="nav-link"><span>✿</span> Catalogue</a>
            <a href="foret.php" class="nav-link"><span>✾</span> Sanctuaire</a>
            <a href="ceremonies.php" class="nav-link"><span>❦</span> L'Art de l'Adieu</a>
        </div>
        <img src="images/logo.png" class="logo-nav" alt="Logo">
        <div style="display:flex; gap:25px;">
            <a href="repos_des_fideles.php" class="nav-link"><span>✤</span> Repos des Fidèles</a>
            <a href="contact.php" class="nav-link"><span>❋</span> Conciergerie</a>
            <?php if (!empty($_SESSION['admin_connecte'])): ?>
                <a href="admin.php" class="nav-link admin-entry"><span>✦</span> Admin</a>
            <?php else: ?>
                <a href="login.php" class="nav-link admin-entry"><span>✦</span> Entrer</a>
            <?php endif; ?>
            <a href="panier.php" class="nav-link">✵ Offrande (<?php echo $nombre_articles; ?>)</a>
        </div>
    </nav>

    <header class="hero">
        <h1 class="title-top">LA DERNIÈRE</h1>
        <h1 class="title-bottom">DEMEURE</h1>
        <p class="tagline-gold">L'ÉTERNITÉ POUR ÉCRIN</p>
    </header>

    <main>
        <div class="welcome-container">
            <p class="quote-italic">"Le passage est une ombre, la mémoire est une lumière."</p>
            <h2>BIENVENUE A LA DERNIERE DEMEURE</h2>
            <p>Depuis des générations, nous honorons la transition suprême avec dignité, respect et raffinement. Notre maison est un sanctuaire de la mémoire où chaque détail est pensé pour célébrer la vie.</p>
        </div>

        <section class="product-section">
            <h2>Les Portails de l'Éternité</h2>
            <div class="product-grid">
                <?php foreach ($categories as $cat): ?>
                <a href="catalogue.php?cat=<?php echo urlencode($cat); ?>" class="product-card">
                    <img src="<?php echo htmlspecialchars($category_images[$cat]); ?>" alt="<?php echo htmlspecialchars($cat); ?>">
                    <div class="product-info">
                        <span class="product-label">Collection</span>
                        <h3><?php echo htmlspecialchars($titres_poetiques[$cat] ?? $cat); ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="forest-section">
            <div class="forest-content">
                <h2>LE SANCTUAIRE<br>DES RACINES</h2>
                <p class="forest-subtitle">Là où l'âme se fait sève et le souvenir devient forêt</p>
                
                <p class="forest-text">
                    À la lisière du monde tangible et de l'éternité sauvage, notre forêt cinéraire offre bien plus qu'un dernier repos : elle propose une véritable métamorphose. Ici, la fin n'est qu'un prélude, une promesse de renouveau où chaque vie honorée vient nourrir la majesté de la nature.
                </p>
                
                <div class="forest-text">
                    <h3>Notre Vision d'Exception</h3>
                    
                    <p><strong>L'Écrin de la Renaissance :</strong> Nous avons délaissé le marbre froid pour le souffle des bois séculaires. Le dépôt des urnes au pied de nos arbres gardiens permet une union éternelle avec la terre, transformant le deuil en une célébration organique de la vie.</p>
                    
                    <p><strong>Équilibre Sacré :</strong> Notre approche fusionne l'hommage solennel aux défunts avec une préservation écologique absolue. Dans ce sanctuaire, la mémoire ne s'efface pas, elle s'élève vers la canopée.</p>
                    
                    <p><strong>La Conciergerie du Passage :</strong> Au-delà du lieu, nous offrons un accompagnement humain d'une infinie délicatesse, veillant à ce que chaque dernier voyage soit aussi unique que l'âme qu'il transporte.</p>
                </div>
                
                <p class="forest-quote">"Dans le murmure des feuilles, la mémoire trouve enfin sa paix."</p>
                
                <a href="foret.php" class="forest-button">Explorer ce dernier voyage</a>
            </div>
            
            <div class="forest-image-container">
                <img src="images/foret.jpg" alt="Le Sanctuaire des Racines">
                <div class="fireflies">
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                    <div class="firefly"></div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-main">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>La Demeure</h4>
                <p>L'art funéraire pensé comme un hommage éternel. Excellence et discrétion depuis 1892.</p>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="catalogue.php">Le Catalogue</a></li>
                    <li><a href="foret.php">Le Sanctuaire</a></li>
                    <li><a href="ceremonies.php">L'Art de l'Adieu</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Informations</h4>
                <ul>
                    <li><a href="contact.php">Nous contacter</a></li>
                    <li><a href="mentions-legales.php">Mentions Légales</a></li>
                    <li><a href="cgv.php">Conditions de vente</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Conciergerie</h4>
                <p>12 rue de l'Éternité, Paris<br>01 40 20 30 40<br>contact@dernieredemeure.fr</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 LA DERNIÈRE DEMEURE. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>