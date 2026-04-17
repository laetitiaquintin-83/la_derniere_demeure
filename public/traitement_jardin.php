<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/app/Models/GardenModel.php';
require_once __DIR__ . '/app/Controllers/GardenController.php';

$controller = new GardenController(new GardenModel($pdo));
$controller->submit();
