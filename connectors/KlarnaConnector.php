<?php
class KlarnaConnector extends Connector {
	public function validate() {
		return true;
	}
	public function internalToExternalAction() {
		if (isset($_GET["token"])) {
			//TODO: error handling
			$resAuth = $this->doAuth($_GET["token"]);
			$redirectUrl = $resAuth["redirect_url"];
			if (!isset($redirectUrl)) {
				ob_start();
				print_r($resAuth);
				return ob_get_clean();
			}
			ob_start();
			header('Location: ' . $redirectUrl);
			exit();
			return ob_get_clean();
		}
		else {
			$resCap = $this->doCapture($_SESSION["order_id"]);
			ob_start();

			if ($resCap == true) {
				//TODO: update 'ctpe'
				header('Location: ' . $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/?result=success");
				exit();
			}
			else {
				echo "error:";
				print_r($resCap);
			}
			return ob_get_clean();
		}
	}
	public function setSessionData() {
		$clientToken = $this->response["body"]["Response.Transaction.Processing.ConnectorReceived.Body.ClientToken"];
		$SessionId = $this->response["body"]["Response.Transaction.Processing.ConnectorReceived.Body.SessionId"];
		if (isset($clientToken)) {
			$_SESSION["client_token"] = $clientToken;
		}
		if (isset($SessionId)) {
			$_SESSION["session_id"] = $SessionId;
		}
	}
	public function getConnectorName() {
		return "Klarna";
	}
	public function getPaymentTypes() {
		$arr = array();
		$arr[] = "PA";
		return $arr;
	}
	public function getWorkflow() {
		$arr = array();
		$arr["PA"] = Workflow::RequestAndAlwaysRedirectThenExternal;
		$arr["DB"] = Workflow::Normal;
		return $arr;
	}
	public function getTransmission() {
		$arr = array();
		$arr["type"] = "http";
		$arr["authType"] = "basic";
		//TODO: this should not be here/hardcoded - it should be sent from merchant account field
		$arr["authUsername"] = "K500553";
		$arr["authPassword"] = "oi/f7woh^Bang0xe";
		$arr["primaryEndpoint"] = "https://api.playground.klarna.com/credit/v1/sessions";
		$arr["headers"] = array("Content-Type"=>"application/json", "Content-Length"=>"x");
		// $http->addHeader("Content-Type", "application/json");
		// 	        $http->addHeader("Content-Length", strlen($payload));
		return $arr;
	}

	public function getRequestMapping() {
		$arr = array();
		$arr["mappingType"] = "json";
		$arr["hardcodedParams"] = array("params"=>array(
			"purchase_country"=>"GB",
			"locale" => "en-gb",
			"order_tax_amount" => "0",
		), "for"=>"PA,DB");
		$arr["callbackUrl"] = array("to"=>"merchant_urls.confirmation", "for"=>"PA,CP,DB,RV,RF");
		$arr["notificationUrl"] = array("to"=>"merchant_urls.notification", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.Amount"] = array("to"=>"order_amount", "for"=>"PA,CP,DB");
		$arr["Request.Transaction.Payment.Currency"] = array("to"=>"purchase_currency", "for"=>"PA,CP,DB");
		$arr["Request.Transaction.Customer.Name.Birthdate"] = array("to"=>"customer.date_of_birth", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Name.Sex"] = array("to"=>"customer.gender", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Name.Title"] = array("to"=>"billing_address.title", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Name.Family"] = array("to"=>"billing_address.family_name", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Name.Given"] = array("to"=>"billing_address.given_name", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Contact.Email"] = array("to"=>"billing_address.email", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Contact.Phone"] = array("to"=>"billing_address.phone", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Address.Country"] = array("to"=>"billing_address.country", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.City"] = array("to"=>"billing_address.city", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.State"] = array("to"=>"billing_address.region", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.Street"] = array("to"=>"billing_address.street_address", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.Street2"] = array("to"=>"billing_address.street_address2", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.Zip"] = array("to"=>"billing_address.postal_code", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Parameters.Parameter.OPP_cart.items[n].type"] = array("to"=>"order_lines.{n}.type", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Parameters.Parameter.OPP_cart.items[n].quantity"] = array("to"=>"order_lines.{n}.quantity", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Parameters.Parameter.OPP_cart.items[n].price"] = array("to"=>"order_lines.{n}.total_amount", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Parameters.Parameter.OPP_cart.items[n].unit_price"] = array("to"=>"order_lines.{n}.unit_price", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Parameters.Parameter.OPP_cart.items[n].description"] = array("to"=>"order_lines.{n}.description", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Parameters.Parameter.OPP_cart.items[n].name"] = array("to"=>"order_lines.{n}.name", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Identification.ShortID"] = array("to"=>"merchant_reference1", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Identification.OrderID"] = array("to"=>"merchant_reference1", "for"=>"PA,CP,DB,RV,RF");

		return $arr;
	}
	public function getResponseMapping() {
		$arr = array();
		$arr["mappingType"] = "json";
		$arr["xml.version"] = "";
		$arr["xml.encoding"] = "";
		$arr["Response.version"] = "";
		$arr["Response.Transaction.mode"] = "";
		$arr["Response.Transaction.response"] = "";
		$arr["Response.Transaction.Identification.ShortID"] = "";
		$arr["Response.Transaction.Identification.UUID"] = "";
		$arr["Response.Transaction.Processing.requestTimestamp"] = "";
		$arr["Response.Transaction.Processing.responseTimestamp"] = "";
		$arr["Response.Transaction.Processing.payPipeProcessingTime"] = "";
		$arr["Response.Transaction.Processing.connectorTime"] = "";
		$arr["Response.Transaction.Processing.Return.code"] = "";
		$arr["Response.Transaction.Processing.Return"] = "";
		$arr["Response.Transaction.Processing.ConnectorTxID1"] = "";
		$arr["Response.Transaction.Processing.ConnectorSent.Url"] = "";
		$arr["Response.Transaction.Processing.ConnectorSent.Body"] = "";
		$arr["Response.Transaction.Processing.ConnectorReceived.timestamp"] = "";
		$arr["Response.Transaction.Processing.ConnectorReceived.Returned.code"] = "";
		$arr["Response.Transaction.Processing.ConnectorReceived.Returned"] = "";
		$arr["Response.Transaction.Processing.ConnectorReceived.Body"] = "";
		$arr["Response.Transaction.Processing.ConnectorReceived.Body.SessionId"] = "session_id";
		$arr["Response.Transaction.Processing.ConnectorReceived.Body.ClientToken"] = "client_token";
		return $arr;
	}
	public function getResponseCodeMap() {
		$arr = array();
		return $arr;
	}
	public function getFeatures() {
		$arr = array();
		return $arr;
	}
	public function getCurrency() {
		$arr = array();
		$arr[] = "ALL";
		return $arr;
	}
	public function getMerchantAccount() {
		$arr = array();
		return $arr;
	}
	//TEMP:
	private function doAuth($authToken) {

		//TODO: standardise through workflow1 ?? or at least have less duplicate code for building the transmission
		//maybe the constructor for transmission can take $this ?
		$_SESSION["workflow"] = "internal-to-external";
		$this->request["callbackUrl"] = $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/callback.php?connector=" . $this->getConnectorName() . "&handleAction=true&type=internal-to-external&ndc=" . session_id();
		//this->request was array merged with callback request and session request, meaning it includes the origianl PA data
		$this->paymentType = "PA";
		$payload = $this->createPayload();
		$this->payload = $payload;
		$url = "https://api.playground.klarna.com/credit/v1/authorizations/" . $authToken . "/order";
		$http = new HttpTransmission($url, $payload);
		$username="K500553";
		$password="oi/f7woh^Bang0xe";
		$http->addBasicAuth($username, $password);
		$http->addHeader("Content-Type", "application/json");
	    $http->addHeader("Content-Length", strlen($payload));
		$success = $http->transmit($this->response);
		if (!$success) {
			$this->errors[] = $this->response;
			$this->errors[] = $payload;
			//return $this->getResponse();	
		}
		$jsonResult = json_decode($this->response["body"], true);
		$_SESSION["order_id"] = $jsonResult["order_id"];
		//$_SESSION["result"] = $jsonResult;
		return $jsonResult;
	}
	private function doCapture($orderId) {
		$data = array(
			"captured_amount" => 100,
		);
		$payload = json_encode($data);
		$this->payload = $payload;
		$url = "https://api.playground.klarna.com/ordermanagement/v1/orders/" . $orderId . "/captures";
		$http = new HttpTransmission($url, $payload);
		$username="K500553";
		$password="oi/f7woh^Bang0xe";
		$http->addBasicAuth($username, $password);
		$http->addHeader("Content-Type", "application/json");
	    $http->addHeader("Content-Length", strlen($payload));
		$success = $http->transmit($this->response);
		if (!$success) {
			$this->errors[] = $this->response;
			$this->errors[] = $payload;
			//return $this->getResponse();
			return false;
		}

		// $jsonResult = json_decode($this->response, true);
		// $_SESSION["order_id"] = $jsonResult->order_id;
		return $success;
	}
}
?>