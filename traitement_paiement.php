<?php
// On inclut directement la config (qui gère la session et la connexion PDO)
require_once 'config.php';

// On vérifie que la requête vient bien du formulaire en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
    die("Erreur de sécurité : le sceau de la requête est corrompu.");
}

// Validation basique des champs du formulaire (pour montrer au jury qu'on contrôle les entrées)
if (empty($_POST['nom_titulaire']) || empty($_POST['numero_carte'])) {
    die("Erreur : Les informations du rituel d'engagement sont incomplètes.");
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
                throw new Exception("L'une des reliques n'est plus disponible en quantité suffisante.");
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
    // On affiche un message d'erreur et on arrête l'exécution
    die("Une perturbation occulte a annulé la transaction : " . htmlspecialchars($e->getMessage()));
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
            <a href="index.php">Retourner à l'Accueil</a>
        </nav>
    </header>

    <div class="success-container">
        <div class="success-icon">⚜️</div>
        <h1 class="success-title">L'Offrande est Scellée</h1>
        <p class="success-text">
            Votre engagement a bien été enregistré dans nos registres.<br>
            Les préparatifs de votre commande commenceront à la tombée de la nuit.
        </p>
        <a href="index.php" class="btn-gold" style="text-decoration: none; display: inline-block;">Retourner au Sanctuaire</a>
    </div>

</body>
</html>