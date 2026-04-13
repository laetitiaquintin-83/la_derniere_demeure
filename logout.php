<?php
session_start();
// On détruit toutes les données de la session (la clé d'accès disparaît)
session_destroy();

// On renvoie vers la page d'accueil
header('Location: index.php');
exit;
?>