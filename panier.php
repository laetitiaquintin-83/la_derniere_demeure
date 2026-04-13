<?php
require_once 'config.php';
// On retire session_start() car config.php s'en charge déjà.

// --- LOGIQUE DE SUPPRESSION (Sécurisée) ---
if (isset($_GET['remove'])) {
    // On vérifie que le jeton CSRF est présent dans l'URL et valide
    if (!isset($_GET['token']) || !validerTokenCSRF($_GET['token'])) {
        die("Erreur de sécurité : Sceau de suppression invalide.");
    }

    // On force le type en entier pour plus de sécurité
    $id_a_supprimer = (int)$_GET['remove'];
    
    if (isset($_SESSION['panier'][$id_a_supprimer])) {
        unset($_SESSION['panier'][$id_a_supprimer]);
    }
    // On recharge la page pour actualiser les calculs (le motif PRG : Post/Redirect/Get)
    header("Location: panier.php");
    exit();
}

// 1. On récupère les ID et quantités en session
$panier_session = $_SESSION['panier'] ?? [];
$panier_details = [];
$total = 0;
$nombre_articles = array_sum($panier_session);

// 2. Si le panier n'est pas vide, on va chercher les vraies infos en base de données
if (!empty($panier_session)) {
    // Excellente pratique : préparation hors de la boucle !
    $stmt = $pdo->prepare("SELECT id, nom, prix FROM catalogue_funeraire WHERE id = ?");
    
    foreach ($panier_session as $id => $quantite) {
        $stmt->execute([$id]);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produit) {
            $produit['quantite'] = $quantite;
            $produit['sous_total'] = $produit['prix'] * $quantite;
            $panier_details[] = $produit; 
            $total += $produit['sous_total']; 
        }
    }
}
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
            <a href="index.php">🏠 Accueil</a>
            <a href="images/catalogue.php">📜 Le Catalogue</a>
            <a href="images/foret.php">🌿 Le Sanctuaire</a>
            <a href="ceremonies.php">🕯️ L'Art de l'Adieu</a>
            <a href="contact.php">📞 Conciergerie</a>
            <a href="panier.php" style="margin-left: auto;" class="active">♧️ L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
        </nav>
    </header>

    <section class="checkout-section">
        <h1 class="section-title">L'Offrande Finale</h1>
        <p style="text-align: center; color: #b3b3b3; font-style: italic; margin-bottom: 50px;">Scellez vos choix pour l'éternité.</p>

        <div class="checkout-grid">
            
            <div class="cart-summary">
                <h2 class="checkout-title">Vos Reliques</h2>
                
                <?php if (empty($panier_details)): ?>
                    <p class="empty-cart">Votre offrande est vide. Aucune relique n'a encore été sélectionnée.</p>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="images/catalogue.php" class="btn-gold" style="font-size: 0.8em; padding: 10px 20px;">Retourner au Catalogue</a>
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
                                <a href="?remove=<?php echo $article['id']; ?>&token=<?php echo genererTokenCSRF(); ?>" class="btn-remove" title="Retirer cette relique">×</a>
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
                
                <form action="traitement_paiement.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(genererTokenCSRF()); ?>">
                    
                    <div class="form-group">
                        <label>Nom gravé sur le sceau (Titulaire)</label>
                        <input type="text" name="nom_titulaire" class="checkout-input" placeholder="Ex: Jean Dupont" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Numéro d'engagement (Carte)</label>
                        <input type="text" name="numero_carte" class="checkout-input" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>
                    
                    <div class="card-row">
                        <div class="form-group" style="flex: 1;">
                            <label>Expiration</label>
                            <input type="text" name="date_expiration" class="checkout-input" placeholder="MM/AA" maxlength="5" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Cryptogramme</label>
                            <input type="text" name="cryptogramme" class="checkout-input" placeholder="123" maxlength="3" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-pay">Honorer le Passage (<?php echo number_format($total, 2, ',', ' '); ?> €)</button>
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <span style="color: #666; font-size: 0.8em; display: flex; align-items: center; justify-content: center; gap: 5px;">
                            🔒 Cérémonie scellée et chiffrée
                        </span>
                    </div>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </section>

</body>
</html>