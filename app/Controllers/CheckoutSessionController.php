<?php

class CheckoutSessionController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createSession(): void
    {
        // Valider la requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            die(json_encode(['error' => 'Méthode non autorisée']));
        }

        // Valider le token CSRF
        if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            die(json_encode(['error' => 'Erreur de sécurité CSRF']));
        }

        // Vérifier qu'il y a un panier
        if (empty($_SESSION['panier'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            die(json_encode(['error' => 'Panier vide']));
        }

        try {
            // ====================================================
            // CALCUL DU MONTANT TOTAL (en centimes pour Stripe)
            // ====================================================

            $montant_total = 0;
            $line_items = [];

            foreach ($_SESSION['panier'] as $id => $quantite) {
                if (!ctype_digit(strval($id)) || $quantite <= 0) {
                    continue;
                }

                // Récupérer les infos du produit
                $stmt = $this->pdo->prepare("SELECT id, nom, prix FROM catalogue_funeraire WHERE id = ? LIMIT 1");
                $stmt->execute([(int) $id]);
                $produit = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($produit) {
                    $prix_cents = (int) ($produit['prix'] * 100); // Stripe utilise les cents
                    $montant_total += $prix_cents * $quantite;
                }
            }

            if ($montant_total <= 0) {
                http_response_code(400);
                die(json_encode(['error' => 'Montant invalide']));
            }

            // ====================================================
            // MODE DÉMO (Sans Stripe réelle)
            // ====================================================
            // En production, décommenter le code Stripe réel ci-dessous

            // Générer un identifiant de paiement unique
            $payment_id = 'pay_' . uniqid();
            $_SESSION['pending_payment'] = [
                'id' => $payment_id,
                'amount' => $montant_total / 100, // Convertir en euros
                'items' => $_SESSION['panier'],
                'created_at' => time(),
                'status' => 'pending'
            ];

            // Rediriger vers la page de confirmation (qui simule Stripe)
            // En production: rediriger vers $session->url
            header('Location: payment-form.php?payment_id=' . urlencode($payment_id));
            exit;

            /*
            // ====================================================
            // CODE STRIPE RÉEL (décommenter en production)
            // ====================================================

            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/payment-success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/panier.php',
                'customer_email' => $_SESSION['email'] ?? '',
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'redirect_url' => $session->url,
                'session_id' => $session->id,
            ]);
            exit;
            */

        } catch (Exception $e) {
            error_log("Erreur création session paiement: " . $e->getMessage() . " | " . $e->getFile() . ":" . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la création de la session de paiement: ' . $e->getMessage()
            ]);
        }
    }
}
