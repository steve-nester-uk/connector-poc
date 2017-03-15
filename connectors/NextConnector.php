<?php
class NextConnector extends Connector {
	public function validate() {
		if (isset(self::$config["Currency"]) 
			&& isset($this->request["currency"])
			&& !(in_array($this->request["currency"], self::$config["Currency"]))) {
			$this->errors[] = "Currency is not supported";
			return false;
		}
		return true;
	}
	public function internalToExternalAction() {
		return "internal-to-external not yet implemented from connector!";
	}
	protected function setSessionData() {
		//TODO: need to get shopperResultUrl
		$_SESSION["shopperResultUrl"] = $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/index.php";
		//$_SESSION["connectorRedirectUrl"] = "";
	}
	protected function setTwoStepCaptureRequest() {
		if (!isset($this->response["body"])) {
			return;
		}
		$this->request["Request.Transaction.Identification.UUID"] = $this->response["body"]["Response.Transaction.Identification.UUID"];
	}
	protected function isDBTwoStep() {return false;}

	public function getConnectorName() {
		return "Next";
	}
	public function getPaymentTypes() {
		$arr = array();
		$arr[] = "PA";
		$arr[] = "CP";
		$arr[] = "DB";
		$arr[] = "RV";
		$arr[] = "RF";
		return $arr;
	}
	public function getWorkflow() {
		$arr = array();
		//$arr["PA"] = Workflow::AlwaysRedirectThenExternal;
		//$arr["PA"] = Workflow::AlwaysRedirectThenRedirect;
		//$arr["PA"] = Workflow::RequestAndAlwaysRedirectThenExternal;
		//$arr["PA"] = Workflow::RequestAndAlwaysRedirectThenRedirect;
		$arr["PA"] = Workflow::Normal;
		$arr["CP"] = Workflow::Normal;
		$arr["DB"] = Workflow::Normal;
		$arr["RV"] = Workflow::Normal;
		$arr["RF"] = Workflow::Normal;
		return $arr;
	}
	public function getTransmission() {
		$arr = array();
		$arr["type"] = "http";
		$arr["PAEndpoint"] = "https://test.oppwa.com/v1/payments";
		$arr["CPEndpoint"] = "https://test.oppwa.com/v1/payments/{id}";
		$arr["DBEndpoint"] = "https://test.oppwa.com/v1/payments";
		$arr["RVEndpoint"] = "https://test.oppwa.com/v1/payments/{id}";
		$arr["RFEndpoint"] = "https://test.oppwa.com/v1/payments/{id}";
		
		return $arr;
	}
	protected function getUrlId($paymentType) {
		//An id that needs to be injected into the URL in {id}
		//TODO: error checking
		return $this->request["Request.Transaction.Identification.UUID"];
	}
	public function getRequestMapping() {
		$arr = array();
		$arr["mappingType"] = "postparams";
		$arr["callbackUrl"] = array("to"=>"shopperResultUrl", "for"=>"PA,DB");
		// $arr["hardcodedParams"] = array("params"=>array("version"=>"1.0"), "for"=>"DB");
		$arr["Request.Transaction.Identification.OrderID"] = array("to"=>"merchantTransactionId", "for"=>"PA,CP");
		// $arr["Request.Transaction.Identification.ShortID"] = array("to"=>"merchantTransactionId", "for"=>"PA,CP");
		$arr["Request.Transaction.MerchantAccount.MerchantID"] = array("to"=>"authentication.entityId", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.MerchantAccount.MerchantName"] = array("to"=>"authentication.userId", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.MerchantAccount.Password"] = array("to"=>"authentication.password", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.type"] = array("to"=>"paymentType", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.Amount"] = array("to"=>"amount", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.Currency"] = array("to"=>"currency", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.Descriptor"] = array("to"=>"descriptor", "for"=>"PA,DB");
		$arr["Request.Transaction.CreditCardAccount.Holder"] = array("to"=>"card.holder", "for"=>"PA,DB");
		$arr["Request.Transaction.CreditCardAccount.Verification"] = array("to"=>"card.cvv", "for"=>"PA,DB");
		$arr["Request.Transaction.CreditCardAccount.Brand"] = array("to"=>"paymentBrand", "for"=>"PA,DB");
		$arr["Request.Transaction.CreditCardAccount.Number"] = array("to"=>"card.number", "for"=>"PA,DB");
		$arr["Request.Transaction.CreditCardAccount.Expiry.month"] = array("to"=>"card.expiryMonth", "for"=>"PA,DB");
		$arr["Request.Transaction.CreditCardAccount.Expiry.year"] = array("to"=>"card.expiryYear", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Name.Family"] = array("to"=>"customer.surname", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Name.Given"] = array("to"=>"customer.givenName", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Contact.Ip"] = array("to"=>"customer.ip", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Contact.Email"] = array("to"=>"customer.email", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Contact.Phone"] = array("to"=>"customer.phone", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Contact.Mobile"] = array("to"=>"customer.mobile", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Address.Country"] = array("to"=>"billing.country", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Address.City"] = array("to"=>"billing.city", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Address.State"] = array("to"=>"billing.state", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Address.Street"] = array("to"=>"billing.street1", "for"=>"PA,DB");
		$arr["Request.Transaction.Customer.Address.Zip"] = array("to"=>"billing.postcode", "for"=>"PA,DB");
		return $arr;
	}
	public function getResponseMapping() {
		$arr = array();
		$arr["mappingType"] = "json";
		$arr["hardcodedParams"] = array("params"=>array("testMode"=>"EXTERNAL"), "for"=>"DB");
		$arr["Response.Transaction.mode"] = "";
		$arr["Response.Transaction.Response"] = "";
		$arr["Response.Transaction.Identification.ShortID"] = "";
		$arr["Response.Transaction.Identification.UUID"] = "id";
		$arr["Response.Transaction.Identification.OrderID"] = "merchantTransactionId";
		$arr["Response.Transaction.Identification.ShopperID"] = "";
		$arr["Response.Transaction.Processing.requestTimestamp"] = "";
		$arr["Response.Transaction.Processing.responseTimestamp"] = "timestamp";
		$arr["Response.Transaction.Processing.PayPipeProcessingTime"] = "";
		$arr["Response.Transaction.Processing.connectorTime"] = "";
		$arr["Response.Transaction.Processing.ConnectorTxID1"] = "resultDetails.ConnectorTxID1";
		$arr["Response.Transaction.Processing.ConnectorTxID1.description"] = "";
		$arr["Response.Transaction.Processing.ConnectorTxID2"] = "resultDetails.ConnectorTxID2";
		$arr["Response.Transaction.Processing.ConnectorTxID2.description"] = "";
		$arr["Response.Transaction.Processing.ConnectorTxID3"] = "resultDetails.ConnectorTxID3";
		$arr["Response.Transaction.Processing.ConnectorTxID3.description"] = "";
		$arr["Response.Transaction.Processing.Return.code"] = "result.code";
		$arr["Response.Transaction.Processing.Return"] = "result.description";
		$arr["Response.Transaction.Processing.AVSResult"] = "";
		$arr["Response.Transaction.Processing.CVVResult"] = "";
		$arr["Response.Transaction.Processing.Redirect"] = "";
		$arr["Response.Transaction.Processing.Redirect.url"] = "redirect.url";
		$arr["Response.Transaction.Processing.Redirect.Parameters"] = "redirect.parameters"; //a list, maybe need an array, with 'type' list
		$arr["Response.Transaction.Processing.ConnectorDetails"] = "resultDetails";
		// $arr["Response.Transaction.Processing.ConnectorDetails.Result"] = "";
		// $arr["Response.Transaction.Processing.ConnectorDetails.Result.name"] = "";
		$arr["Response.Transaction.Processing.SecurityHash"] = "";
		$arr["Response.Transaction.ConnectorSent"] = "";
		$arr["Response.Transaction.ConnectorReceived"] = "";
		return $arr;
	}
	public function getResponseCodeMap() {
		$arr = array();
		// $arr["000.000.000"] = "abc123";
		// $arr["000.100.110"] = "test456";
		return $arr;
	}
	public function getFeatures() {
		$arr = array();
		$arr[] = "AVS";
		$arr[] = "CVV";
		$arr[] = "3D";
		return $arr;
	}
	public function getCurrency() {
		$arr = array();
		$arr[] = "EUR";
		$arr[] = "GBP";
		return $arr;
	}
	public function getMerchantAccount() {
		$arr = array();
		$arr["field1"] = array("supported"=>"true", "description"=>"my field 1");
		$arr["field2"] = array("supported"=>"true", "description"=>"my field 2");
		return $arr;
	}
}
?>