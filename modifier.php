<?php
require_once 'config.php';

// 1. Le verrou du Gardien (Sécurité)
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// Validation de l'ID
if (!isset($_GET['id']) || !ctype_digit(strval($_GET['id']))) {
    die("Erreur : ID invalide.");
}
$id = (int)$_GET['id'];
$allowed_image_dir = __DIR__ . '/images/catalogue/';

// Récupérer les catégories disponibles
$catStmt = $pdo->query("SELECT DISTINCT categorie FROM catalogue_funeraire WHERE categorie IS NOT NULL AND categorie != '' ORDER BY categorie ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// On récupère les infos actuelles de l'article
$stmt = $pdo->prepare("SELECT * FROM catalogue_funeraire WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) { 
    die("Le cercueil a disparu dans les brumes..."); 
}

// Si on soumet le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        die("Erreur de sécurité : jeton CSRF invalide.");
    }

    $old_values = [
        'nom' => $produit['nom'],
        'categorie' => $produit['categorie'],
        'essence_bois' => $produit['essence_bois'] ?? null,
        'couleur_velours' => $produit['couleur_velours'] ?? null,
        'prix' => $produit['prix'],
        'description' => $produit['description'],
        'image_path' => $produit['image_path'],
    ];
    
    $nom = trim($_POST['nom'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $essence_bois = trim($_POST['essence_bois'] ?? '');
    $couleur_velours = trim($_POST['couleur_velours'] ?? '');
    $prix = $_POST['prix'] ?? 0;
    $description = trim($_POST['description'] ?? '');

    if (empty($nom) || empty($prix) || empty($categorie)) {
        die("Erreur : le nom, la catégorie et le prix sont obligatoires.");
    }

    $image_path = $produit['image_path']; 
    $nouvelle_image_uploadee = false;
    $ancienne_image = $produit['image_path'];

    // Priorité 1 : Chemin manuel
    if (!empty($_POST['image_path_manuel'])) {
        $manual_input = trim($_POST['image_path_manuel']);
        $manual_file = basename($manual_input);
        $candidate = $allowed_image_dir . $manual_file;
        $candidate_real = realpath($candidate);
        $allowed_real = realpath($allowed_image_dir);

        if ($candidate_real === false || $allowed_real === false || strpos($candidate_real, $allowed_real) !== 0) {
            die("Erreur : chemin image manuel invalide.");
        }

        $image_path = 'images/catalogue/' . $manual_file;
    } 
    // Priorité 2 : Upload de fichier
    elseif (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $target_dir = "images/catalogue/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $file_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        // Validation simple
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_extension, $allowed) && $_FILES["image"]["size"] < 5000000) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // On prépare la suppression de l'ancienne image plus tard
                $ancienne_image = $produit['image_path'];
                $image_path = $target_file;
                $nouvelle_image_uploadee = true;
            }
        }
    }

    $sql = "UPDATE catalogue_funeraire SET nom=?, categorie=?, essence_bois=?, couleur_velours=?, prix=?, description=?, image_path=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nom, $categorie, $essence_bois, $couleur_velours, (float)$prix, $description, $image_path, $id]);
    
    if ($success) {
        log_audit_event('UPDATE', 'catalogue_funeraire', $id, $old_values, [
            'nom' => $nom,
            'categorie' => $categorie,
            'essence_bois' => $essence_bois,
            'couleur_velours' => $couleur_velours,
            'prix' => (float)$prix,
            'description' => $description,
            'image_path' => $image_path,
        ]);

        // Nettoyage de l'ancienne image si on en a mis une nouvelle (et que ce n'est pas une image par défaut)
        $old_real = !empty($ancienne_image) ? realpath($ancienne_image) : false;
        $allowed_real = realpath($allowed_image_dir);
        if ($nouvelle_image_uploadee && $old_real && $allowed_real && strpos($old_real, $allowed_real) === 0 && file_exists($old_real)) {
            // Optionnel : ne pas supprimer si c'est une image partagée
            unlink($old_real);
        }
        header("Location: gestion.php?success=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Modifier l'Article | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-container">
        <h1 class="admin-title">Refaçonner l'Œuvre</h1>
        
        <form method="POST" enctype="multipart/form-data" class="crypt-form">
            <input type="hidden" name="csrf_token" value="<?php echo genererTokenCSRF(); ?>">
            
            <div style="text-align: center; margin-bottom: 20px;">
                <p style="color: var(--gold);">Apparence actuelle :</p>
                <img src="<?php echo htmlspecialchars($produit['image_path']); ?>" style="max-width: 150px; border: 1px solid var(--gold);">
            </div>

            <div class="form-group">
                <label>Nom du modèle</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
            </div>

            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($cat === $produit['categorie']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Prix (€)</label>
                <input type="number" step="0.01" name="prix" value="<?php echo $produit['prix']; ?>" required>
            </div>

            <div class="form-group">
                <label>Épitaphe (Description)</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($produit['description']); ?></textarea>
            </div>

            <div style="background: rgba(181, 148, 16, 0.1); padding: 15px; border-radius: 5px;">
                <label>Changer l'image (Upload)</label>
                <input type="file" name="image">
                <p style="margin: 10px 0; text-align: center; font-style: italic;">ou</p>
                <label>Chemin manuel</label>
                <input type="text" name="image_path_manuel" placeholder="Ex: images/catalogue/article.jpg">
            </div>

            <button type="submit" class="btn-gold" style="width: 100%; margin-top: 20px; cursor: pointer;">Sceller les Modifications</button>
            <a href="gestion.php" style="display: block; text-align: center; color: #888; margin-top: 15px;">Retour</a>
        </form>
    </div>
</body>
</html>