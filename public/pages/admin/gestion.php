<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

$inventoryController = new InventoryViewController(new AdminDashboardModel($pdo));
$data = $inventoryController->view();
extract($data, EXTR_SKIP);
?>
<?php require __DIR__ . '/../../../app/Views/pages/gestion.php'; ?>




