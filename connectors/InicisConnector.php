<?php
class InicisConnector extends Connector {
	public function validate() {
		return true;
	}
	public function internalToExternalAction() {
		if (isset($_GET["action"])) {
			switch($_GET["action"]) {
				case "success": {
					//set return code to success
					//$this->callbackNotification();
					//or set type to shopperReturn and 'handleShopperReturn'
					ob_start();
					header('Location: ' . $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/?result=success");
					return ob_get_clean();
				}
				case "cancel" : {
					//set return code to cancel
					//$this->callbackNotification();
					ob_start();
					header('Location: ' . $this->envconfig->siteProtocol . $this->envconfig->siteHost . "/?result=cancel");
					return ob_get_clean();
				}
			}
		}
		else {
			return "internal-to-external not implemented for connector";
		}
	}
	public function setSessionData() {
		$_SESSION["mid"] = "INIpayTest";;
		$_SESSION["signKey"] = "SU5JTElURV9UUklQTEVERVNfS0VZU1RS";
		$_SESSION["price"] = $this->request["Request.Transaction.Payment.Amount"];
		$_SESSION["cardNoInterestQuota"] = "11-2:3:,34-5:12,14-6:12:24,12-12:36,06-9:12,01-3:4";
		$_SESSION["cardQuotaBase"] = "2:3:4:5:6:11:12:24:36";
	}
	protected function setTwoStepCaptureRequest() {}
	protected function isDBTwoStep() {return false;}
	protected function isCPOnline() {return true;}
	protected function isRFOnline() {return true;}

	public function getConnectorName() {
		return "Inicis";
	}
	public function getPaymentTypes() {
		$arr = array();
		$arr[] = "PA";
		return $arr;
	}
	public function getWorkflow() {
		$arr = array();
		$arr["PA"] = Workflow::AlwaysRedirectThenExternal;
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
		$arr["mappingType"] = "none";
		return $arr;
	}
	public function getResponseMapping() {
		$arr = array();
		$arr["mappingType"] = "none";
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