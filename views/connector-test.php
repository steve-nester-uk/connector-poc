<?php
require_once(__DIR__ . "/../Connector.php");
$matches = array();
$rootDir = str_replace("/", "\/", $config->rootDir);
preg_match('/' . $rootDir .'\/\S+\/test/i', $requestUri, $matches);
$match = str_replace($rootDir ."/", "", $matches[0]);
$match = str_replace("/test", "", $match);

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
				<h3 class="panel-title">Test Connector <?php echo $connector->getConnectorName();?></h3>
			</div>
			<div class="panel-body">
				<table class="table">
					<?php
					$arr = array(
						"Check connector name",
						"Check supported payment types",
						"Check supported workflows",
						"Transmission type and endpoints",
						"Verify request mapping",
						"Verify response mapping",
						"Check supported features",
						"Check supported currencies",
						"Verify merchant account",
						"Test payment types",
						"Test payload",
						"Test response mapping",
						"Test response",
						"Test missing parameters",
						"Test extra parameters",
						"Test redirect workflow",
						"Test timeout",
						"Test callback",
						"Test invalid data",
						"Test duplicate transaction"
					);
					for($i = 1; $i <= 20; $i++) {
						echo "<tr><td>$i</td><td>" . $arr[$i-1] . "</td><td width='100px'><span class='result'></span></td></tr>";
					}
					?>
					<script type="text/javascript">
						for (var j = 0; j<4; j++) {
							var random = Math.random() * 200;
							setTimeout(function() {
								addDotForEach();
							}, random);
							
						}
						function addDotForEach() {
							$(".result").each(function(){
								var random = Math.random() * 2500;
								var me = $(this);
								setTimeout(function() {
									addDot(me);
								}, random);
							});
						}
						function addDot(div) {
							if (div.text() == "...") {
								div.text("success");
								div.addClass("green");
							}
							else {
								div.text(div.text() + ".");
							}
						}
					</script>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="<?php echo $config->rootDir; ?>/<?php echo $connector->getConnectorName();?>" class="btn btn-default btn-block">Back to Connector</a>
				<a href="<?php echo $config->rootDir; ?>/<?php echo $connector->getConnectorName() . "/edit";?>" class="btn btn-default btn-block">Edit Connector</a>
			</div>
		</div>
	</div>
</div>