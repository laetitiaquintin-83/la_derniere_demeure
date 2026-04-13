<?php
require_once 'config.php';

// 1. Vérifier l'authentification (session_start est dans config.php)
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// 2. Vérifier la méthode et le jeton CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: gestion.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
    die("Erreur de sécurité : Jeton CSRF invalide.");
}

// 3. Valider l'ID et l'ajustement
if (!isset($_POST['id']) || !ctype_digit(strval($_POST['id']))) {
    die("Erreur : ID invalide.");
}

$id = (int)$_POST['id'];
$ajustement = (int)($_POST['quantity'] ?? 0);

if ($ajustement < -999 || $ajustement > 999) {
    die("Erreur : l'ajustement est hors limites.");
}

try {
    // 4. Mettre à jour le stock en empêchant de descendre sous 0
    // La fonction SQL GREATEST(0, valeur) assure que le résultat ne sera jamais inférieur à zéro
    $stmt = $pdo->prepare("UPDATE catalogue_funeraire SET stock = GREATEST(0, stock + ?) WHERE id = ?");
    $stmt->execute([$ajustement, $id]);

    // 5. Redirection avec succès
    header("Location: gestion.php?msg=stock_mis_a_jour");
    exit();

} catch (PDOException $e) {
    error_log("Erreur ajustement stock : " . $e->getMessage());
    die("Une erreur est survenue lors de la mise à jour des réserves.");
}