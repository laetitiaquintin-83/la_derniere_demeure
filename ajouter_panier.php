<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/Models/CartActionModel.php';
require_once __DIR__ . '/app/Controllers/CartActionController.php';

$controller = new CartActionController(new CartActionModel($pdo));
$controller->add();