<?php
// On inclut la config qui gÃ¨re dÃ©jÃ  session_start() et PDO
require_once __DIR__ . '/../../app/bootstrap.php';

// 1. VÃ‰RIFICATION DE L'ACCÃˆS ADMIN
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// 2. VÃ‰RIFICATION DU JETON CSRF (Le bouclier)
// On vÃ©rifie que le token envoyÃ© dans l'URL correspond Ã  celui en session
if (!isset($_GET['token']) || !validerTokenCSRF($_GET['token'])) {
    die("Erreur de sÃ©curitÃ© : Le sceau de suppression est invalide ou a expirÃ©.");
}

// 3. VALIDATION DE L'ID
if (!isset($_GET['id']) || !ctype_digit(strval($_GET['id']))) {
    die("Erreur : L'identifiant de l'article est corrompu.");
}

$id = (int)$_GET['id'];

try {
    // 4. RÃ‰CUPÃ‰RATION DES INFOS (Pour le nettoyage du fichier)
    $stmt = $pdo->prepare("SELECT image_path FROM catalogue_funeraire WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch();

    if (!$produit) {
        die("L'article a dÃ©jÃ  rejoint le nÃ©ant...");
    }

    // 5. SUPPRESSION PHYSIQUE DE L'IMAGE
    // On s'assure de ne pas supprimer par erreur un dossier ou un fichier vital
    if (!empty($produit['image_path']) && file_exists($produit['image_path']) && is_file($produit['image_path'])) {
        unlink($produit['image_path']);
    }

    // 6. SUPPRESSION EN BASE DE DONNÃ‰ES
    $delete_stmt = $pdo->prepare("DELETE FROM catalogue_funeraire WHERE id = ?");
    $delete_stmt->execute([$id]);

    // SuccÃ¨s : retour Ã  la gestion
    header("Location: gestion.php?msg=suppression_ok");
    exit();

} catch (PDOException $e) {
    error_log("Erreur lors de l'anÃ©antissement : " . $e->getMessage());
    die("Une force obscure a empÃªchÃ© la suppression de l'article.");
}

