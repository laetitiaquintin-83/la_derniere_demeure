<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

$updateController = new AdminUpdateController(new AdminDashboardModel($pdo));
$updateData = $updateController->edit();
extract($updateData, EXTR_SKIP);
?>
<?php require __DIR__ . '/../../../app/Views/pages/modifier.php'; ?>





