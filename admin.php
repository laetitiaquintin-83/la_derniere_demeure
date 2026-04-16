<?php
require_once 'config.php';

// 1. Vérifier l'authentification (Sécurité Gardien)
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

$message = "";
$messageType = ""; 

// --- LOGIQUE DE MODÉRATION DU JARDIN DES SOUVENIRS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['moderation_action'])) {
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        $message = "Erreur de sécurité : action de modération invalide.";
        $messageType = "error";
    } else {
        $message_id = $_POST['message_id'] ?? '';
        if (!ctype_digit((string)$message_id)) {
            $message = "Identifiant de message invalide.";
            $messageType = "error";
        } elseif ($_POST['moderation_action'] === 'approve') {
            $pdo->prepare("UPDATE livre_dor_animaux SET approuve = 1 WHERE id = ?")->execute([(int)$message_id]);
            log_audit_event('APPROVE', 'livre_dor_animaux', (int)$message_id, null, ['approuve' => 1]);
            $message = "La pensée a été scellée dans le Jardin.";
            $messageType = "success";
        } elseif ($_POST['moderation_action'] === 'delete') {
            $stmt_old = $pdo->prepare("SELECT * FROM livre_dor_animaux WHERE id = ? LIMIT 1");
            $stmt_old->execute([(int)$message_id]);
            $old_message = $stmt_old->fetch(PDO::FETCH_ASSOC) ?: null;
            $pdo->prepare("DELETE FROM livre_dor_animaux WHERE id = ?")->execute([(int)$message_id]);
            log_audit_event('DELETE', 'livre_dor_animaux', (int)$message_id, $old_message, null);
            $message = "La pensée a été bannie du registre.";
            $messageType = "error";
        }
    }
}

// Génération du jeton CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Compteur panier
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

// 2. Traitement du Formulaire d'Ajout d'Article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_article'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Erreur de sécurité : le sceau CSRF est invalide.";
        $messageType = "error";
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        $prix = $_POST['prix'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if (empty($nom) || empty($categorie) || $prix === "" || $stock === "") {
            $message = "Erreur : tous les champs obligatoires doivent être complétés.";
            $messageType = "error";
        } elseif (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                $message = "Erreur : une image est requise.";
            $messageType = "error";
        } else {
            $target_dir = "images/catalogue/";
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            
            // SÉCURITÉ: Extensions blanches seulement
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($file_extension, $allowed_extensions)) {
                $message = "Erreur : formats acceptés JPG, PNG, WebP uniquement.";
                $messageType = "error";
            } else {
                // SÉCURITÉ: Vérifier la VRAIE MIME type (côté serveur, pas le header du client)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $real_mime = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
                finfo_close($finfo);
                
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($real_mime, $allowed_mimes)) {
                    $message = "Erreur de sécurité : Le fichier uploadé n'est pas une image valide.";
                    $messageType = "error";
                } else {
                    // SÉCURITÉ: Vérifier que la taille est raisonnable (max 5MB)
                    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
                        $message = "Erreur : La taille du fichier dépasse 5MB.";
                        $messageType = "error";
                    } else {
                        $file_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
                        $target_file = $target_dir . $file_name;
                        
                        // SÉCURITÉ: Résoudre et valider le chemin final
                        $real_path = realpath($target_dir) . DIRECTORY_SEPARATOR . $file_name;
                        if (strpos($real_path, realpath($target_dir)) !== 0) {
                            $message = "Erreur de sécurité : Chemin invalide (tentative de traversée répertoire).";
                            $messageType = "error";
                        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $real_path)) {
                            try {
                                $sql = "INSERT INTO catalogue_funeraire (nom, description, prix, image_path, categorie, stock) VALUES (?, ?, ?, ?, ?, ?)";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$nom, $description, (float)$prix, $target_file, $categorie, (int)$stock]);
                                log_audit_event('CREATE', 'catalogue_funeraire', $pdo->lastInsertId(), null, [
                                    'nom' => $nom,
                                    'categorie' => $categorie,
                                    'prix' => (float)$prix,
                                    'stock' => (int)$stock,
                                    'image_path' => $target_file,
                                ]);
                                $message = "L'article « $nom » a été ajouté avec succès.";
                                $messageType = "success";
                            } catch (PDOException $e) {
                                if (file_exists($target_file)) { unlink($target_file); }
                                $message = "Erreur SQL : " . $e->getMessage();
                                $messageType = "error";
                            }
                        }
                    }
                }
            }
        }
    }
}
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
            <a href="logout.php" style="color: #ff4c4c;">◇ Quitter</a>
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
        
        <?php
        $attente = $pdo->query("SELECT * FROM livre_dor_animaux WHERE approuve = 0 ORDER BY date_publication DESC")->fetchAll();
        if (empty($attente)): ?>
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