<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$logoutController = new LogoutController();
$logoutController->handle();
?>
<?php require __DIR__ . '/../../app/Views/pages/logout.php'; ?>



