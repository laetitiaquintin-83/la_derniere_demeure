<?php
require_once __DIR__ . '/app/bootstrap.php';

$authController = new AuthController(new AuthModel($pdo));
$authData = $authController->login();
extract($authData, EXTR_SKIP);
?>
<?php require __DIR__ . '/app/Views/pages/login.php'; ?>

