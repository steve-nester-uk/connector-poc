<?php
require_once(__DIR__ . "/Processing.php");
$response = (new Processing())->callback($_REQUEST);

echo $response;
die();
?>