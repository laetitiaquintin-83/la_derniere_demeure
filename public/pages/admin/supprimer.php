<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

$deleteController = new AdminDeleteController(new AdminDashboardModel($pdo));
$deleteController->delete();


