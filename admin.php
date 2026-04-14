<?php
require_once 'config.php';

// 1. Vérifier l'authentification
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

$message = "";
$messageType = ""; // 'error' ou 'success'

// Calcul du compteur panier
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        $message = "Erreur de sécurité : jeton CSRF invalide.";
        $messageType = "error";
    } else {
        // Validation des données
        $nom = trim($_POST['nom'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        $prix = $_POST['prix'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $essence_bois = trim($_POST['essence_bois'] ?? '');
        $couleur_velours = trim($_POST['couleur_velours'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($nom) || empty($categorie) || $prix === "" || $stock === "") {
            $message = "Erreur : les champs obligatoires doivent être complétés.";
            $messageType = "error";
        } elseif (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            $message = "Erreur : une image est requise pour illustrer la relique.";
            $messageType = "error";
        } else {
            // Logique d'upload
            $target_dir = "images/catalogue/";
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $file_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
            $target_file = $target_dir . $file_name;

            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($_FILES["image"]["type"], $allowed_types)) {
                $message = "Erreur : format accepté JPG, PNG ou WebP uniquement.";
                $messageType = "error";
            } else {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    try {
                        $sql = "INSERT INTO catalogue_funeraire (nom, essence_bois, couleur_velours, description, prix, image_path, categorie, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$nom, $essence_bois, $couleur_velours, $description, (float)$prix, $target_file, $categorie, (int)$stock]);
                        
                        $message = "L'article « $nom » a été scellé avec succès.";
                        $messageType = "success";
                    } catch (PDOException $e) {
                        if (file_exists($target_file)) { unlink($target_file); }
                        $message = "Erreur SQL : Impossible d'enregistrer la relique.";
                        $messageType = "error";
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
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Administration | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .error-box { background: rgba(255, 76, 76, 0.2); border: 1px solid #ff4c4c; color: #ff4c4c; padding: 15px; margin-bottom: 20px; border-radius: 3px; }
        .success-box { background: rgba(74, 222, 128, 0.2); border: 1px solid #4ade80; color: #4ade80; padding: 15px; margin-bottom: 20px; border-radius: 3px; }
    </style>
</head>
<body class="admin-body"> 
    
    <header class="admin-nav">
        <nav>
            <a href="index.php">Accueil</a>
            <a href="images/catalogue.php">Le Catalogue</a>
            <a href="admin.php" class="active">Le Registre</a>
            <a href="gestion.php">L'Inventaire</a>
            <a href="panier.php" style="margin-left: auto;">L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
            <a href="logout.php" class="lock-link" style="color: #ff4c4c;">🔓 Quitter</a>
        </nav>
    </header>

    <div class="admin-container">
        <h1 class="admin-title">Registre de la Crypte</h1>
        
        <?php if($message): ?>
            <div class="<?php echo ($messageType === 'success') ? 'success-box' : 'error-box'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="crypt-form">
            <input type="hidden" name="csrf_token" value="<?php echo genererTokenCSRF(); ?>">
            
            <div class="form-group">
                <label>Désignation de l'article</label>
                <input type="text" name="nom" placeholder="Ex: L'Éminence Sombre..." required>
            </div>
            
            <div class="form-row" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 2;">
                    <label>Nature / Univers</label>
                    <select name="categorie" required>
                        <option value="Cercueils">Cercueils & Sarcophages</option>
                        <option value="Urnes">Urnes Cinéraires</option>
                        <option value="Hommages Floraux">Hommages Floraux</option>
                        <option value="Univers Passion">Univers Passion</option>
                        <option value="Stèles">Stèles</option>
                        <option value="Animaux">Repos des Fidèles</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Offrande (€)</label>
                    <input type="number" step="0.01" name="prix" placeholder="0.00" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Stock</label>
                    <input type="number" name="stock" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label>Épitaphe (Description)</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label>Relique Visuelle (Image)</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
            </div>
            
            <button type="submit" class="btn-crypt" style="width: 100%; margin-top: 20px; cursor: pointer;">Sceller dans le Catalogue</button>
        </form>
    </div>
</body>
</html>