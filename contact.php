<?php 
require_once 'config.php'; 

// Calcul du nombre total d'articles pour le compteur du menu
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

$message_succes = '';

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 🔐 Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        $message_succes = "⚠️ Erreur de sécurité. Veuillez réessayer.";
    } else {
        // Pour l'instant, on simule l'envoi. Plus tard, on pourra ajouter la fonction mail() ou l'enregistrement en BDD.
        $message_succes = "Votre message a été confié à notre scénographe. Nous vous répondrons avec la plus grande discrétion.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Conciergerie | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Styles spécifiques pour le formulaire de luxe */
        .contact-section {
            padding: 80px 5%;
            background: #000;
            color: #fff;
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 50px;
            max-width: 700px;
        }
        .contact-header h1 {
            color: #D4AF37;
            font-family: 'Cinzel', serif;
            font-size: 2.8em;
            margin-bottom: 15px;
        }
        .contact-header p {
            color: #aaa;
            font-style: italic;
            line-height: 1.6;
        }
        .premium-form {
            width: 100%;
            max-width: 600px;
            background: #050505;
            padding: 40px;
            border: 1px solid #222;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
        }
        .form-group {
            margin-bottom: 25px;
        }
        .premium-form label {
            display: block;
            margin-bottom: 10px;
            color: #D4AF37;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .premium-form input, 
        .premium-form select, 
        .premium-form textarea {
            width: 100%;
            padding: 15px;
            background: #000;
            border: 1px solid #444;
            color: #fff;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .premium-form input:focus, 
        .premium-form select:focus, 
        .premium-form textarea:focus {
            border-color: #D4AF37;
            outline: none;
        }
        .premium-form textarea {
            resize: vertical;
            min-height: 120px;
        }
        .btn-submit {
            width: 100%;
            padding: 18px;
            background: #D4AF37;
            color: #000;
            border: none;
            font-family: 'Cinzel', serif;
            font-weight: bold;
            font-size: 1.1em;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-submit:hover {
            background: #fff;
        }
        .success-msg {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid #D4AF37;
            color: #D4AF37;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
            font-style: italic;
        }
    </style>
</head>
<body style="background: #000; margin: 0; padding: 0;">
    
    <header style="background: rgba(0,0,0,0.9); padding: 15px 5%; border-bottom: 1px solid #333;">
        <nav>
            <a href="index.php">✦ Accueil</a>
            <a href="catalogue.php">✿ Le Catalogue</a>
            <a href="foret.php">✾ Le Sanctuaire</a>
            <a href="ceremonies.php">❦ L'Art de l'Adieu</a>
            <a href="contact.php" class="active" style="color: #D4AF37;">❋ Conciergerie</a>
            <a href="panier.php" style="margin-left: auto;">✵ L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="lock-link">◇ Quitter</a>
            <?php else: ?>
                <a href="login.php" class="lock-link">◆ Entrer</a>
            <?php endif; ?>
        </nav>
    </header>

    <section class="contact-section">
        <div class="contact-header">
            <h1>Orchestrer l'Inoubliable</h1>
            <p>Notre service de conciergerie et nos scénographes sont à votre écoute pour concevoir un hommage sur-mesure ou vous guider dans le choix de la dernière demeure.</p>
        </div>

        <?php if (!empty($message_succes)): ?>
            <div class="success-msg">
                <?php echo $message_succes; ?>
            </div>
        <?php endif; ?>

        <form class="premium-form" method="POST" action="contact.php">
            <!-- 🔐 TOKEN CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(genererTokenCSRF()); ?>">
            
            <div class="form-group">
                <label for="nom">Nom & Prénom</label>
                <input type="text" id="nom" name="nom" required placeholder="Votre nom complet">
            </div>

            <div class="form-group">
                <label for="email">Courriel Confidentiel</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com">
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone (Optionnel)</label>
                <input type="tel" id="telephone" name="telephone" placeholder="Pour un échange de vive voix">
            </div>

            <div class="form-group">
                <label for="sujet">Objet de votre demande</label>
                <select id="sujet" name="sujet" required>
                    <option value="" disabled selected>Sélectionnez un sujet...</option>
                    <option value="ceremonie">Création d'une cérémonie sur-mesure</option>
                    <option value="catalogue">Renseignement sur un écrin (Catalogue)</option>
                    <option value="foret">Information sur la Forêt Cinéraire</option>
                    <option value="autre">Autre demande discrète</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Votre Message</label>
                <textarea id="message" name="message" required placeholder="Partagez-nous vos souhaits ou ceux de l'être cher..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Confier mon message</button>
        </form>
    </section>

    <script src="script.js"></script>

    <?php include 'footer.php'; ?>
</body>
</html>