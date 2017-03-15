<?php
require_once(__DIR__ . "/transmission/HttpTransmission.php");
require_once(__DIR__ . "/transmission/TcpTransmission.php");
require_once(__DIR__ . "/util/Mapper.php");
require_once(__DIR__ . "/util/Workflow.php");

abstract class Connector {
	protected $payload = "";
	protected $request = array();
	protected $response = array();
	protected $paymentType = "";
	protected $envconfig = array();
	public static $config = array();
	public $errors = array();
//INIT
	public function __construct() {
        $this->setConfig();
        $this->setupSession();
        $this->envconfig = include(__DIR__ . "/config.php");
    }
    private function setupSession() {
    	if (session_id() === "") {
			if (!empty($_GET["ndc"])) {
				session_id($_GET["ndc"]);	
			}
			else {
				$ndc = uniqid("ndc-"); //allowed /^[-,a-zA-Z0-9]{1,128}$/
				session_id($ndc);
			}
		    session_start();
		}
    }
	private function setConfig() {
		self::$config["Transmission"]	= $this->getTransmission();
		self::$config["RequestMapping"]	= $this->getRequestMapping();
		self::$config["ResponseMapping"]= $this->getResponseMapping();
		self::$config["Features"]		= $this->getFeatures();
		self::$config["PaymentTypes"]	= $this->getPaymentTypes();
		self::$config["Currency"]		= $this->getCurrency();
		self::$config["MerchantAccount"]= $this->getMerchantAccount();
	}
//ENTRY POINTS
	public function send($request) {
		$this->request = $request;
		if (!$this->preValidate()) {
			return $this->getResponse();
		}
		if (!$this->myValidate()) {
			return $this->getResponse();
		}
		return $this->buildWorkflow();
	}
	public function callback($request) {
		//format: callback.php?connector=next&type=internal-to-external&ndc=" . $sessionId
		//TODO: change format to callback/next/internal-to-external/sessionId ??
		if (isset($_SESSION["request"])) {
			$this->request = array_merge($request, $_SESSION["request"]);
		}
		else {
			$this->request = $request;
		}
		if (!$this->validateCallback()) {
			return $this->getResponse();
		}
		switch($this->request["type"]) {
			case "internal-to-external": {
				return $this->handleInternalToExternal();
			}
			case "shopperReturn": {
				return $this->callbackNotification();
			}
			case "notification": {
				return $this->asyncNotification();
			}
			default: {
				$this->errors[] = $this->request["type"] . " callback is not implemented";
				return $this->getResponse();
			}
		}
	}
//VALIDATE
	private function preValidate() {
		$this->paymentType = $this->request["Request.Transaction.Payment.type"];
		if (!isset($this->paymentType) || empty($this->paymentType)) {
			$this->errors[] = "Payment.type is required"; 
			return false;
		}
		$this->request["workflow"] = $this->myWorkflow();
		if (($this->request["workflow"] == Workflow::AlwaysRedirectThenExternal
			|| $this->request["workflow"] == Workflow::RequestAndAlwaysRedirectThenExternal) 
			&& !method_exists($this, "getCallbackUrl")) {
			$this->errors[] = "getCallbackUrl() needs to be set for connector";
			return false;
		}

		if (($this->request["workflow"] == Workflow::AlwaysRedirectThenExternal 
			|| $this->request["workflow"] == Workflow::RequestAndAlwaysRedirectThenExternal) 
			&& !method_exists($this, "internalToExternalAction")) {
			$this->errors[] = "internalToExternalAction() needs to be set for connector";
			return false;
		}
		return true;
	}
	private function myValidate() {
		if (!isset($this->request) || empty($this->request)) {
			$this->errors[] = "Request is null"; 
			return false;
		}
		if (null === $this->getConnectorName()) {
			$this->errors[] = "ConnectorName is null"; 
			return false;
		}
		$transmission = self::$config["Transmission"];
		if (!isset($transmission) 
			|| (!isset($transmission["primaryEndpoint"])) && !isset($transmission["PAEndpoint"])) {
			$this->errors[] = "Neither primaryEndpoint or PAEndpoint is set";
			return false;
		}
		if (!isset($transmission) && !isset($transmission["type"]) && !empty($transmission["type"])) {
			$this->errors[] = "Transmission type undefined"; 
			return false;
		}
		if (!array_key_exists("mappingType", self::$config["RequestMapping"])) {
			$this->errors[] = "Connector Request Mapping type undefined"; 
			return false;
		}
		if (!array_key_exists("mappingType", self::$config["ResponseMapping"])) {
			$this->errors[] = "Connector Response Mapping type undefined"; 
			return false;
		}
		if (isset(self::$config["RequestMapping"]["callbackUrl"])) {
			if (!method_exists($this, "getCallbackUrl")) {
				$this->errors[] = "getCallbackUrl() needs to be set for connector";
				return false;
			}
			$redirectType = "shopperReturn";
			$this->request["callbackUrl"] = $this->getCallbackUrl($redirectType, session_id());
		}
		$paymentTypes = $this->getPaymentTypes();
		if (!in_array($this->paymentType, $paymentTypes)) {
			$this->errors[] = "paymentType " . $this->paymentType ." is not supported";
			return false;
		}
		foreach($paymentTypes as $pt) {
			if (!array_key_exists($pt, $this->getWorkflow())) {
				$this->errors[] = "paymentType " . $pt ." is supported, but the workflow is not defined.";
				$this->errors[] = $this->getWorkflow();
				return false;
			}
		}
		

		if (!array_key_exists("Request.Transaction.Payment.Currency", $this->request)) {
			$this->errors[] = "currency is required";
			return false;
		}
		$curr = $this->request["Request.Transaction.Payment.Currency"];
		$currencies = $this->getCurrency();
		if ($currencies[0] != "ALL") {
			if (!in_array($curr, $currencies)) {
				$this->errors[] = "currency " . $curr ." is not supported";
				return false;
			}
		}
		//TODO: validation for
		// public abstract function getFeatures();
		// public abstract function getMerchantAccount();
		return $this->validate();
	}
	private function validateCallback() {
		if (!isset($this->request["ndc"])) {
			$this->errors[] = "ndc is not set";
			return false;
		}
		if (!isset($this->request["connector"])) {
			$this->errors[] = "connector is not set";
			return false;
		}
		if (!isset($this->request["type"])) {
			$this->errors[] = "type is not set";
			return false;
		}
		return true;
	}
//WORKFLOW
	private function buildWorkflow() {
		//workflow 1: send request and parse response (which could include a redirect) (3d CC case)
		//workflow 2: send request and always redirect to PP with some response data (Klarna case)
		//workflow 3: always redirect to PP (Iyzico case)
		$workflow = $this->request["workflow"];
		switch($workflow) {
			case Workflow::Normal: {
				$twoStep = false;
				if ($this->paymentType == "DB" && $this->isDBTwoStep()) {
					$twoStep = true;
					$this->paymentType = "PA";
					//a hack
					$this->request["Request.Transaction.Payment.type"] = "PA";
				}
				$this->handleWorkflow1();
				if ($twoStep) {
					$this->handleTwoStepCapture();
				}
				break;
			}
			case Workflow::RequestAndAlwaysRedirectThenExternal: {
				//TODO: should this be somewhere else (it is used to retrieve the request data for internal-to-external)
				$_SESSION["request"] = $this->request;
				$this->handleWorkflow2();
				break;
			}
			case Workflow::AlwaysRedirectThenExternal: {
				//TODO: should this be somewhere else (it is used to retrieve the request data for internal-to-external)
				$_SESSION["request"] = $this->request;
				$this->handleWorkflow3();
				break;
			}
		}
		return $this->getResponse();
	}
	private function handleWorkflow1() {
		if ($this->paymentType == "CP" && !$this->isCPOnline()) {
			$this->sendOffline();
			//TODO: when will response be parsed? does sendOffline need to do it?
			return;
		}
		if ($this->paymentType == "RF" && !$this->isRFOnline()) {
			$this->sendOffline();
			//TODO: when will response be parsed? does sendOffline need to do it?
			return;
		}
		$endpoint = $this->getEndpoint();
		if (count($this->errors) > 0) return;

		$transmission = self::$config["Transmission"];
		$this->payload = $this->createPayload();
		if (count($this->errors) > 0) return;

		if ($transmission["type"] == "http") {
			$http = new HttpTransmission($endpoint, $this->payload);
			//authentication type
			if (isset($transmission["authType"]) && $transmission["authType"] == "basic") {
				$http->addBasicAuth($transmission["authUsername"], $transmission["authPassword"]);
			}
			//headers
			if (isset($transmission["headers"])) {
				foreach($transmission["headers"] as $key => $val) {
					if ($key == "Content-Length") {
						$http->addHeader("Content-Length", strlen($this->payload));
					}
					else {
						$http->addHeader($key, $val);
					}
				}
			}
			$success = $http->transmit($this->response);

			if (!$success) {
				$this->errors[] = "http code was not 200";
				return;	
			}
			
		}
		else if ($transmission["type"] == "tcp") {
			$http = new TcpTransmission($endpoint, $this->payload);
			//TODO: enable, make sure urls and ip:ports can be handled
			//$success = $http->transmit($this->response);
			$success = false;
			if (!$success) {
				$this->errors[] = "tcp not supported";
				return;	
			}
		}
		else {
			$this->errors[] = "Unknown transmission type " . $transmission["type"];
		}
		$this->parseResponse();
	}
	private function handleWorkflow2() {
		$this->handleWorkflow1();

		if (!isset($this->response["body"]["Response.Transaction.Processing.Redirect.url"])) {
			//workflows 2-2 / 2-3: send request and always redirect to PP with some response data (Klarna case)
			//TODO: check if request was successful before redirecting
			if (isset($this->response["body"]["Response.Transaction.Identification.UUID"])) {
				$_SESSION["uuid"] = $this->response["body"]["Response.Transaction.Identification.UUID"];
			}
			$this->setSessionData();
			$this->setRedirectResponse(session_id());
			
		}
	}
	private function handleWorkflow3() {
		//workflows 3-2 / 3-3: always redirect to PP (Iyzico case)
		//todo: add needed data to session
		//for Iyzico case, probably want config details for installments
		//and authentication parameters (config / request details)
		$this->setSessionData();
		$this->setRedirectResponse(session_id());
		$this->parseResponse();
	}
	private function handleTwoStepCapture() {
		$this->paymentType = "CP";
		//a hack?
		$this->request["Request.Transaction.Payment.type"] = "CP";
		$this->setTwoStepCaptureRequest(); //sets request data from response
		$this->handleWorkflow1();
	}
	public function myWorkflow() {
		$arr = $this->getWorkflow();
		if (array_key_exists($this->paymentType, $arr)) {
			return $arr[$this->paymentType];
		}
		else {
			return Workflow::Normal;
		}
	}
//REQUEST
	private function getEndpoint() {
		$transmission = &self::$config["Transmission"];
		if (isset($this->request["mode"]) && $this->request["mode"] == "echo") {
			//would need to log this
			$transmission["type"] = "http";
			$transmission["primaryEndpoint"] = $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/echo";
		}
		$endpoint = "";
		if (isset($transmission["primaryEndpoint"])) {
			$endpoint = $transmission["primaryEndpoint"];
		}
		else {
			switch($this->paymentType) {
				case "PA": {
					$endpoint = $transmission["PAEndpoint"];
					break;
				}
				case "CP": {
					$endpoint = $transmission["CPEndpoint"];
					break;
				}
				case "DB": {
					$endpoint = $transmission["DBEndpoint"];
					break;
				}
				case "RV": {
					$endpoint = $transmission["RVEndpoint"];
					break;
				}
				case "RF": {
					$endpoint = $transmission["RFEndpoint"];
					break;
				}
				default: {
					$this->errors[] = "configuation error, unable to find Endpoint for paymentType " . $this->paymentType;
					return null;
				}
			}
			if (strpos($endpoint, '{id}') !== false) {
				if (!method_exists($this, "getUrlId")) {
					$this->errors[] = "getUrlId() needs to be set for connector";
					return null;
				}
				$id = $this->getUrlId($this->paymentType);
			    $endpoint = str_replace('{id}', $id, $endpoint);
			}
		}
		return $endpoint;
	}
	protected function createPayload() {
		$mappingType = self::$config["RequestMapping"]["mappingType"];

		$mapper = new Mapper($this->errors);
		switch($mappingType) {
			case "postparams": {
				return $mapper->createPostParams($this->request, self::$config["RequestMapping"], $this->paymentType, $this->errors);
			}
			case "json": {
				return $mapper->createJson($this->request, self::$config["RequestMapping"], $this->paymentType, $this->errors);
			}
			default: {
				$this->errors[] = "Unknown mapping type " . $mappingType;
				return null;
			}
		}		
	}
	private function sendOffline() {
		//TODO: sendOffline
	}
//CALLBACK
	protected function handleInternalToExternal() {
		//TODO: would need to handle session expired etc
		//TODO: would probably want security to ensure request is coming from the callback.php page etc
		//TODO: when/how to destroy the session?
		//session_destroy();
		//internal-to-external
		$request = $this->request;
		if (isset($this->request["handleAction"])) {
			return $this->internalToExternalAction();
		}
		else {
			$_SESSION["workflow"] = "internal-to-external";
			$conCallbackFile = __DIR__ . "/callbacks/" . $this->getConnectorName() . "Callback.php";
			if (file_exists($conCallbackFile)) {
				ob_start();
				include_once($conCallbackFile);
				return ob_get_clean();
			}
			else {
				return "callback is not implemented for connector";
			}
		}
	}
	private function getCallbackUrl($type, $sessionId="") {
		if ($sessionId == "") $sessionId = session_id();
		$connectorName = $this->getConnectorName();
		$url = "";
		switch ($type) {
			case "internal-to-external": {
				$url = $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/callback.php?connector=" . $connectorName . "&type=internal-to-external&ndc=" . $sessionId;
				break;
			}
			case "shopperReturn": {
				$url = $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/callback.php?connector=" . $connectorName . "&type=shopperReturn&ndc=" . $sessionId;
				break;
			}
			case "notification": {
				$url = $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/callback.php?connector=" . $connectorName . "&type=notification&ndc=" . $sessionId;
				break;
			}
			default: {
				$url = $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/callback.php?connector=" . $connectorName . "&type=default&ndc=" . $sessionId;
				break;
			}
		}
		//TODO
		return str_replace("&","%26",$url);
		//return urlencode($url);
	}
	private function asyncNotification() {
		//send async notification to CTPE (nobrowsersession=true)
		//TODO: async notification response - 200 OK ? or something more?
		//TODO: an easy way to simulate async notification callback
		return "async notification is currently not implemented!";
	}
	private function callbackNotification() {
		//send callback notification to CTPE (nobrowsersession=false) + redirect

		//TODO: parse data - return code etc
		//TODO: get shopper result url
		//handleShopperReturn
		$url = $this->envconfig->siteProtocol . $this->envconfig->siteHost;
		ob_start();
		header('Location: ' . $url);
		return ob_get_clean();
	}
//RESPONSE
	private function setRedirectResponse($sessionId="") {
		if ($sessionId == "") $sessionId = session_id();

		$workflow = $this->request["workflow"];
		if ($workflow == Workflow::RequestAndAlwaysRedirectThenExternal || $workflow == Workflow::AlwaysRedirectThenExternal) {
			$redirectType = "internal-to-external";	
		}
		$response = array(
			"Response.Transaction.Processing.Return.code" => "000.200.000",
			"Response.Transaction.Processing.Return" => "transaction pending",
			"Response.Transaction.Processing.Redirect.url" => urldecode($this->getCallbackUrl($redirectType, $sessionId)),
			"Response.Transaction.Processing.Redirect.Parameters" => array("OrderID"=>"abc")
		);
		if (isset($this->response["body"]) && is_array($this->response["body"])) {
			$this->response["body"] = array_merge($this->response["body"], $response);
		}
		else {
			$this->response["body"] = $response;
		}
	}
	protected function responseCodeMapping() {
		$map = $this->getResponseCodeMap();
		//TODO: do this in validation?
		if (!isset($map) || !is_array($map)) {
			$this->errors[] = "Response Code Map is not set";
			return;
		}
		//TODO: what if the code is set based on HTTP response codes?
		$responseCode = "";
		if (isset($this->response["body"]["Response.Transaction.Processing.Return.code"])) {
			$responseCode = $this->response["body"]["Response.Transaction.Processing.Return.code"];
		}
		else if (isset($this->response["httpResponseCode"])) {
			$responseCode = $this->response["httpResponseCode"];
		}
		else {
			$this->errors[] = "cannot find http response code";
		}
		if (array_key_exists($responseCode, $map)) {
			$this->response["body"]["Response.Transaction.Processing.Return.code"] = $map[$responseCode];
			return;
		}
	}
	private function parseResponse() {
		$mapper = new Mapper();
		$mappingType = self::$config["ResponseMapping"]["mappingType"];
		if ($this->request["workflow"] == Workflow::AlwaysRedirectThenExternal) {
			$mappingType = "noparse";
		}
		if (isset($this->request["mode"]) && $this->request["mode"] == "echo") {
			$mappingType = "echo";
		}
		switch($mappingType) {
			case "echo": {
				$mapper->parseEchoResponse($this->response, self::$config["ResponseMapping"], $this->errors);
				break;
			}
			case "json": {
				$mapper->parseJsonResponse($this->response, self::$config["ResponseMapping"], $this->errors);
				break;
			}
			case "noparse": {
				break;
			}
			default: {
				$this->errors[] = "Unknown mapping type " . $mappingType;
				return;
			}
		}
		//response code mapping
		$this->responseCodeMapping();
	}
	protected function getResponse() {
    	$res = array();
    	if (count($this->errors) > 0) {
    		//$res["request"] = $this->request;
    		$res["payload"] = $this->payload;
    		$res["response"] = $this->response;
    		$res["code"] = "999";
    		$res["description"] = "Errors";
    		$res["errors"] = $this->errors;
    	}
    	else {
    		//$res["request"] = $this->request;
    		$res["payload"] = $this->payload;
    		$res["response"] = $this->response["body"];
    		$res["code"] = "000";
    		$res["description"] = "";
    		$res["errors"] = array();
    	}
    	return json_encode($res);
    }
//LOCAL
	protected abstract function validate();
	//can be overridden:
	//TODO: add to PIA UI
	protected function setSessionData() {}
	protected function setTwoStepCaptureRequest() {}
	protected function isDBTwoStep() {return false;}
	protected function isCPOnline() {return true;}
	protected function isRFOnline() {return true;}
//PIA
	public abstract function getConnectorName();
	public abstract function getWorkflow();
	public abstract function getTransmission();
	public abstract function getRequestMapping();
	public abstract function getResponseMapping();
	public abstract function getResponseCodeMap();
	public abstract function getFeatures();
	public abstract function getPaymentTypes();
	public abstract function getCurrency();
	public abstract function getMerchantAccount();
}
?>