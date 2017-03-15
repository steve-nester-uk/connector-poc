<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1>Hello Callback</h1>
<?php
if (isset($request)) {
	print_r($request);
}
?>
<h1>Hello Session</h1>
<?php
if (isset($_SESSION)) {
	print_r($_SESSION);
}
?>
<?php
$workflow = "internal-to-external";
$url = "callback.php?connector=" . $request["connector"] . "&handleAction=true" . "&type=" . $workflow . "&ndc=" . $request["ndc"];
echo "<form action='" . $url . "' method='POST' />";
echo "Choose One: <input type='radio' name='choose' value='1' />";
echo "Choose Two: <input type='radio' name='choose' value='2' />";
echo "<input type='submit' value='Submit' />";
echo "</form>";
?>
</body>
</html>