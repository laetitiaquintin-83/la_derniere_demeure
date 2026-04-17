<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/Models/AdminDashboardModel.php';
require_once __DIR__ . '/app/Controllers/AdminDashboardController.php';

$adminController = new AdminDashboardController(new AdminDashboardModel($pdo));
$adminData = $adminController->index();
extract($adminData, EXTR_SKIP);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | Registre de la Crypte</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --gold: #d4af37; --dark-bg: #0a0a0a; --panel-bg: #111111; }
        body.admin-body { background-color: var(--dark-bg); color: #e0e0e0; font-family: 'Arial', sans-serif; margin: 0; }
        .admin-nav nav { display: flex; align-items: center; padding: 20px 5%; background: #000; border-bottom: 1px solid var(--gold); }
        .admin-nav a { color: #fff; text-decoration: none; margin-right: 25px; font-family: 'Cinzel', serif; font-size: 0.9rem; transition: color 0.3s; }
        .admin-nav a:hover, .admin-nav a.active { color: var(--gold); }
        .logout-inline { margin-left: 0; }
        .logout-inline form { margin: 0; }
        .logout-inline button {
            background: transparent;
            border: none;
            color: #ff4c4c;
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            cursor: pointer;
            padding: 0;
        }
        .admin-container { max-width: 900px; margin: 40px auto; background: var(--panel-bg); padding: 40px; border: 1px solid #222; }
        .admin-title { font-family: 'Cinzel', serif; color: var(--gold); text-align: center; font-size: 2rem; margin-bottom: 30px; text-transform: uppercase; }
        .error-box { background: rgba(255, 76, 76, 0.15); border: 1px solid #ff4c4c; color: #ff4c4c; padding: 15px; margin-bottom: 25px; text-align: center; }
        .success-box { background: rgba(74, 222, 128, 0.15); border: 1px solid #4ade80; color: #4ade80; padding: 15px; margin-bottom: 25px; text-align: center; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-family: 'Cinzel', serif; color: var(--gold); font-size: 0.8rem; }
        input[type="text"], input[type="number"], select, textarea { width: 100%; padding: 12px; background: #050505; border: 1px solid #333; color: #fff; box-sizing: border-box; }
        .btn-crypt { background: var(--gold); color: #000; border: none; padding: 15px 30px; font-family: 'Cinzel', serif; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-crypt:hover { background: #fff; }
        .form-row { display: flex; gap: 20px; }
        .form-row > div { flex: 1; }
        .message-card { background: #050505; border: 1px solid #222; padding: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 20px; }
        .admin-quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 30px; }
        .admin-quick-link {
            display: block;
            padding: 22px;
            border: 1px solid rgba(212, 175, 55, 0.22);
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.06), rgba(5, 5, 5, 0.92));
            color: #f1dfb0;
            border-radius: 14px;
            text-decoration: none;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .admin-quick-link:hover {
            transform: translateY(-2px);
            border-color: rgba(212, 175, 55, 0.45);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.35);
        }
        .admin-quick-link .label { display: block; font-family: 'Cinzel', serif; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; color: #fff; }
        .admin-quick-link .desc { display: block; color: #bda97a; line-height: 1.7; font-size: 0.95rem; }
    </style>
</head>
<body class="admin-body"> 
    
    <header class="admin-nav">
        <nav>
            <a href="index.php">✦ Accueil</a>
            <a href="catalogue.php">✿ Catalogue</a>
            <a href="admin.php" class="active">◆ Registre</a>
            <a href="gestion.php">✦ Inventaire</a>
            <a href="panier.php" style="margin-left: auto;">✵ L'Offrande (<?php echo $nombre_articles; ?>)</a>
            <span class="logout-inline">
                <form method="POST" action="logout.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(genererTokenCSRF()); ?>">
                    <button type="submit">◇ Quitter</button>
                </form>
            </span>
        </nav>
    </header>

    <div class="admin-container">
        <h1 class="admin-title">Panneau du Gardien</h1>
        <div class="admin-quick-links">
            <a href="admin.php" class="admin-quick-link">
                <span class="label">◆ Registre</span>
                <span class="desc">Gérer les articles, ajouter une nouvelle pièce et garder l’ensemble du catalogue sous contrôle.</span>
            </a>
            <a href="gestion.php" class="admin-quick-link">
                <span class="label">✦ Inventaire</span>
                <span class="desc">Consulter l’état du stock, modifier les fiches et retirer un article si nécessaire.</span>
            </a>
        </div>

        <h1 class="admin-title">Ajouter un Article</h1>
        
        <?php if($message): ?>
            <div class="<?php echo ($messageType === 'success') ? 'success-box' : 'error-box'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="ajouter_article" value="1">
            
            <div class="form-group">
                <label>Désignation de l'Article</label>
                <input type="text" name="nom" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Nature / Univers</label>
                    <select name="categorie" required>
                        <option value="Cercueils">Cercueils & Sarcophages</option>
                        <option value="Urnes">Urnes Cinéraires</option>
                        <option value="Hommages Floraux">Hommages Floraux</option>
                        <option value="Stèles">Stèles</option>
                        <option value="Animaux">Repos des Fidèles</option>
                        <option value="Jardin">Jardin des Souvenirs</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Offrande (€)</label>
                    <input type="number" step="0.01" name="prix" required>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" value="1" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label>Épitaphe (Description)</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label>Image de l'Article</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            
            <button type="submit" class="btn-crypt">Sceller au Catalogue</button>
        </form>
    </div>

    <div class="admin-container">
        <h2 class="admin-title" style="font-size: 1.5rem;">Modération du Jardin</h2>
        
        <?php if (empty($attente)): ?>
            <p style="text-align: center; color: #666;">Aucune nouvelle pensée en attente.</p>
        <?php else: 
            foreach ($attente as $m): ?>
                <div class="message-card">
                    <?php if($m['photo_path']): ?>
                        <img src="<?php echo htmlspecialchars($m['photo_path']); ?>" style="width: 60px; height: 60px; object-fit: cover; border: 1px solid var(--gold);">
                    <?php endif; ?>
                    <div style="flex: 1;">
                        <strong style="color: var(--gold);"><?php echo htmlspecialchars($m['nom_animal']); ?></strong> 
                        <span style="font-size: 0.8rem; color: #888;"> (Par <?php echo htmlspecialchars($m['nom_proprietaire']); ?>)</span>
                        <p style="margin: 5px 0; font-size: 0.9rem; font-style: italic;">"<?php echo htmlspecialchars($m['message']); ?>"</p>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <input type="hidden" name="message_id" value="<?php echo (int)$m['id']; ?>">
                            <input type="hidden" name="moderation_action" value="approve">
                            <button type="submit" style="background:none;border:none;color:#4ade80;text-decoration:none;font-weight:bold;cursor:pointer;padding:0;">[ VALIDER ]</button>
                        </form>
                        <form method="POST" style="margin:0;" onsubmit="return confirm('Bannir cette pensée ?')">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <input type="hidden" name="message_id" value="<?php echo (int)$m['id']; ?>">
                            <input type="hidden" name="moderation_action" value="delete">
                            <button type="submit" style="background:none;border:none;color:#ff4c4c;text-decoration:none;font-size:0.8rem;cursor:pointer;padding:0;">[ SUPPRIMER ]</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; 
        endif; ?>
    </div>
</body>
</html>