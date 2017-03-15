<?php
require_once(__DIR__ . "/../Connector.php");
$matches = array();
$rootDir = str_replace("/", "\/", $config->rootDir);
preg_match('/'. $rootDir .'\/\S+/i', $requestUri, $matches);
$match = str_replace($config->rootDir . "/", "", $matches[0]);

$connector = getConnector($match);
if (!isset($connector)) return "connector not found";

function getConnector($connectorName) {
	if (!isset($connectorName) || empty($connectorName)) return null;
	foreach (glob(__DIR__ . '/../connectors/*.php') as $file) {
		$conStr = str_replace(__DIR__  . "/../connectors/", "", $file);
		$conStr = str_replace("Connector.php", "", $conStr);
		$conStr = strtolower($conStr);
		$conStr = ucwords($conStr);
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
				<h3 class="panel-title">Connector <?php echo $connector->getConnectorName();?></h3>
			</div>
			<div class="panel-body">
				<h3>Connector Name</h3>
				<?php print_r($connector->getConnectorName()); ?>
				<h3>Payment Types</h3>
				<?php print_r($connector->getPaymentTypes()); ?>
				<h3>Workflow</h3>
				<?php print_r($connector->getWorkflow()); ?>
				<h3>Transmission</h3>
				<?php print_r($connector->getTransmission()); ?>
				<h3>Request Mapping</h3>
				<?php print_r($connector->getRequestMapping()); ?>
				<h3>Response Mapping</h3>
				<?php print_r($connector->getResponseMapping()); ?>
				<h3>Features</h3>
				<?php print_r($connector->getFeatures()); ?>
				<h3>Currency</h3>
				<?php print_r($connector->getCurrency()); ?>
				<h3>Merchant Account</h3>
				<?php print_r($connector->getMerchantAccount()); ?>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="<?php echo $config->rootDir; ?>/<?php echo $connector->getConnectorName() . "/test";?>" class="btn btn-default btn-block">Run Tests</a>
				<a href="<?php echo $config->rootDir; ?>/<?php echo $connector->getConnectorName() . "/edit";?>" class="btn btn-default btn-block">Edit Connector</a>
			</div>
		</div>
	</div>
</div>