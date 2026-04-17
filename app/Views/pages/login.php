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
            <a href="index.php">✦ Retourner au Sanctuaire</a>
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

