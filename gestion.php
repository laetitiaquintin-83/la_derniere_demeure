<?php
require_once __DIR__ . '/app/bootstrap.php';

if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$produits = (new AdminDashboardModel($pdo))->getProductsForInventory();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo genererTokenCSRF(); ?>">
    <title>Gestion du Stock | La Dernière Demeure</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .admin-nav nav {
            background: rgba(10, 10, 10, 0.95);
            border-bottom: 1px solid rgba(181, 148, 16, 0.3);
            justify-content: center;
        }
        .admin-nav a.active {
            color: #fff;
            text-shadow: 0 0 15px var(--gold-bright);
            border-bottom: 2px solid var(--gold);
        }
        .inventory-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .inventory-table th { background: rgba(181, 148, 16, 0.2); color: var(--gold); padding: 12px; text-align: left; font-family: 'Cinzel', serif; letter-spacing: 1px; }
        .inventory-table td { border-bottom: 1px solid rgba(181, 148, 16, 0.2); padding: 12px; color: #bbb; }
        .inventory-table tr:hover { background: rgba(181, 148, 16, 0.1); }
        .item-name { display: flex; align-items: center; gap: 10px; }
        .mini-thumb { width: 40px; height: 40px; object-fit: cover; border: 1px solid var(--gold); border-radius: 3px; }
        .badge { background: rgba(181, 148, 16, 0.3); color: var(--gold); padding: 3px 8px; font-size: 0.85em; border-radius: 2px; }
        .stock-number { font-weight: bold; color: #fff; }
        .in-stock { color: #4ade80; }
        .low-stock { color: #facc15; }
        .out-of-stock { color: #ff4c4c; }
        .quick-update { display: flex; gap: 5px; }
        .quick-update input { width: 60px; padding: 5px; background: rgba(0,0,0,0.5); border: 1px solid rgba(181, 148, 16, 0.4); color: #fff; }
        .btn-action { background: var(--gold); color: #000; border: none; padding: 5px 10px; cursor: pointer; font-weight: bold; border-radius: 2px; }
        .btn-modify { background: rgba(100, 150, 200, 0.3); color: #64b3d9; padding: 5px 10px; border: none; cursor: pointer; text-decoration: none; border-radius: 2px; margin-right: 5px; }
        .btn-delete { background: rgba(200, 50, 50, 0.3); color: #ff6b6b; padding: 5px 10px; border: none; cursor: pointer; border-radius: 2px; }
        .btn-delete:hover { background: rgba(200, 50, 50, 0.5); }
        
        /* Actions cell avec flexbox */
        .actions-cell {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-modify, .btn-delete {
            white-space: nowrap;
            transition: all 0.3s ease;
            font-size: 0.9em;
            font-family: 'Cinzel', serif;
            letter-spacing: 0.5px;
        }
        
        .btn-modify:hover {
            background: rgba(100, 150, 200, 0.6) !important;
            box-shadow: 0 0 10px rgba(100, 150, 200, 0.4);
        }
        .logout-inline { margin-left: 15px; }
        .logout-inline form { margin: 0; }
        .logout-inline button {
            background: transparent;
            border: none;
            color: #d9534f;
            font-family: 'Cinzel', serif;
            cursor: pointer;
            padding: 0;
            font-size: inherit;
        }
    </style>
</head>
<body class="admin-body">
    <header class="admin-nav">
        <nav>
            <a href="index.php">✦ Accueil</a>
            <a href="catalogue.php">✿ Catalogue</a>
            <a href="foret.php">✾ Le Sanctuaire</a>
            <a href="admin.php">◆ Registre</a>
            <a href="gestion.php" class="active">✦ Inventaire</a>
            <a href="panier.php" style="margin-left: auto;">✵ L'Offrande <span id="cart-counter"><?php echo $nombre_articles; ?></span></a>
            <span class="logout-inline">
                <form method="POST" action="logout.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(genererTokenCSRF()); ?>">
                    <button type="submit">◇ Quitter</button>
                </form>
            </span>
        </nav>
    </header>

<section class="admin-section">
    <div class="admin-container">
        <h2 class="section-title">L'État des Réserves</h2>
        
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Catégorie</th>
                    <th>Prix Unit.</th>
                    <th>Stock Actuel</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $p) : 
                    // Logique visuelle pour le stock
                    $stockClass = ($p['stock'] <= 0) ? 'out-of-stock' : (($p['stock'] < 3) ? 'low-stock' : 'in-stock');
                    $stockLabel = ($p['stock'] <= 0) ? 'ÉPUISÉ' : (($p['stock'] < 3) ? 'CRITIQUE' : 'STABLE');
                ?>
                <tr>
                    <td class="item-name">
                        <img src="<?php echo htmlspecialchars($p['image_path']); ?>" class="mini-thumb">
                        <?php echo htmlspecialchars($p['nom']); ?>
                    </td>
                    <td><span class="badge"><?php echo htmlspecialchars($p['categorie']); ?></span></td>
                    <td><?php echo number_format($p['prix'], 2, ',', ' '); ?> €</td>
                    <td class="<?php echo $stockClass; ?>">
                        <span class="stock-number"><?php echo $p['stock']; ?></span>
                        <small><?php echo $stockLabel; ?></small>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="modifier.php?id=<?php echo htmlspecialchars($p['id']); ?>" class="btn-modify">Refaçonner</a>
                            <a href="supprimer.php?id=<?php echo htmlspecialchars($p['id']); ?>&token=<?php echo htmlspecialchars(genererTokenCSRF()); ?>" class="btn-delete" onclick="return confirm('Êtes-vous certain de vouloir supprimer cet article ?');">Anéantir</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($produits)): ?>
            <p style="text-align: center; color: #888; padding: 30px;">Aucun article actuellement répertorié.</p>
        <?php endif; ?>
    </div>
</section>

    <div id="toast" class="toast"></div>
    <script src="script.js"></script>
</body>
</html>