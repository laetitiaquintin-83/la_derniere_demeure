<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_p = htmlspecialchars($_POST['nom_proprietaire']);
    $nom_a = htmlspecialchars($_POST['nom_animal']);
    $msg = htmlspecialchars($_POST['message']);
    $photo_path = null;

    // Gestion de la photo
    if (isset($_FILES['photo_compagnon']) && $_FILES['photo_compagnon']['error'] === 0) {
        $dir = "images/souvenirs/";
        if (!file_exists($dir)) mkdir($dir, 0777, true);

        $ext = pathinfo($_FILES['photo_compagnon']['name'], PATHINFO_EXTENSION);
        $filename = "souvenir_" . time() . "_" . uniqid() . "." . $ext;
        
        if (move_uploaded_file($_FILES['photo_compagnon']['tmp_name'], $dir . $filename)) {
            $photo_path = $dir . $filename;
        }
    }

    $sql = "INSERT INTO livre_dor_animaux (nom_proprietaire, nom_animal, message, photo_path) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom_p, $nom_a, $msg, $photo_path]);

    header('Location: repos_des_fideles.php?status=success');
    exit;
}