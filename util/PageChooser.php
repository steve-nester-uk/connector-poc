<?php
class PageChooser {
	private $config = array();
	public function __construct() {
        $this->config = include(__DIR__ . "/../config.php");
    }
	public function set($requestUri) {
		$pageTitle = "Default";
		$pageInclude = __DIR__ . "/../views/default.php";

		$rootDir = str_replace("/", "\/", $this->config->rootDir);

		if (preg_match('/' . $rootDir . '\/Add\/?/i', $requestUri)) {
		    $pageTitle = "Add Connector";
			$pageInclude = "views/connector-add.php";
		}
		else if (preg_match('/' . $rootDir . '\/docs\/workflows/i', $requestUri)) {
		    $pageTitle = "Workflows";
		    $pageInclude = "views/docs/workflows.php";
		}
		else if (preg_match('/' . $rootDir . '\/\S+\/test/i', $requestUri)) {
			$matches = array();
		    preg_match('/' . $rootDir . '\/\S+\/test/i', $requestUri, $matches);
		    $match = str_replace($rootDir . "/", "", $matches[0]);
		    $match = str_replace("/test", "", $match);
		    $pageTitle = $match;
		    $pageInclude = "views/connector-test.php";
		}
		else if (preg_match('/' . $rootDir . '\/\S+\/edit/i', $requestUri)) {
			$matches = array();
		    preg_match('/' . $rootDir . '\/\S+\/edit/i', $requestUri, $matches);
		    $match = str_replace($rootDir . "/", "", $matches[0]);
		    $match = str_replace("/edit", "", $match);
		    $pageTitle = $match;
		    $pageInclude = "views/connector-edit.php";
		}
		else if (preg_match('/' . $rootDir . '\/\S+/i', $requestUri)) {
			$matches = array();
		    preg_match('/' . $rootDir . '\/\S+/i', $requestUri, $matches);
		    $match = str_replace($rootDir . "/", "", $matches[0]);
		    $pageTitle = $match;
		    $pageInclude = "views/connector.php";
		}
		else if (preg_match('/\/echo\/?/i', $requestUri)) {
		    $pageTitle = "Echo";
			$pageInclude = "views/echo.php";
		}
		// else if (preg_match('/\/logout/i', $requestUri)) {
		//     //doLogout();
		//     echo "would do logout and redirect";
		// }
		return array("title"=>$pageTitle, "include"=>$pageInclude);
	}
}
?>