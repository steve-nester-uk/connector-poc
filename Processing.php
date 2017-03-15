<?php
require_once(__DIR__ . "/Connector.php");
class Processing {
	public function send($request) {
		$connectorName = $request["Request.Transaction.MerchantAccount.type"];
		$connector = $this->getConnector($connectorName);
		if (!isset($connector)) return $this->returnError("Cannot find connector");
		return $connector->send($request);
	}
	public function callback($getArr) {
		$connectorName = $getArr["connector"];
		$connector = $this->getConnector($connectorName);
		if (!isset($connector)) return "connector is null";
		return $connector->callback($getArr);
	}
	private function getConnector($connectorName) {
		if (!isset($connectorName) || empty($connectorName)) return null;
		foreach (glob(__DIR__ . '/connectors/*.php') as $file) {
			$conStr = str_replace(__DIR__  . "/connectors/", "", $file);
			$conStr = str_replace("Connector.php", "", $conStr);
			if (strcasecmp($conStr,$connectorName) == 0) {
		        require_once $file;
				$class = basename($file, '.php');

		        if (class_exists($class)) return new $class();
	    	}
	    }
	}
	private function returnError($msg) {
		$res = array();
		$res["code"] = "999";
		$res["description"] = "Errors";
		$res["errors"] = array($msg);
    	return json_encode($res, true);
	}
}
?>