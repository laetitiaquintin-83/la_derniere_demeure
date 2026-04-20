<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$paymentProcessController = new PaymentProcessController($pdo);
$paymentProcessController->process();
?>
