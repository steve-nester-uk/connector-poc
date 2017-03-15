<?php
if (isset($_POST["name"])) {
	function editConnector($name, $data) {
		$name = strtolower($name);
		$name = ucwords($name);
		$filename = $name . "Connector.php";
		$fullpath = __DIR__ . "/../connectors/" . $filename;

		$fh = fopen($fullpath, "w");
	    if (!is_resource($fh)) {
	        return false;
	    }
	    fwrite($fh, sprintf($data));
	    fclose($fh);

	    return true;
	}

	$name = $_POST["name"];
	$data = $_POST["data"];
	if (editConnector($name, $data)) {
		echo "edit file";
		//redirect to reload /rootDir/$name
		header('Location: ' . $config->rootDir . '/' . $name);
	}
	exit();
}
require_once(__DIR__ . "/../Connector.php");
$matches = array();
$rootDir = str_replace("/", "\/", $config->rootDir);
preg_match('/' . $rootDir .'\/\S+\/edit/i', $requestUri, $matches);
$match = str_replace($config->rootDir ."/", "", $matches[0]);
$match = str_replace("/edit", "", $match);

$connector = getConnector($match);
if (!isset($connector)) return "connector not found";

function getConnector($connectorName) {
	if (!isset($connectorName) || empty($connectorName)) return null;
	foreach (glob(__DIR__ . '/../connectors/*.php') as $file) {
		$conStr = str_replace(__DIR__  . "/../connectors/", "", $file);
		$conStr = str_replace("Connector.php", "", $conStr);
		if (strcasecmp($conStr,$connectorName) == 0) {
	        require_once $file;
			$class = basename($file, '.php');

	        if (class_exists($class)) return new $class();
    	}
    }
    //TODO: logging here - couldn't find $connectorName, using default
    require_once(__DIR__ . "/../connectors/DefaultConnector.php");
	return new DefaultConnector();
}
?>
<div class="container">
	<div>
		<ol class="breadcrumb">
			<li class="active">Home</li>
		</ol>
	</div>

	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Edit Connector <?php echo $connector->getConnectorName();?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $config->rootDir; ?>/<?php echo $connector->getConnectorName();?>/edit" method="POST">
					<input type="hidden" name="name" value="<?php echo $connector->getConnectorName();?>" />
					<?php
					$data = file_get_contents(__DIR__ . '/../connectors/' . $connector->getConnectorName() . 'Connector.php');
					?>
					<textarea name="data"><?php echo $data;?></textarea>
					<input type="submit" value="Update" />
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="<?php echo $config->rootDir; ?>/<?php echo $connector->getConnectorName();?>" class="btn btn-default btn-block">Back to Connector</a>
				<a href="<?php echo $config->rootDir; ?>/<?php echo $connector->getConnectorName() . "/test";?>" class="btn btn-default btn-block">Run Tests</a>
			</div>
		</div>
	</div>
</div>