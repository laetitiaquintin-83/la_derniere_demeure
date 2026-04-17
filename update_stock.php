<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/Models/InventoryModel.php';
require_once __DIR__ . '/app/Controllers/StockController.php';

$controller = new StockController(new InventoryModel($pdo));
$controller->update();