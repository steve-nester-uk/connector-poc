<?php
echo file_get_contents('php://input');
die();
$request = array("echoRequest" => $_POST);
echo "got here";
echo json_decode($request);
echo "and here";
?>