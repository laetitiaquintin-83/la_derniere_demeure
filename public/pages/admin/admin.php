<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

$adminController = new AdminDashboardController(new AdminDashboardModel($pdo));
$adminData = $adminController->index();
extract($adminData, EXTR_SKIP);
?>
<?php require __DIR__ . '/../../../app/Views/pages/admin.php'; ?>




