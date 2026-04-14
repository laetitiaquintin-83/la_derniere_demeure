<?php 
session_start();
require_once 'config.php'; 

// Calcul du nombre total d'articles pour le compteur du menu
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>L'Art de l'Adieu | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Styles spécifiques pour l'immersion de cette page */
        .ceremonie-split {
            display: flex;
            flex-wrap: wrap;
            min-height: 600px;
        }
        .ceremonie-text {
            flex: 1;
            min-width: 300px;
            padding: 8% 10%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #050505;
            color: #ccc;
            line-height: 1.8;
        }
        .ceremonie-image {
            flex: 1;
            min-width: 300px;
            background-size: cover;
            background-position: center;
        }
        .ceremonie-title {
            color: #D4AF37;
            font-family: 'Cinzel', serif;
            font-size: 2.2em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body style="background: #000; margin: 0; padding: 0;">
    
    <header style="background: rgba(0,0,0,0.9); padding: 15px 5%; border-bottom: 1px solid #333;">
        <nav>
            <a href="index.php">🏠 Accueil</a>
            <a href="images/catalogue.php">📜 Le Catalogue</a>
            <a href="images/foret.php">🌿 Le Sanctuaire</a>
            <a href="ceremonies.php" class="active" style="color: #D4AF37;">🕯️ L'Art de l'Adieu</a>
            <a href="contact.php">📞 Conciergerie</a>
            <a href="panier.php" style="margin-left: auto;">L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="lock-link">🔓 Quitter</a>
            <?php else: ?>
                <a href="login.php" class="lock-link">🔒 Entrer</a>
            <?php endif; ?>
        </nav>
    </header>

    <section style="height: 70vh; background: url('images/celebration-allee-lumineuse.jpg') center/cover; display: flex; align-items: center; justify-content: center; position: relative;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.9));"></div>
        <div style="position: relative; text-align: center; z-index: 1; padding: 0 20px;">
            <h1 style="font-family: 'Cinzel', serif; color: #D4AF37; font-size: 3.5em; text-shadow: 2px 2px 10px rgba(0,0,0,0.8);">L'Art du Baptême de Mémoire</h1>
            <p style="color: #fff; font-style: italic; font-size: 1.3em; letter-spacing: 1px;">"Parce que chaque vie est un chef-d'œuvre qui mérite son vernissage."</p>
        </div>
    </section>

    <section style="padding: 80px 15%; text-align: center; background: #000;">
        <h2 style="color: #D4AF37; font-family: 'Cinzel', serif; margin-bottom: 30px; font-size: 2em;">Une Renaissance dans les Cœurs</h2>
        <p style="color: #ccc; font-size: 1.1em; line-height: 1.8;">
            Le dernier chapitre d'une vie ne devrait pas s'écrire dans la pénombre et le silence. Inspirés par la puissance des nouveaux départs, nos rituels transforment l'adieu en une véritable célébration du vivant. Ici, la douleur s'apaise dans la lumière, et le souvenir s'enracine pour devenir un héritage éternel.
        </p>
    </section>

    <section class="ceremonie-split">
        <div class="ceremonie-text">
            <h3 class="ceremonie-title">Le Chemin des Lucioles</h3>
            <p>Une déambulation nocturne au cœur de notre forêt cinéraire. Guidés par la seule lueur de centaines de bougies et le murmure apaisant de la nature, les proches accompagnent l'être cher dans une transition d'une douceur absolue.</p>
            <p style="margin-top: 15px; font-style: italic; color: #888;">Ce rituel permet un temps de réflexion suspendu, où chaque pas est une pensée lumineuse dédiée à la mémoire.</p>
        </div>
        <div class="ceremonie-image" style="background-image: url('images/celebration-allee-lumineuse.jpg');"></div>
    </section>

    <section class="ceremonie-split" style="flex-direction: row-reverse;">
        <div class="ceremonie-text">
            <h3 class="ceremonie-title">Le Baptême de Sève</h3>
            <p>Le moment sacré du retour à la terre. Au centre d'un cercle de lumière, nous célébrons la transmission. Au lieu de déposer des fleurs éphémères, chaque invité plante une graine ou confie l'urne biodégradable aux racines d'un arbre majestueux.</p>
            <p style="margin-top: 15px; font-style: italic; color: #888;">L'absence physique devient alors une présence vibrante et vivante à travers le frissonnement des feuilles et l'envol des papillons.</p>
        </div>
        <div class="ceremonie-image" style="background-image: url('images/rituel-cercle-papillons.jpg');"></div>
    </section>

    <section class="ceremonie-split">
        <div class="ceremonie-text">
            <h3 class="ceremonie-title">L'Ondine de Paix</h3>
            <p>Un rituel de lâcher-prise au bord de notre cours d'eau sacré. Les invités sont conviés à déposer des bougies flottantes sur le ruisseau, emportant avec elles des messages d'amour et des vœux de paix.</p>
            <p style="margin-top: 15px; font-style: italic; color: #888;">Voir la lumière s'éloigner doucement au fil de l'eau symbolise l'acceptation sereine et le flux continu de l'existence.</p>
        </div>
        <div class="ceremonie-image" style="background-image: url('images/rituel-fil-eau.jpg');"></div>
    </section>

    <section class="ceremonie-split" style="flex-direction: row-reverse;">
        <div class="ceremonie-text">
            <h3 class="ceremonie-title">Le Vernissage d'une Vie</h3>
            <p>Transformez la douleur en célébration autour d'une réception d'exception. À la lueur de l'aube ou du crépuscule, parmi les lys et la verdure, nous dressons un espace où résonnent les rires et les anecdotes.</p>
            <p style="margin-top: 15px; font-style: italic; color: #888;">On y porte un toast aux succès, aux passions et aux moments partagés, honorant la personne telle qu'elle était vraiment.</p>
        </div>
        <div class="ceremonie-image" style="background-image: url('images/celebration-banquet-lys.jpg');"></div>
    </section>

    <section style="padding: 100px 5%; text-align: center; background: #000; border-top: 1px solid #222;">
        <div style="max-width: 800px; margin: 0 auto; border: 1px dashed #D4AF37; padding: 50px 30px;">
            <h3 style="color: #D4AF37; font-family: 'Cinzel', serif; font-size: 2em; margin-bottom: 20px;">Orchestrer l'Inoubliable</h3>
            <p style="color: #ccc; margin-bottom: 30px;">Notre service de conciergerie et nos scénographes sont à votre écoute pour concevoir un hommage sur-mesure, respectant l'essence même de la vie que vous souhaitez célébrer.</p>
            <a href="contact.php" style="display: inline-block; padding: 15px 40px; background: #D4AF37; color: #000; text-decoration: none; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;">Contacter notre Scénographe</a>
        </div>
    </section>

    <script src="script.js"></script>

    <?php include 'footer.php'; ?>
</body>
</html>