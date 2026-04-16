<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🔐 Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        http_response_code(403);
        die("⚠️ Erreur de sécurité CSRF. Veuillez réessayer.");
    }
    
    $nom_p = htmlspecialchars($_POST['nom_proprietaire']);
    $nom_a = htmlspecialchars($_POST['nom_animal']);
    $msg = htmlspecialchars($_POST['message']);
    $photo_path = null;

    // Gestion sécurisée de la photo
    $photo_path = null;
    if (isset($_FILES['photo_compagnon']) && $_FILES['photo_compagnon']['error'] === 0) {
        $dir = "images/souvenirs/";
        if (!file_exists($dir)) mkdir($dir, 0777, true);

        // SÉCURITÉ: Vérifier extension
        $ext = strtolower(pathinfo($_FILES['photo_compagnon']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (!in_array($ext, $allowed_extensions)) {
            // Erreur silencieuse: continuer sans photo
            $ext = null;
        } elseif ($_FILES['photo_compagnon']['size'] > 3 * 1024 * 1024) {
            // Fichier trop gros: skipper silencieusement
            $ext = null;
        } else {
            // SÉCURITÉ: Vérifier le type MIME réel (côté serveur)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $real_mime = finfo_file($finfo, $_FILES['photo_compagnon']['tmp_name']);
            finfo_close($finfo);
            
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($real_mime, $allowed_mimes)) {
                $ext = null; // Type MIME invalide
            }
        }
        
        if ($ext) {
            $filename = "souvenir_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            $full_path = $dir . $filename;
            
            // SÉCURITÉ: Vérifier la traversée répertoire
            $real_path = realpath($dir) . DIRECTORY_SEPARATOR . $filename;
            if (strpos($real_path, realpath($dir)) === 0 && move_uploaded_file($_FILES['photo_compagnon']['tmp_name'], $full_path)) {
                $photo_path = $full_path;
            }
        }
    }

    $sql = "INSERT INTO livre_dor_animaux (nom_proprietaire, nom_animal, message, photo_path) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom_p, $nom_a, $msg, $photo_path]);

    header('Location: repos_des_fideles.php?status=success');
    exit;
}