<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$checkoutSessionController = new CheckoutSessionController($pdo);
$checkoutSessionController->createSession();
?>
