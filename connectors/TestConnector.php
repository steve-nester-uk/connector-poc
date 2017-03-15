<?php
class TestConnector extends Connector {
	public function validate() {
		return true;
	}
	public function getConnectorName() {
		return "Test";
	}
	public function getWorkflow() {
		$arr = array();
		return $arr;
	}
	public function getTransmission() {
		$arr = array();
		$arr["type"] = "tcp";
		$arr["primaryEndpoint"] = "localhost:80";
		return $arr;
	}
	public function getRequestMapping() {
		$arr = array();
		$arr["mappingType"] = "postparams";
		$arr["Request.Transaction.Identification.ShortID"] = array("to"=>"merchantTransactionId", "for"=>"PA,CP");
		return $arr;
	}
	public function getResponseMapping() {
		$arr = array();
		$arr["mappingType"] = "json";
		return $arr;
	}
	public function getResponseCodeMap() {
		$arr = array();
		return $arr;
	}
	public function getFeatures() {
		$arr = array();
		$arr[] = "AVS";
		$arr[] = "CVV";
		$arr[] = "3D";
		return $arr;
	}
	public function getPaymentTypes() {
		$arr = array();
		$arr[] = "PA";
		$arr[] = "DB";
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