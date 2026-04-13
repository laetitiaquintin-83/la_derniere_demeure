<?php
// On inclut la config qui gère déjà session_start() et PDO
require_once 'config.php';

// 1. VÉRIFICATION DE L'ACCÈS ADMIN
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// 2. VÉRIFICATION DU JETON CSRF (Le bouclier)
// On vérifie que le token envoyé dans l'URL correspond à celui en session
if (!isset($_GET['token']) || !validerTokenCSRF($_GET['token'])) {
    die("Erreur de sécurité : Le sceau de suppression est invalide ou a expiré.");
}

// 3. VALIDATION DE L'ID
if (!isset($_GET['id']) || !ctype_digit(strval($_GET['id']))) {
    die("Erreur : L'identifiant de la relique est corrompu.");
}

$id = (int)$_GET['id'];

try {
    // 4. RÉCUPÉRATION DES INFOS (Pour le nettoyage du fichier)
    $stmt = $pdo->prepare("SELECT image_path FROM catalogue_funeraire WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch();

    if (!$produit) {
        die("La relique a déjà rejoint le néant...");
    }

    // 5. SUPPRESSION PHYSIQUE DE L'IMAGE
    // On s'assure de ne pas supprimer par erreur un dossier ou un fichier vital
    if (!empty($produit['image_path']) && file_exists($produit['image_path']) && is_file($produit['image_path'])) {
        unlink($produit['image_path']);
    }

    // 6. SUPPRESSION EN BASE DE DONNÉES
    $delete_stmt = $pdo->prepare("DELETE FROM catalogue_funeraire WHERE id = ?");
    $delete_stmt->execute([$id]);

    // Succès : retour à la gestion
    header("Location: gestion.php?msg=suppression_ok");
    exit();

} catch (PDOException $e) {
    error_log("Erreur lors de l'anéantissement : " . $e->getMessage());
    die("Une force obscure a empêché la suppression de la relique.");
}