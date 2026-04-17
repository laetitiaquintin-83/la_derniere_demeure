<?php
/**
 * process-payment.php
 * 
 * Traitement du paiement sÃ©curisÃ©
 * 
 * Important: En production, cette page reÃ§oit un TOKEN de Stripe,
 * JAMAIS les donnÃ©es rÃ©elles de la carte
 */

require_once __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json');

// Valider la requÃªte
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'MÃ©thode non autorisÃ©e']));
}

// Valider le token CSRF
if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
    http_response_code(403);
    die(json_encode(['success' => false, 'error' => 'Erreur de sÃ©curitÃ© CSRF']));
}

$payment_id = $_POST['payment_id'] ?? null;

// Valider l'ID de paiement
if (!$payment_id || !isset($_SESSION['pending_payment']) || $_SESSION['pending_payment']['id'] !== $payment_id) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Paiement invalide']));
}

try {
    $payment = $_SESSION['pending_payment'];

    // Initialisation dÃ©fensive des tables de commande.
    $pdo->exec("CREATE TABLE IF NOT EXISTS commandes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        montant_total DECIMAL(10,2) NOT NULL,
        statut VARCHAR(32) NOT NULL DEFAULT 'payee',
        email VARCHAR(255) NOT NULL,
        cree_a TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS commande_articles (
        id INT PRIMARY KEY AUTO_INCREMENT,
        commande_id INT NOT NULL,
        produit_id INT NOT NULL,
        quantite INT NOT NULL,
        prix_unitaire DECIMAL(10,2) NOT NULL,
        cree_a TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_commande_id (commande_id)
    )");

    // Validation non sensible (aucune donnÃ©e carte n'est traitÃ©e ici)
    $cardholder = preg_replace('/[^a-zA-ZÃ©Ã¨Ãª\s\-\']/', '', $_POST['cardholder'] ?? '');
    if (strlen($cardholder) < 2 || strlen($cardholder) > 100) {
        throw new Exception('Nom du titulaire invalide');
    }

    if (!isset($_POST['confirm_payment']) || $_POST['confirm_payment'] !== '1') {
        throw new Exception('Confirmation de paiement manquante');
    }
    
    // ====================================================
    // MODE DÃ‰MO: Simuler la rÃ©ponse Stripe
    // ====================================================
    // En production: appeler l'API Stripe avec le token
    /*
    $stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
    $paymentIntent = $stripe->paymentIntents->retrieve($payment_id);
    
    if ($paymentIntent->status !== 'succeeded') {
        throw new Exception('Paiement non autorisÃ© par la banque');
    }
    */
    
    // Simuler le succÃ¨s
    $payment_status = 'succeeded'; // En dÃ©mo, paiement rÃ©ussi
    
    if ($payment_status !== 'succeeded') {
        throw new Exception('Paiement refusÃ© par la banque');
    }
    
    // ====================================================
    // CRÃ‰ER LA COMMANDE EN BASE DE DONNÃ‰ES
    // ====================================================
    
    $pdo->beginTransaction();
    
    try {
        // CrÃ©er la commande
        $stmt = $pdo->prepare("INSERT INTO commandes (montant_total, statut, email, cree_a) 
                                VALUES (?, 'payee', ?, NOW())");
        $stmt->execute([
            $payment['amount'],
            $_SESSION['email'] ?? 'inconnu@example.com'
        ]);
        $commande_id = $pdo->lastInsertId();
        
        // Ajouter les articles
        foreach ($payment['items'] as $id => $quantite) {
            if (!ctype_digit(strval($id)) || $quantite <= 0) {
                continue;
            }
            
            // RÃ©cupÃ©rer le prix
            $stmt_prix = $pdo->prepare("SELECT prix FROM catalogue_funeraire WHERE id = ? LIMIT 1");
            $stmt_prix->execute([(int)$id]);
            $produit = $stmt_prix->fetch(PDO::FETCH_ASSOC);
            
            if ($produit) {
                $stmt_insert = $pdo->prepare("INSERT INTO commande_articles (commande_id, produit_id, quantite, prix_unitaire) 
                                             VALUES (?, ?, ?, ?)");
                $stmt_insert->execute([
                    $commande_id,
                    (int)$id,
                    (int)$quantite,
                    $produit['prix']
                ]);
            }
        }
        
        // DÃ©crÃ©menter les stocks
        $stmt_stock = $pdo->prepare("UPDATE catalogue_funeraire SET stock = stock - ? WHERE id = ? AND stock >= ?");
        
        foreach ($payment['items'] as $id => $quantite) {
            if (!ctype_digit(strval($id)) || $quantite <= 0) {
                continue;
            }
            
            $result = $stmt_stock->execute([(int)$quantite, (int)$id, (int)$quantite]);
            
            if ($stmt_stock->rowCount() === 0) {
                throw new Exception('Stock insuffisant pour produit ID: ' . $id);
            }
        }
        
        $pdo->commit();

        log_audit_event('PAYMENT_SUCCESS', 'commande', $commande_id, null, [
            'payment_id' => $payment_id,
            'montant' => $payment['amount'],
            'email' => $_SESSION['email'] ?? 'inconnu@example.com',
            'items_count' => count($payment['items'])
        ]);
        
        // ====================================================
        // NETTOYER LA SESSION ET RETOURNER LE SUCCÃˆS
        // ====================================================
        
        unset($_SESSION['panier']);
        unset($_SESSION['pending_payment']);
        
        // Enregistrer le paiement en logs
        error_log("âœ“ Paiement rÃ©ussi - Commande #$commande_id - Montant: " . $payment['amount'] . "â‚¬ - Timestamp: " . date('Y-m-d H:i:s'));
        
        echo json_encode([
            'success' => true,
            'payment_id' => $payment_id,
            'commande_id' => $commande_id,
            'montant' => $payment['amount'],
            'statut' => 'paid'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("âœ— Erreur paiement: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>


