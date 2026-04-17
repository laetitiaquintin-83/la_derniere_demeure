<?php 
require_once __DIR__ . '/../app/bootstrap.php';

// Calcul du nombre total d'articles pour le compteur du menu
$nombre_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

$message_succes = '';

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 🔐 Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !validerTokenCSRF($_POST['csrf_token'])) {
        $message_succes = "⚠️ Erreur de sécurité. Veuillez réessayer.";
    } else {
        // Pour l'instant, on simule l'envoi. Plus tard, on pourra ajouter la fonction mail() ou l'enregistrement en BDD.
        $message_succes = "Votre message a été confié à notre scénographe. Nous vous répondrons avec la plus grande discrétion.";
    }
}
?>
<?php require __DIR__ . '/../app/Views/pages/contact.php'; ?>



