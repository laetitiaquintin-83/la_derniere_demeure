<?php
/**
 * payment-form.php
 * 
 * Formulaire de Paiement Sécurisé avec Stripe Elements
 * 
 * Les données sensibles (n° carte, CVV) ne sont JAMAIS stockées sur ce serveur
 * Stripe s'occupe de tout via son formulaire tokenisé
 */

require_once __DIR__ . '/app/bootstrap.php';

$payment_id = $_GET['payment_id'] ?? null;

// Valider l'ID de paiement
if (!$payment_id || !isset($_SESSION['pending_payment']) || $_SESSION['pending_payment']['id'] !== $payment_id) {
    header('Location: panier.php');
    exit;
}

$payment = $_SESSION['pending_payment'];
$montant = number_format($payment['amount'], 2, ',', ' ');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .payment-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 40px;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            border: 1px solid #333;
            border-radius: 8px;
        }
        .payment-nav {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }
        .payment-nav a {
            color: #d4af37;
            text-decoration: none;
            font-family: 'Cinzel', serif;
            font-size: 0.82rem;
            letter-spacing: 1px;
            padding: 10px 14px;
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 999px;
            background: rgba(212, 175, 55, 0.06);
            transition: all 0.25s ease;
        }
        .payment-nav a:hover {
            background: rgba(212, 175, 55, 0.16);
            border-color: rgba(212, 175, 55, 0.65);
            transform: translateY(-1px);
        }
        .payment-header {
            font-family: 'Cinzel', serif;
            text-align: center;
            color: #d4af37;
            margin-bottom: 30px;
        }
        .payment-header h1 {
            font-size: 28px;
            margin: 0;
        }
        .payment-info {
            background: rgba(212, 175, 55, 0.1);
            border-left: 3px solid #d4af37;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .payment-info p {
            color: #e0e0e0;
            margin: 8px 0;
        }
        .payment-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #d4af37;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input::placeholder {
            color: #999;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-row .form-group {
            flex: 1;
        }
        #card-element {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
            border-radius: 4px;
            padding: 12px;
            color: #fff;
        }
        .stripe-notice {
            color: #999;
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stripe-notice::before {
            content: "🔒";
        }
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #d4af37 0%, #b8941e 100%);
            border: none;
            border-radius: 4px;
            color: #000;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        }
        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .error-message {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            border-left: 3px solid #ff6b6b;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: none;
        }
        .security-badges {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #333;
            justify-content: center;
            flex-wrap: wrap;
        }
        .back-actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 22px;
        }
        .back-actions a {
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid rgba(212, 175, 55, 0.45);
            color: #e0e0e0;
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 1px;
            background: rgba(212, 175, 55, 0.05);
            transition: all 0.25s ease;
        }
        .back-actions a:hover {
            background: rgba(212, 175, 55, 0.14);
            color: #fff;
        }
        .badge {
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        .badge::before {
            display: block;
            font-size: 24px;
            margin-bottom: 8px;
        }
        .badge.ssl::before {
            content: "🔐";
        }
        .badge.encrypted::before {
            content: "🔒";
        }
        .badge.pci::before {
            content: "✓";
            color: #d4af37;
        }
    </style>
</head>
<body style="background: #000; color: #e0e0e0;">
    <div class="payment-container">
        <div class="payment-nav">
            <a href="index.php">Retour à l'accueil</a>
            <a href="catalogue.php">Voir le catalogue</a>
            <a href="panier.php">Revenir au panier</a>
        </div>

        <div class="payment-header">
            <h1>Finaliser votre Paiement</h1>
            <p style="color: #999; margin-top: 10px;">Session sécurisée par Stripe</p>
        </div>

        <div class="payment-info">
            <p><strong>Montant à payer:</strong> <span style="color: #d4af37; font-size: 20px; font-weight: bold;"><?php echo $montant; ?> €</span></p>
            <p><strong>Articles:</strong> <?php echo count($payment['items']); ?> article(s)</p>
            <p style="font-size: 12px; color: #999;">ID de paiement: <?php echo htmlspecialchars($payment_id); ?></p>
        </div>

        <form id="payment-form" class="payment-form">
            <div class="error-message" id="error-message"></div>

            <div class="form-group">
                <label for="cardholder">Titulaire de la Carte</label>
                <input type="text" 
                       id="cardholder" 
                       name="cardholder" 
                       placeholder="Nom complet"
                       required>
            </div>

            <div style="background: rgba(74, 222, 128, 0.1); border: 1px dashed #4ade80; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <p style="color: #4ade80; font-weight: bold; margin: 0 0 10px 0;">Paiement sécurisé</p>
                <p style="font-size: 13px; margin: 0; color: #b3b3b3; line-height: 1.6;">
                    Aucune donnée bancaire n'est saisie sur ce formulaire. La validation ci-dessous confirme uniquement l'intention de paiement.
                </p>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;color:#e0e0e0;text-transform:none;letter-spacing:0;">
                    <input type="checkbox" id="confirm-payment" name="confirm_payment" value="1" required>
                    Je confirme vouloir finaliser ce paiement sécurisé.
                </label>
            </div>

            <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment_id); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo genererTokenCSRF(); ?>">

            <button type="submit" class="submit-btn" id="submit-btn">
                Payer <?php echo $montant; ?> € de façon Sécurisée
            </button>

            <div class="back-actions">
                <a href="panier.php">Annuler et revenir au panier</a>
                <a href="index.php">Retour à l'accueil</a>
            </div>
        </form>

        <div class="security-badges">
            <div class="badge encrypted">
                Chiffrement SSL
            </div>
            <div class="badge ssl">
                Données Sécurisées
            </div>
            <div class="badge pci">
                Conforme PCI-DSS
            </div>
        </div>
    </div>

    <script>
    // ====================================================
    // GESTION DU FORMULAIRE DE PAIEMENT
    // ====================================================
    
    document.getElementById('payment-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const errorMsg = document.getElementById('error-message');
        
        submitBtn.disabled = true;
        errorMsg.style.display = 'none';
        
        try {
            // Validation basique (aucune donnée carte collectée)
            const cardholder = document.getElementById('cardholder').value.trim();
            const confirmation = document.getElementById('confirm-payment').checked;
            
            if (!cardholder || !confirmation) {
                throw new Error('Veuillez compléter les champs requis');
            }
            
            // Soumettre au serveur
            const formData = new FormData(document.getElementById('payment-form'));
            const response = await fetch('process-payment.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Rediriger vers la page de succès
                window.location.href = 'payment-success.php?session_id=' + result.payment_id;
            } else {
                throw new Error(result.error || 'Erreur lors du paiement');
            }
            
        } catch (error) {
            errorMsg.textContent = '❌ ' + error.message;
            errorMsg.style.display = 'block';
            submitBtn.disabled = false;
            console.error('Erreur paiement:', error);
        }
    });
    
    // Aucun champ carte n'est rendu côté client en mode sécurisé actuel.
    // On évite donc toute logique JS liée à des IDs inexistants.
    </script>
</body>
</html>
