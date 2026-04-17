<?php
require_once __DIR__ . '/../app/bootstrap.php';

$catalogueController = new CatalogueController(new CatalogueModel($pdo));
$catalogueData = $catalogueController->index();
extract($catalogueData, EXTR_SKIP);
?>
<?php require __DIR__ . '/../app/Views/pages/catalogue.php'; ?>


