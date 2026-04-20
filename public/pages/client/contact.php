<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

$contactController = new ContactController();
$data = $contactController->handleSubmit();
extract($data, EXTR_SKIP);
?>
<?php require __DIR__ . '/../../../app/Views/pages/contact.php'; ?>





