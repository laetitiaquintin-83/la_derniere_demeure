<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

$recommandationController = new RecommandationController(new RecommandationModel($pdo));
$data = $recommandationController->index();
extract($data, EXTR_SKIP);
?>
<?php require __DIR__ . '/../../../app/Views/pages/recommandations.php'; ?>
