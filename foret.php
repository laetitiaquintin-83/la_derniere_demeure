<?php 
session_start();
require_once '../config.php'; 

$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Le Sanctuaire des Racines | La Dernière Demeure</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        /* Styles d'immersion (similaires à cérémonies) */
        .foret-split {
            display: flex;
            flex-wrap: wrap;
            min-height: 600px;
        }
        .foret-text {
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
        .foret-image {
            flex: 1;
            min-width: 300px;
            background-size: cover;
            background-position: center;
        }
        .script-font {
            font-family: 'Great Vibes', cursive;
            color: #D4AF37;
            font-size: 4em;
            text-align: center;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.8);
            margin-bottom: 10px;
        }
        h2.cinzel-title {
            color: #D4AF37;
            font-family: 'Cinzel', serif;
            font-size: 2.2em;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        .poetry-box {
            padding: 80px 15%;
            text-align: center;
            background: #000;
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
        }
        .poetry-text {
            font-style: italic; 
            color: #ddd; 
            font-size: 1.4em;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        .poetry-text::before, .poetry-text::after {
            content: '"';
            font-family: 'Cinzel', serif;
            color: #D4AF37;
            font-size: 2em;
            opacity: 0.5;
        }
    </style>
</head>
<body style="background: #000; margin: 0; padding: 0;">
    
    <header style="background: rgba(0,0,0,0.9); padding: 15px 5%; border-bottom: 1px solid #333; position: sticky; top: 0; z-index: 20;">
        <nav>
            <a href="../index.php">Accueil</a>
            <a href="catalogue.php">Le Catalogue</a>
            <a href="foret.php" class="active" style="color: #D4AF37;">Le Sanctuaire</a>
            <a href="../ceremonies.php">L'Art de l'Adieu</a>
            <a href="../contact.php">Conciergerie</a>
            <a href="../panier.php" style="margin-left: auto;">L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
        </nav>
    </header>

    <section style="height: 80vh; background: url('foret-hero-canopee.jpg') center/cover fixed; display: flex; align-items: center; justify-content: center; position: relative;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.9));"></div>
        <div style="position: relative; text-align: center; z-index: 1; padding: 0 20px;">
            <span class="script-font">L'Étreinte de l'Éternité</span>
            <h1 style="font-family: 'Cinzel', serif; color: #fff; font-size: 3em; letter-spacing: 8px; margin-top: 0; text-shadow: 0 5px 15px rgba(0,0,0,1);">LE SANCTUAIRE DES RACINES</h1>
        </div>
    </section>

    <section style="padding: 60px 15%; text-align: center; background: #000;">
        <p style="color: #ccc; font-size: 1.2em; line-height: 2;">
            Dans le murmure des feuilles et l'éclat des lucioles, la <strong>forêt cinéraire</strong> offre un dernier voyage d'une pureté absolue. C'est une promesse de paix, une métamorphose où l'absence se fait sève, et le souvenir, forêt sacrée.
        </p>
    </section>

    <section class="foret-split">
        <div class="foret-text">
            <h2 class="cinzel-title">Le Rite de la Terre</h2>
            <p>Ici, point de futilité. L'urne, délicatement façonnée dans l'argile, le sel ou les fibres végétales, retourne doucement à la poussière. Elle est confiée au sol, tout contre les racines d'un arbre protecteur.</p>
            <p style="margin-top: 15px; color: #888;">Ce geste ancestral transforme le deuil en une croissance éternelle vers la lumière céleste. La vie ne s'arrête pas, elle se diffuse dans l'écosystème entier.</p>
        </div>
        <div class="foret-image" style="background-image: url('foret-rite-terre.jpg');"></div>
    </section>

    <section class="poetry-box">
        <div class="poetry-text">
            Ne me cherchez plus dans la solitude du marbre, mais dans la danse de la lune sur les chênes. Je suis devenu le souffle de la terre.
        </div>
    </section>

    <section class="foret-split" style="flex-direction: row-reverse;">
        <div class="foret-text">
            <h2 class="cinzel-title">Un Repos Souverain</h2>
            <p>Ce voyage, bien que mystique, s'inscrit dans un cadre solennel et pérenne. En France, ces bois du souvenir — à l'image des sanctuaires d'Arbas ou de Nancy — sont les gardiens de votre tranquillité.</p>
            <p style="margin-top: 15px; color: #888;">La loi et la nature veillent ensemble, assurant que votre repos sera aussi immuable et sacré que le cycle éternel des saisons.</p>
        </div>
        <div class="foret-image" style="background-image: url('foret-repos-souverain.jpg');"></div>
    </section>

    <section style="padding: 80px 5%; text-align: center; background: #000;">
        <div style="max-width: 800px; margin: 0 auto; border: 1px dashed #D4AF37; padding: 40px;">
            <h3 style="color: #D4AF37; font-family: 'Cinzel', serif; font-size: 1.8em; margin-bottom: 20px;">Organiser ce dernier voyage</h3>
            <p style="color: #ccc; margin-bottom: 30px;">Nos conseillers vous accompagnent dans le choix de votre arbre de mémoire et l'organisation de ce rituel sylvestre.</p>
            <a href="../contact.php" style="display: inline-block; padding: 12px 30px; background: transparent; border: 1px solid #D4AF37; color: #D4AF37; text-decoration: none; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; margin-right: 15px;">Écrire à la Conciergerie</a>
            <a href="../index.php" style="display: inline-block; padding: 12px 30px; background: #D4AF37; color: #000; text-decoration: none; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;">Regagner la Demeure</a>
        </div>
    </section>

    <div id="toast" class="toast"></div>

    <script src="../script.js"></script>
</body>
</html>