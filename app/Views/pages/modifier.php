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
                <label>Stock</label>
                <input type="number" name="stock" min="0" step="1" value="<?php echo (int) $produit['stock']; ?>" required>
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

