<?php
require_once __DIR__ . '/app/bootstrap.php';

$cartController = new CartController(new CartModel($pdo));
$cartData = $cartController->index();
extract($cartData, EXTR_SKIP);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>L'Offrande | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        .checkout-section { max-width: 1100px; margin: 50px auto; padding: 0 20px; }
        .checkout-grid { display: flex; gap: 40px; flex-wrap: wrap; align-items: flex-start; }
        .cart-summary, .payment-form-container { flex: 1; min-width: 320px; background: rgba(10, 10, 10, 0.85); border: 1px solid rgba(181, 148, 16, 0.3); padding: 30px; border-radius: 3px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8); }
        .checkout-title { color: var(--gold); font-family: 'Cinzel', serif; font-size: 1.5em; border-bottom: 1px solid rgba(181, 148, 16, 0.3); padding-bottom: 15px; margin-bottom: 25px; letter-spacing: 2px; text-transform: uppercase; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed rgba(255, 255, 255, 0.1); padding: 15px 0; }
        .cart-item-name { color: #fff; font-family: 'Cinzel', serif; font-size: 1.1em; }
        .cart-item-desc { color: #888; font-size: 0.85em; margin-top: 5px; }
        .cart-item-price { color: var(--gold-bright); font-weight: bold; text-align: right; }
        
        .btn-remove { color: #800000; text-decoration: none; font-size: 1.2em; margin-left: 15px; transition: 0.3s; }
        .btn-remove:hover { color: #ff4d4d; transform: scale(1.2); }

        .cart-total { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--gold); font-family: 'Cinzel', serif; font-size: 1.3em; color: #fff; }
        .form-group label { display: block; color: #b3b3b3; font-size: 0.85em; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .checkout-input { width: 100%; background: rgba(0, 0, 0, 0.5); border: 1px solid rgba(181, 148, 16, 0.4); color: #fff; padding: 12px 15px; margin-bottom: 20px; font-family: 'Arial', sans-serif; transition: all 0.3s; }
        .checkout-input:focus { border-color: var(--gold-bright); outline: none; box-shadow: 0 0 10px rgba(181, 148, 16, 0.2); }
        .card-row { display: flex; gap: 15px; }
        .btn-pay { width: 100%; background: linear-gradient(45deg, #8a7312, #b59410); color: #0a0a0a; border: none; padding: 15px; font-family: 'Cinzel', serif; font-size: 1.1em; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; margin-top: 10px; transition: all 0.3s ease; }
        .btn-pay:hover { box-shadow: 0 0 20px rgba(181, 148, 16, 0.5); transform: translateY(-2px); }
        .empty-cart { text-align: center; color: #b3b3b3; font-style: italic; padding: 20px; }
    </style>
</head>
<body class="admin-body">

    <header class="admin-nav">
        <nav>
            <a href="index.php">✦ Accueil</a>
            <a href="catalogue.php">✿ Catalogue</a>
            <a href="foret.php">✾ Le Sanctuaire</a>
            <a href="ceremonies.php">❦ L'Art de l'Adieu</a>
            <a href="contact.php">❋ Conciergerie</a>
            <a href="panier.php" style="margin-left: auto;" class="active">✵ L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
        </nav>
    </header>

    <section class="checkout-section">
        <h1 class="section-title">L'Offrande Finale</h1>
        <p style="text-align: center; color: #b3b3b3; font-style: italic; margin-bottom: 50px;">Scellez vos choix pour l'éternité.</p>

        <div class="checkout-grid">
            
            <div class="cart-summary">
                <h2 class="checkout-title">Vos Articles</h2>
                
                <?php if (empty($panier_details)): ?>
                    <p class="empty-cart">Votre offrande est vide. Aucun article n'a encore été sélectionné.</p>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="catalogue.php" class="btn-gold" style="font-size: 0.8em; padding: 10px 20px;">Retourner au Catalogue</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($panier_details as $article): ?>
                        <div class="cart-item">
                            <div style="flex: 1;">
                                <div class="cart-item-name"><?php echo htmlspecialchars($article['nom']); ?></div>
                                <div class="cart-item-desc">Quantité : <?php echo $article['quantite']; ?></div>
                            </div>
                            <div class="cart-item-price">
                                <?php echo number_format($article['sous_total'], 2, ',', ' '); ?> €
                                <a href="?remove=<?php echo $article['id']; ?>&token=<?php echo genererTokenCSRF(); ?>" class="btn-remove" title="Retirer cet article">×</a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="cart-total">
                        <span>Offrande Totale</span>
                        <span style="color: var(--gold-bright);"><?php echo number_format($total, 2, ',', ' '); ?> €</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($panier_details)): ?>
            <div class="payment-form-container">
                <h2 class="checkout-title">Sceau de l'Engagement</h2>

                <form action="create-checkout-session.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(genererTokenCSRF()); ?>">

                    <p style="color: #b3b3b3; line-height: 1.7; margin-bottom: 20px;">
                        Le paiement est traité via une session sécurisée. Aucune donnée bancaire n'est saisie ni stockée sur ce site.
                    </p>

                    <button type="submit" class="btn-pay">Continuer vers le Paiement Sécurisé (<?php echo number_format($total, 2, ',', ' '); ?> €)</button>
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <span style="color: #666; font-size: 0.8em; display: flex; align-items: center; justify-content: center; gap: 5px;">
                            🔒 Session externe sécurisée
                        </span>
                    </div>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>