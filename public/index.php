<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/bootstrap.php';

$homeController = new HomeController(new HomePageModel($pdo));
$homeData = $homeController->index();
extract($homeData, EXTR_SKIP);
?>
<?php require __DIR__ . '/../app/Views/pages/index.php'; ?>


