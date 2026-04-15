<?php
// On inclut la configuration et les fonctions de sécurité (cela démarre aussi la session)
require_once 'config.php';

$erreur = "";

// Le hachage généré au préalable (ne JAMAIS utiliser password_hash() directement ici)
// Remplace cette chaîne par celle que tu auras générée pour "cerbere" !
$hash_sauvegarde = '$2y$10$dQ04JR2zzMidalMeBMeMiuNgBnSaJBv/PNRYq2fxptuFmGnl1JDO2'; 

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Vérification du jeton CSRF (Protection contre les failles inter-sites)
    $token_soumis = $_POST['csrf_token'] ?? '';
    if (!validerTokenCSRF($token_soumis)) {
        die("Erreur de sécurité : Jeton invalide. Le sceau a été corrompu.");
    }

    $mdp_saisi = $_POST['mot_de_passe'] ?? '';
    
    // 2. On vérifie de manière sécurisée si le mot de passe correspond
    if (password_verify($mdp_saisi, $hash_sauvegarde)) {
        
        // Protection contre la fixation de session (Excellente pratique pour l'examen)
        session_regenerate_id(true);

        // Le mot de passe est bon, on donne la clé d'accès (nom identique à index.php)
        $_SESSION['admin_connecte'] = true;
        
        // On redirige vers le registre
        header('Location: admin.php');
        exit;
    } else {
        // En cas d'erreur, on peut imaginer ajouter un délai (sleep(2)) pour ralentir les attaques par force brute
        $erreur = "Accès refusé. Sceau incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Accès Gardien | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: rgba(10, 10, 10, 0.85);
            border: 1px solid rgba(181, 148, 16, 0.3);
            padding: 40px;
            border-radius: 3px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
            text-align: center;
        }
        .login-title {
            color: var(--gold);
            font-family: 'Cinzel', serif;
            font-size: 1.5em;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .error-msg {
            color: #d9534f;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        .login-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(181, 148, 16, 0.4);
            color: #fff;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            letter-spacing: 2px;
        }
        .login-input:focus {
            border-color: var(--gold-bright);
            outline: none;
        }
        .btn-login {
            width: 100%;
            background: linear-gradient(45deg, #8a7312, #b59410);
            color: #0a0a0a;
            border: none;
            padding: 15px;
            font-family: 'Cinzel', serif;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body class="admin-body">

    <header class="admin-nav">
        <nav>
            <a href="index.php">Retourner au Sanctuaire</a>
        </nav>
    </header>

    <div class="login-container">
        <h2 class="login-title">Accès Gardien</h2>
        
        <?php if (!empty($erreur)): ?>
            <div class="error-msg"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo genererTokenCSRF(); ?>">
            
            <input type="password" name="mot_de_passe" class="login-input" placeholder="Mot de passe" required>
            <button type="submit" class="btn-login">Déverrouiller</button>
        </form>
    </div>

</body>
</html>