<?php
class DefaultConnector extends Connector {
	public function validate() {
		return true;
	}
	public function internalToExternalAction() {
		return "internal-to-external not implemented for connector";
	}
	public function setSessionData() {}
	protected function setTwoStepCaptureRequest() {}
	protected function isDBTwoStep() {return false;}
	protected function isCPOnline() {return true;}
	protected function isRFOnline() {return true;}

	public function getConnectorName() {
		return "Default";
	}
	public function getPaymentTypes() {
		$arr = array();
		return $arr;
	}
	public function getWorkflow() {
		$arr = array();
		return $arr;
	}
	public function getTransmission() {
		$arr = array();
		$arr["type"] = "http";
		$arr["authType"] = "none";
		$arr["primaryEndpoint"] = "";
		return $arr;
	}
	protected function getUrlId($paymentType) {}
	public function getRequestMapping() {
		$arr = array();
		$arr["mappingType"] = "";
		$arr["callbackUrl"] = array("to"=>"", "for"=>"PA,DB");
		$arr["hardcodedParams"] = array("params"=>array(), "for"=>"PA,DB");
		$arr["Request.Transaction.Identification.ShortID"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Identification.OrderID"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Identification.UUID"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.MerchantAccount.type"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.MerchantAccount.MerchantID"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.MerchantAccount.MerchantName"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.MerchantAccount.Password"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.MerchantAccount.Country"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.type"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.Amount"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.Currency"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Payment.Descriptor"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.CreditCardAccount.Holder"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.CreditCardAccount.Verification"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.CreditCardAccount.Brand"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.CreditCardAccount.Number"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.CreditCardAccount.Expiry.month"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.CreditCardAccount.Expiry.year"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Name.Family"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Name.Given"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Contact.Ip"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Contact.Email"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Contact.Phone"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Contact.Mobile"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.Country"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.City"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.State"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.Street"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Customer.Address.Zip"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Authentication.Eci"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Authentication.Verification"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
		$arr["Request.Transaction.Authentication.Xid"] = array("to"=>"", "for"=>"PA,CP,DB,RV,RF");
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
}
?>