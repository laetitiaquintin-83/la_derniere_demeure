<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mentions Légales | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .mentions-container .quick-nav {
            margin: 30px auto 0;
            max-width: 900px;
            padding: 0 20px;
            background: transparent;
            border-bottom: 0;
        }
        .mentions-container {
            max-width: 900px;
            margin: 100px auto;
            padding: 40px;
            background: #0a0a0a;
            border: 1px solid #b59410;
            color: #e0e0e0;
            line-height: 1.8;
        }
        h1, h2 { color: #b59410; font-family: 'Cinzel', serif; }
        .section-mentions { margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
    </style>
</head>
<body>

    <div class="mentions-container">
        <div class="quick-nav">
            <a href="index.php">Accueil</a>
            <a href="catalogue.php">Catalogue</a>
            <a href="contact.php">Conciergerie</a>
        </div>

        <h1>Mentions Légales</h1>
        
        <div class="section-mentions">
            <h2>Éditeur du site</h2>
            <p>
                Le site <strong>La Dernière Demeure</strong> est édité par la société <strong>Incinéris</strong>, 
                SASU au capital de 5 500 000 €.<br>
                <strong>Siège social :</strong> Immeuble le Wellice – 50 rue de la vague, 59650 Villeneuve d’Ascq.<br>
                <strong>Immatriculation :</strong> RCS de Lille Métropole 805 018 959.<br>
                <strong>TVA Intracommunautaire :</strong> FR68805018959.<br>
                <strong>Téléphone :</strong> 03 20 61 71 51.
            </p>
        </div>

        <div class="section-mentions">
            <h2>Hébergement</h2>
            <p>
                Le site est hébergé localement via l'environnement <strong>Laragon</strong> à des fins de développement. 
                (À remplacer par les coordonnées de ton hébergeur final comme OVH ou Hostinger lors de la mise en ligne).
            </p>
        </div>

        <div class="section-mentions">
            <h2>Propriété intellectuelle</h2>
            <p>
                L'univers visuel, les textes et le concept de "La Dernière Demeure" sont protégés. 
                Toute reproduction, même partielle, sans autorisation préalable est interdite.
            </p>
        </div>

        <div class="section-mentions">
            <h2>Protection des données (RGPD)</h2>
            <p>
                Conformément à la loi, vous disposez d'un droit d'accès et de rectification de vos données. 
                Les informations collectées via notre formulaire de contact ne sont utilisées que pour le traitement de vos demandes de rituels ou services funéraires.
            </p>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php" class="btn-gold" style="text-decoration: none; padding: 10px 20px;">✦ Retour au Sanctuaire</a>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

