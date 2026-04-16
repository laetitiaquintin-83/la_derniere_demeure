<?php
/**
 * payment-success.php
 * 
 * Page de confirmation après paiement réussi
 * Affiche les détails de la commande
 */

require_once 'config.php';

$payment_id = $_GET['session_id'] ?? $_GET['payment_id'] ?? null;

if (!$payment_id) {
    header('Location: index.php');
    exit;
}

// En production, valider avec Stripe que le paiement est réussi
// Pour cette démo, on considère que si on est ici, c'est bon
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Confirmé | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .success-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            border: 1px solid #333;
            border-radius: 8px;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 0.6s ease-in-out;
        }
        @keyframes bounce {
            0%, 100% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-title {
            font-family: 'Cinzel', serif;
            font-size: 32px;
            color: #d4af37;
            margin: 20px 0;
        }
        .success-message {
            color: #e0e0e0;
            font-size: 16px;
            line-height: 1.6;
            margin: 20px 0;
        }
        .order-details {
            background: rgba(212, 175, 55, 0.1);
            border-left: 3px solid #d4af37;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
            text-align: left;
        }
        .order-details p {
            color: #e0e0e0;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .order-details strong {
            color: #d4af37;
        }
        .security-note {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid #00c864;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            color: #00c864;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .security-note::before {
            content: "✓";
            font-size: 20px;
            font-weight: bold;
        }
        .action-buttons {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            justify-content: center;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #d4af37 0%, #b8941e 100%);
            color: #000;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        }
        .btn-secondary {
            background: rgba(212, 175, 55, 0.2);
            color: #d4af37;
            border: 1px solid #d4af37;
        }
        .btn-secondary:hover {
            background: rgba(212, 175, 55, 0.3);
        }
    </style>
</head>
<body style="background: #000; color: #e0e0e0;">
    <div class="success-container">
        <div class="success-icon">✓</div>
        
        <h1 class="success-title">Paiement Confirmé</h1>
        
        <div class="success-message">
            <p>Merci pour votre fidélité.</p>
            <p>Votre offrande aux dépouilles éternelles a été reçue.</p>
        </div>

        <div class="order-details">
            <p>
                <strong>ID de Paiement:</strong>
                <span><?php echo htmlspecialchars(substr($payment_id, 0, 16)) . '...'; ?></span>
            </p>
            <p>
                <strong>Date:</strong>
                <span><?php echo strftime('%d %B %Y à %H:%M', time()); ?></span>
            </p>
            <p>
                <strong>Statut:</strong>
                <span style="color: #00c864;">✓ Payé</span>
            </p>
        </div>

        <div class="security-note">
            Votre paiement a été sécurisé par Stripe et conforme à la norme PCI-DSS. 
            Vos données bancaires n'ont jamais transité par nos serveurs.
        </div>

        <div class="success-message" style="margin-top: 20px;">
            <p><strong>Prochaines étapes:</strong></p>
            <p>Un email de confirmation sera envoyé à votre adresse avec les détails de votre commande et les instructions de livraison.</p>
        </div>

        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">✦ Retour à l'Accueil</a>
            <a href="catalogue.php" class="btn btn-secondary">✿ Continuer les Courses</a>
        </div>
    </div>
</body>
</html>
