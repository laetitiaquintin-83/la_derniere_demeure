<?php
// On inclut directement la config (qui gère la session et la connexion PDO)
require_once __DIR__ . '/app/bootstrap.php';

// Endpoint historique désactivé: le paiement passe par create-checkout-session.php
// pour éviter toute collecte locale de données carte.
header('Location: panier.php');
exit;

// On vérifie que la requête vient bien du formulaire en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
    die("Erreur de sécurité : le sceau de la requête est corrompu.");
}

// ==========================================
// VALIDATION SÉCURISÉE DES DONNÉES PAIEMENT
// ==========================================

// Fonction de validation: Algorithme Luhn (carte bancaire)
function valideeLuhn($numero) {
    $numero = preg_replace('/\D/', '', $numero);
    if (!preg_match('/^[0-9]{13,19}$/', $numero)) return false;
    
    $sum = 0;
    $parity = strlen($numero) % 2;
    for ($i = 0; $i < strlen($numero); $i++) {
        $digit = (int)$numero[$i];
        if ($i % 2 == $parity) $digit *= 2;
        if ($digit > 9) $digit -= 9;
        $sum += $digit;
    }
    return ($sum % 10) == 0;
}

// Validation basique des champs du formulaire
if (empty($_POST['nom_titulaire']) || empty($_POST['numero_carte'])) {
    die("Erreur : Les informations du rituel d'engagement sont incomplètes.");
}

// VALIDATION SÉCURISÉE: Numéro de carte (Luhn algorithm)
$numero_carte = preg_replace('/\D/', '', $_POST['numero_carte'] ?? '');
if (!valideeLuhn($numero_carte)) {
    die("Erreur de sécurité : Numéro de carte invalide (non conforme Luhn).");
}

// VALIDATION SÉCURISÉE: Date d'expiration (format MM/YY)
$date_exp = $_POST['date_expiration'] ?? '';
if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $date_exp)) {
    die("Erreur de sécurité : Format date invalide. Utilisez MM/YY.");
}
// Vérifier que la carte n'est pas expirée
list($mois, $year2) = explode('/', $date_exp);
$year_complet = 2000 + (int)$year2;
$mois_courant = (int)date('m');
$year_courant = (int)date('Y');
if ($year_complet < $year_courant || ($year_complet == $year_courant && $mois < $mois_courant)) {
    die("Erreur de sécurité : Carte expirée.");
}

// VALIDATION SÉCURISÉE: CVV (3-4 chiffres)
$cvv = preg_replace('/\D/', '', $_POST['cvv'] ?? '');
if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
    die("Erreur de sécurité : CVV invalide. Doit être 3-4 chiffres.");
}

// VALIDATION SÉCURISÉE: Nom du titulaire
$nom_titulaire = preg_replace('/[^a-zA-Zéèê\s\-\']/', '', $_POST['nom_titulaire']);
if (strlen($nom_titulaire) < 3 || strlen($nom_titulaire) > 50) {
    die("Erreur : Nom du titulaire invalide.");
}

// On vérifie qu'il y a bien quelque chose à commander
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

// ==========================================
// TRAITEMENT DE LA COMMANDE ET DES STOCKS
// ==========================================

try {
    // 1. DÉMARRAGE DE LA TRANSACTION
    // À partir d'ici, aucune modification en base n'est définitive tant qu'on ne fait pas "commit()"
    $pdo->beginTransaction(); 

    // Préparer la requête de décrémentation du stock
    $stmt = $pdo->prepare("UPDATE catalogue_funeraire SET stock = stock - ? WHERE id = ? AND stock >= ?");
    
    foreach ($_SESSION['panier'] as $id => $quantite) {
        if (ctype_digit(strval($id)) && $quantite > 0) {
            
            $stmt->execute([(int)$quantite, (int)$id, (int)$quantite]);
            
            // 2. VÉRIFICATION DU RÉSULTAT
            // Si rowCount est à 0, c'est que la condition "stock >= ?" a bloqué la mise à jour !
            if ($stmt->rowCount() === 0) {
                // On déclenche volontairement une exception pour arrêter le processus
                throw new Exception("stock_insuffisant");
            }
        }
    }
    
    // 3. VALIDATION DÉFINITIVE
    // Si la boucle s'est terminée sans encombre, on valide toutes les modifications d'un coup
    $pdo->commit();
    
    // Le paiement est "validé" et les stocks sont à jour, on vide le panier
    unset($_SESSION['panier']);

} catch (Exception $e) {
    // 4. ANNULATION
    // Si une erreur SQL ou notre Exception personnalisée survient, on annule TOUT ce qui s'est passé depuis beginTransaction()
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Erreur de commande : " . $e->getMessage());
    
    // Déterminer le type d'erreur
    $error_code = $e->getMessage();
    $error_title = "L'Adieu n'a pu se conclure";
    $error_message = "Une perturbation éthérée a empêché votre offrande.";
    
    if ($error_code === 'stock_insuffisant') {
        $error_message = "L'une de vos reliques a été réclamée par un autre fidèle. Notre inventaire a été mis à jour.";
    }
    
    // Afficher la page d'erreur élégante
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($error_title); ?> | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #050505 0%, #1a1a1a 100%);
            padding: 20px;
        }
        .error-box {
            text-align: center;
            max-width: 600px;
            background: rgba(10, 10, 10, 0.8);
            border: 2px solid #D4AF37;
            border-radius: 10px;
            padding: 60px 40px;
            box-shadow: 0 15px 60px rgba(212, 175, 55, 0.2);
        }
        .error-icon {
            font-size: 4em;
            margin-bottom: 20px;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .error-title {
            color: #D4AF37;
            font-family: 'Cinzel', serif;
            font-size: 2em;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        .error-message {
            color: #b3b3b3;
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 40px;
            font-style: italic;
        }
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-error {
            padding: 12px 30px;
            border: 1px solid #D4AF37;
            background: transparent;
            color: #D4AF37;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: 'Cinzel', serif;
            font-size: 0.95em;
            letter-spacing: 1px;
        }
        .btn-error:hover {
            background: #D4AF37;
            color: #000;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.5);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-box">
            <div class="error-icon">⚰️</div>
            <h1 class="error-title"><?php echo htmlspecialchars($error_title); ?></h1>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <div class="error-actions">
                <a href="panier.php" class="btn-error">↻ Retour au Panier</a>
                <a href="index.php" class="btn-error">✦ Retour à l'Accueil</a>
            </div>
        </div>
    </div>
</body>
</html>
    <?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sceau Apposé | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            background: rgba(10, 10, 10, 0.85);
            border: 1px solid var(--gold);
            padding: 50px 30px;
            box-shadow: 0 0 30px rgba(181, 148, 16, 0.3);
            border-radius: 3px;
        }
        .success-icon {
            font-size: 4em;
            color: var(--gold-bright);
            margin-bottom: 20px;
        }
        .success-title {
            color: var(--gold);
            font-family: 'Cinzel', serif;
            font-size: 2em;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .success-text {
            color: #b3b3b3;
            line-height: 1.6;
            margin-bottom: 40px;
        }
    </style>
</head>
<body class="admin-body">

    <header class="admin-nav">
        <nav>
            <a href="index.php">✦ Retourner à l'Accueil</a>
        </nav>
    </header>

    <div class="success-container">
        <div class="success-icon">⚜️</div>
        <h1 class="success-title">L'Offrande est Scellée</h1>
        <p class="success-text">
            Votre engagement a bien été enregistré dans nos registres.<br>
            Les préparatifs de votre commande commenceront à la tombée de la nuit.
        </p>
        <a href="index.php" class="btn-gold" style="text-decoration: none; display: inline-block;">✦ Retourner au Sanctuaire</a>
    </div>

</body>
</html>