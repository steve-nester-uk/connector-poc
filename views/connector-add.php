<?php
if (isset($_POST["name"])) {
	function buildDefault($name, $conArr) {
		$conFile = '<?php
class ' . $name . 'Connector extends Connector {
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
		return "' . $name . '";
	}
	public function getPaymentTypes() {
		$arr = array();
		return $arr;
	}
	public function getWorkflow() {
		$arr = array();
		' . $conArr["paymentTypes"] . ' 
		return $arr;
	}
	public function getTransmission() {
		$arr = array();
		' . $conArr["transmission"] . '
		return $arr;
	}
	public function getRequestMapping() {
		$arr = array();
		' . $conArr["requestMapping"] . '
		return $arr;
	}
	public function getResponseMapping() {
		$arr = array();
		' . $conArr["responseMapping"] . '
		return $arr;
	}
	public function getResponseCodeMap() {
		$arr = array();
		' . $conArr["responseCodeMap"] . '
		return $arr;
	}
	public function getFeatures() {
		$arr = array();
		' . $conArr["features"] . '
		return $arr;
	}
	public function getCurrency() {
		$arr = array();
		' . $conArr["currency"] . '
		return $arr;
	}
	public function getMerchantAccount() {
		$arr = array();
		' . $conArr["merchantAccountMapping"] . '
		return $arr;
	}
}
?>';
		return $conFile;
	}
	function getConnector($connectorName) {
		require_once(__DIR__ . "/../Connector.php");
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
	    return null;
	}
	function createConnector($name, $conArr) {
		//check connector doesn't already exist
		if (getConnector($name) != null) {
			echo "Error: Cannot create this connector as it already exists";
			return;
		}
		//create connector
		$name = strtolower($name);
		$name = ucwords($name);
		$filename = $name . "Connector.php";
		$fullpath = __DIR__ . "/../connectors/" . $filename;

		$fh = fopen($fullpath, "w");
	    if (!is_resource($fh)) {
	        return false;
	    }
	    // foreach ($config as $key => $value) {
	    //     fwrite($fh, sprintf("%s = %s\n", $key, $value));
	    // }
	    fwrite($fh, sprintf(buildDefault($name, $conArr)));
	    fclose($fh);

	    return true;
	}
	//create connector
	$name = $_POST["name"];

	$paymentTypes = "";
	$transmission = "";
	$requestMapping = "";
	$responseMapping = "";
	$responseCodeMap = "";
	$features = "";
	$currency = "";
	$merchantAccountMapping = "";

	//PAYMENT TYPES

	if ($_POST["paWorkflow"] != "NotSupported") {
		$paymentTypes .= "\$arr[] = \"PA\";\n";
	}
	if ($_POST["cpWorkflow"] != "NotSupported") {
		$paymentTypes .= "\$arr[] = \"CP\";\n";
	}
	if ($_POST["dbWorkflow"] != "NotSupported") {
		$paymentTypes .= "\$arr[] = \"DB\";";
	}
	if ($_POST["rvWorkflow"] != "NotSupported") {
		$paymentTypes .= "\$arr[] = \"RV\";\n";
	}
	if ($_POST["rfWorkflow"] != "NotSupported") {
		$paymentTypes .= "\$arr[] = \"RF\";\n";
	}
	//TRANSMISSION
	$transmission .= "\$arr[\"type\"] = \"" . $_POST["transmissionType"] . "\";\n";

	if (isset($_POST["isPrimaryEndpoint"]) && $_POST["isPrimaryEndpoint"] == "on") {
		$transmission .= "\$arr[\"primaryEndpoint\"] = \"" . $_POST["primaryEndpoint"] . "\";\n";
	}
	if (isset($_POST["isSecondaryEndpoint"]) && $_POST["isSecondaryEndpoint"] == "on") {
		$transmission .= "\$arr[\"secondaryEndpoint\"] = \"" . $_POST["secondaryEndpoint"] . "\";\n";
	}
	if (isset($_POST["isPAEndpoint"]) && $_POST["isPAEndpoint"] == "on") {
		$transmission .= "\$arr[\"PAEndpoint\"] = \"" . $_POST["paEndpoint"] . "\";\n";
	}
	if (isset($_POST["isCPEndpoint"]) && $_POST["isCPEndpoint"] == "on") {
		$transmission .= "\$arr[\"CPEndpoint\"] = \"" . $_POST["cpEndpoint"] . "\";\n";
	}
	if (isset($_POST["isDBEndpoint"]) && $_POST["isDBEndpoint"] == "on") {
		$transmission .= "\$arr[\"DBEndpoint\"] = \"" . $_POST["dbEndpoint"] . "\";\n";
	}
	if (isset($_POST["isRVEndpoint"]) && $_POST["isRVEndpoint"] == "on") {
		$transmission .= "\$arr[\"RVEndpoint\"] = \"" . $_POST["rvEndpoint"] . "\";\n";
	}
	if (isset($_POST["isRFEndpoint"]) && $_POST["isRFEndpoint"] == "on") {
		$transmission .= "\$arr[\"RFEndpoint\"] = \"" . $_POST["rfEndpoint"] . "\";\n";
	}

	//REQUEST MAPPING
	$requestMapping .= "\$arr[\"mappingType\"] = \"" . $_POST["requestType"] . "\";\n";

	for ($i = 0; $i < count($_POST["requestName"]); $i++) {
		$rn = $_POST["requestName"][$i];
		$rv = $_POST["requestVal"][$i];
		
		$for = "PA,CP,DB,RV,RF";
		if (isset($_POST["requestFor"]) && isset($_POST["requestFor"][$i])) {
			$for = $_POST["requestFor"][$i];	
		}
		$requestMapping .= "\$arr[\"$rn\"] = array(\"to\"=>\"$rv\", \"for\"=>\"". $for . "\");\n";
	}

	//RESPONSE MAPPING
	$responseMapping .= "\$arr[\"mappingType\"] = \"" . $_POST["responseType"] . "\";\n";

	for ($i = 0; $i < count($_POST["responseName"]); $i++) {
		$rn = $_POST["responseName"][$i];
		$rv = $_POST["responseVal"][$i];
		$responseMapping .= "\$arr[\"$rn\"] = \"$rv\";\n";
	}

	//responseCodeMap
	for ($i = 0; $i < count($_POST["responseCodeName"]); $i++) {
		$rn = $_POST["responseCodeName"][$i];
		$rv = $_POST["responseCodeVal"][$i];
		$responseCodeMap .= "\$arr[\"$rn\"] = \"$rv\";\n";
	}
	
	//features
	$features .= "\$arr[] = \"" . $_POST["features"] . "\";\n";
	
	//currency
	$currency .= "\$arr[] = \"" . $_POST["currency"] . "\";\n";
	
	//merchantAccountMapping
	$merchantAccountMapping .= "\$arr[] = \"" . $_POST["merchantAccount"] . "\";\n";

	$conArr = array(
		"paymentTypes" => $paymentTypes, 
		"transmission" => $transmission,
		"requestMapping" => $requestMapping,
		"responseMapping" => $responseMapping,
		"responseCodeMap" => $responseCodeMap,
		"features" => $features,
		"currency" => $currency,
		"merchantAccountMapping" => $merchantAccountMapping
	);

	if (createConnector($name, $conArr)) {
		echo "created file";
		//redirect to reload /rootDir/$name
		header('Location: ' . $config->rootDir . '/' . $name);
		exit();
	}

	die();
}
?>
<div class="container">
	<div>
		<ol class="breadcrumb">
			<li class="active">Home</li>
		</ol>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Create New Connector</h3>
		</div>
		<div class="panel-body">
			<form class="form-horizontal" method="POST" action="<?php echo $config->rootDir;?>/Add">
			  <div class="form-group">
			    <label for="inputName" class="col-sm-2 control-label">Name</label>
			    <div class="col-sm-10">
			      <input type="text" class="form-control" name="name" id="inputName" placeholder="The name of the Connector">
			    </div>
			  </div>
			  <hr />
              <div class="form-group">
                <label for="inputPAWorkflow" class="col-sm-2 control-label">PA Workflow</label>
                <div class="col-sm-10">
                  <select class="form-control" name="paWorkflow" id="inputPAWorkflow">
				  	  <option value="NotSupported">Not Supported</option>
	                  <option value="Normal">Normal</option>
				      <option value="RequestAndAlwaysRedirectThenExternal">RequestAndAlwaysRedirectThenExternal</option>
				      <option value="RequestAndAlwaysRedirectThenRedirect">RequestAndAlwaysRedirectThenRedirect</option>
				      <option value="AlwaysRedirectThenExternal">AlwaysRedirectThenExternal</option>
				      <option value="AlwaysRedirectThenRedirect">AlwaysRedirectThenRedirect</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="inputCPWorkflow" class="col-sm-2 control-label">CP Workflow</label>
                <div class="col-sm-10">
                  <select class="form-control" name="cpWorkflow" id="inputCPWorkflow">
				  	  <option value="NotSupported">Not Supported</option>
	                  <option value="Normal">Normal</option>
				      <option value="RequestAndAlwaysRedirectThenExternal">RequestAndAlwaysRedirectThenExternal</option>
				      <option value="RequestAndAlwaysRedirectThenRedirect">RequestAndAlwaysRedirectThenRedirect</option>
				      <option value="AlwaysRedirectThenExternal">AlwaysRedirectThenExternal</option>
				      <option value="AlwaysRedirectThenRedirect">AlwaysRedirectThenRedirect</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="inputDBWorkflow" class="col-sm-2 control-label">DB Workflow</label>
                <div class="col-sm-10">
                  <select class="form-control" name="dbWorkflow" id="inputDBWorkflow">
				  	  <option value="NotSupported">Not Supported</option>
	                  <option value="Normal">Normal</option>
				      <option value="RequestAndAlwaysRedirectThenExternal">RequestAndAlwaysRedirectThenExternal</option>
				      <option value="RequestAndAlwaysRedirectThenRedirect">RequestAndAlwaysRedirectThenRedirect</option>
				      <option value="AlwaysRedirectThenExternal">AlwaysRedirectThenExternal</option>
				      <option value="AlwaysRedirectThenRedirect">AlwaysRedirectThenRedirect</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="inputRVWorkflow" class="col-sm-2 control-label">RV Workflow</label>
                <div class="col-sm-10">
                  <select class="form-control" name="rvWorkflow" id="inputRVWorkflow">
				  	  <option value="NotSupported">Not Supported</option>
	                  <option value="Normal">Normal</option>
				      <option value="RequestAndAlwaysRedirectThenExternal">RequestAndAlwaysRedirectThenExternal</option>
				      <option value="RequestAndAlwaysRedirectThenRedirect">RequestAndAlwaysRedirectThenRedirect</option>
				      <option value="AlwaysRedirectThenExternal">AlwaysRedirectThenExternal</option>
				      <option value="AlwaysRedirectThenRedirect">AlwaysRedirectThenRedirect</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="inputRFWorkflow" class="col-sm-2 control-label">RF Workflow</label>
                <div class="col-sm-10">
                  <select class="form-control" name="rfWorkflow" id="inputRFWorkflow">
				  	  <option value="NotSupported">Not Supported</option>
	                  <option value="Normal">Normal</option>
				      <option value="RequestAndAlwaysRedirectThenExternal">RequestAndAlwaysRedirectThenExternal</option>
				      <option value="RequestAndAlwaysRedirectThenRedirect">RequestAndAlwaysRedirectThenRedirect</option>
				      <option value="AlwaysRedirectThenExternal">AlwaysRedirectThenExternal</option>
				      <option value="AlwaysRedirectThenRedirect">AlwaysRedirectThenRedirect</option>
                  </select>
                </div>
              </div>
              <hr />
              <div class="form-group">
                <label for="inputTransmissionType" class="col-sm-2 control-label">Transmission Type</label>
                <div class="col-sm-10">
                  <select class="form-control" name="transmissionType" id="inputTransmissionType">
				  	  <option value="http">HTTP</option>
	                  <option value="tcp">TCP</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">Primary Endpoint</label>
                <div class="col-sm-10">
                  	<div class="input-group">
				      <span class="input-group-addon">
				        <input type="checkbox" name="isPrimaryEndpoint">
				      </span>
				      <input type="text" class="form-control" name="primaryEndpoint">
				    </div><!-- /input-group -->
                </div>
              </div>
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">Secondary Endpoint</label>
                <div class="col-sm-10">
                  	<div class="input-group">
				      <span class="input-group-addon">
				        <input type="checkbox" name="isSecondaryEndpoint">
				      </span>
				      <input type="text" class="form-control" name="secondaryEndpoint">
				    </div><!-- /input-group -->
                </div>
              </div>
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">PA Endpoint</label>
                <div class="col-sm-10">
                  	<div class="input-group">
				      <span class="input-group-addon">
				        <input type="checkbox" name="isPAEndpoint">
				      </span>
				      <input type="text" class="form-control" name="paEndpoint">
				    </div><!-- /input-group -->
                </div>
              </div>
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">CP Endpoint</label>
                <div class="col-sm-10">
                  	<div class="input-group">
				      <span class="input-group-addon">
				        <input type="checkbox" name="isCPEndpoint">
				      </span>
				      <input type="text" class="form-control" name="cpEndpoint">
				    </div><!-- /input-group -->
                </div>
              </div>
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">DB Endpoint</label>
                <div class="col-sm-10">
                  	<div class="input-group">
				      <span class="input-group-addon">
				        <input type="checkbox" name="isDBEndpoint">
				      </span>
				      <input type="text" class="form-control" name="dbEndpoint">
				    </div><!-- /input-group -->
                </div>
              </div>
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">RV Endpoint</label>
                <div class="col-sm-10">
                  	<div class="input-group">
				      <span class="input-group-addon">
				        <input type="checkbox" name="isRVEndpoint">
				      </span>
				      <input type="text" class="form-control" name="rvEndpoint">
				    </div><!-- /input-group -->
                </div>
              </div>
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">RF Endpoint</label>
                <div class="col-sm-10">
                  	<div class="input-group">
				      <span class="input-group-addon">
				        <input type="checkbox" name="isRFEndpoint">
				      </span>
				      <input type="text" class="form-control" name="rfEndpoint">
				    </div><!-- /input-group -->
                </div>
              </div>
              <hr />
			  <div class="form-group">
                <label for="inputRequestType" class="col-sm-2 control-label">Request Type</label>
                <div class="col-sm-10">
                  <select class="form-control" name="requestType" id="inputRequestType"><option value="json">JSON</option><option value="postparms">POST params</option></select>
                </div>
              </div>
              
              <div class="form-group">
		        <label class="col-md-2 control-label">Request Mapping</label>
		        <div class="col-sm-10">
		            <div class="form-group row">
		                <label for="inputKey" class="col-md-1 control-label">PayPipe</label>
		                <div class="col-md-2">
		                    <select class="form-control" name="requestName[]">
								<option value="Request.Transaction.Identification.OrderID">Request.Transaction.Identification.OrderID</option>
								<option value="Request.Transaction.Identification.ShortID">Request.Transaction.Identification.ShortID</option>
								<option value="Request.Transaction.MerchantAccount.MerchantID">Request.Transaction.MerchantAccount.MerchantID</option>
								<option value="Request.Transaction.MerchantAccount.MerchantName">Request.Transaction.MerchantAccount.MerchantName</option>
								<option value="Request.Transaction.MerchantAccount.Password">Request.Transaction.MerchantAccount.Password</option>
								<option value="Request.Transaction.Payment.type">Request.Transaction.Payment.type</option>
								<option value="Request.Transaction.Payment.Amount">Request.Transaction.Payment.Amount</option>
								<option value="Request.Transaction.Payment.Currency">Request.Transaction.Payment.Currency</option>
								<option value="Request.Transaction.Payment.Descriptor">Request.Transaction.Payment.Descriptor</option>
								<option value="Request.Transaction.CreditCardAccount.Holder">Request.Transaction.CreditCardAccount.Holder</option>
								<option value="Request.Transaction.CreditCardAccount.Verification">Request.Transaction.CreditCardAccount.Verification</option>
								<option value="Request.Transaction.CreditCardAccount.Brand">Request.Transaction.CreditCardAccount.Brand</option>
								<option value="Request.Transaction.CreditCardAccount.Number">Request.Transaction.CreditCardAccount.Number</option>
								<option value="Request.Transaction.CreditCardAccount.ExpiryMonth">Request.Transaction.CreditCardAccount.ExpiryMonth</option>
								<option value="Request.Transaction.CreditCardAccount.ExpiryYear">Request.Transaction.CreditCardAccount.ExpiryYear</option>
								<option value="Request.Transaction.Customer.Name.Family">Request.Transaction.Customer.Name.Family</option>
								<option value="Request.Transaction.Customer.Name.Given">Request.Transaction.Customer.Name.Given</option>
								<option value="Request.Transaction.Customer.Contact.Ip">Request.Transaction.Customer.Contact.Ip</option>
								<option value="Request.Transaction.Customer.Contact.Email">Request.Transaction.Customer.Contact.Email</option>
								<option value="Request.Transaction.Customer.Contact.Phone">Request.Transaction.Customer.Contact.Phone</option>
								<option value="Request.Transaction.Customer.Contact.Mobile">Request.Transaction.Customer.Contact.Mobile</option>
								<option value="Request.Transaction.Customer.Address.Country">Request.Transaction.Customer.Address.Country</option>
								<option value="Request.Transaction.Customer.Address.City">Request.Transaction.Customer.Address.City</option>
								<option value="Request.Transaction.Customer.Address.State">Request.Transaction.Customer.Address.State</option>
								<option value="Request.Transaction.Customer.Address.Street">Request.Transaction.Customer.Address.Street</option>
								<option value="Request.Transaction.Customer.Address.Zip">Request.Transaction.Customer.Address.Zip</option>
							</select>
		                </div>
		                <label for="inputValue" class="col-md-1 control-label">Connector</label>
		                <div class="col-md-2">
		                    <input type="text" class="form-control" name="requestVal[]" placeholder="value">
		                </div>
		                <label for="inputValue" class="col-md-1 control-label">For</label>
		                <div class="col-md-2">
		                    <input type="text" class="form-control" name="requestFor[]" value="PA,CP,DB,RV,RF">
		                </div>
		                <span href="<?php echo $config->rootDir;?>" onclick="removeRequestField(this)" class="btn btn-danger">-</span>
		            </div>
		            <span href="<?php echo $config->rootDir;?>" onclick="addRequestField(this)" class="btn btn-success">+</span>
		        </div>
		    </div>
		    <hr />
              <div class="form-group">
                <label for="inputResponseType" class="col-sm-2 control-label">Response Type</label>
                <div class="col-sm-10">
                  <select class="form-control" name="responseType" id="inputResponseType"><option value="json">JSON</option></select>
                </div>
              </div>
              <div class="form-group">
		        <label class="col-md-2 control-label">Response Mapping</label>
		        <div class="col-sm-10">
		            <div class="form-group row">
		                <label for="inputKey" class="col-md-1 control-label">PayPipe</label>
		                <div class="col-md-2">
		                	<select class="form-control" name="responseName[]">
		                		<option value="Response.Transaction.mode">Response.Transaction.mode</option>
		                		<option value="Response.Transaction.response">Response.Transaction.response</option>
		                		<option value="Response.Transaction.response">Response.Transaction.response</option>
		                		<option value="Response.Transaction.Identification.ShortID">Response.Transaction.Identification.ShortID</option>
		                		<option value="Response.Transaction.Identification.UUID">Response.Transaction.Identification.UUID</option>
		                		<option value="Response.Transaction.Identification.OrderID">Response.Transaction.Identification.OrderID</option>
		                		<option value="Response.Transaction.Identification.ShopperID">Response.Transaction.Identification.ShopperID</option>
		                		<option value="Response.Transaction.Processing.requestTimestamp">Response.Transaction.Processing.requestTimestamp</option>
		                		<option value="Response.Transaction.Processing.responseTimestamp">Response.Transaction.Processing.responseTimestamp</option>
		                		<option value="Response.Transaction.Processing.payPipeProcessingTime">Response.Transaction.Processing.payPipeProcessingTime</option>
		                		<option value="Response.Transaction.Processing.connectorTime">Response.Transaction.Processing.connectorTime</option>
		                		<option value="Response.Transaction.Processing.Return.code">Response.Transaction.Processing.Return.code</option>
		                		<option value="Response.Transaction.Processing.Return">Response.Transaction.Processing.Return</option>
		                		<option value="Response.Transaction.Processing.ConnectorTxID1">Response.Transaction.Processing.ConnectorTxID1</option>
		                		<option value="Response.Transaction.Processing.ConnectorTxID1.description">Response.Transaction.Processing.ConnectorTxID1.description</option>
		                		<option value="Response.Transaction.Processing.ConnectorTxID2">Response.Transaction.Processing.ConnectorTxID2</option>
		                		<option value="Response.Transaction.Processing.ConnectorTxID2.description">Response.Transaction.Processing.ConnectorTxID2.description</option>
		                		<option value="Response.Transaction.Processing.ConnectorTxID3">Response.Transaction.Processing.ConnectorTxID3</option>
		                		<option value="Response.Transaction.Processing.ConnectorTxID3.description">Response.Transaction.Processing.ConnectorTxID3.description</option>
		                		<option value="Response.Transaction.Processing.AVSResult">Response.Transaction.Processing.AVSResult</option>
		                		<option value="Response.Transaction.Processing.CVVResult">Response.Transaction.Processing.CVVResult</option>
		                		<option value="Response.Transaction.Processing.Redirect">Response.Transaction.Processing.Redirect</option>
		                		<option value="Response.Transaction.Processing.Redirect.url">Response.Transaction.Processing.Redirect.url</option>
		                		<option value="Response.Transaction.Processing.Redirect.Parameters">Response.Transaction.Processing.Redirect.Parameters</option>
		                		<option value="Response.Transaction.Processing.ConnectorSent.Url">Response.Transaction.Processing.ConnectorSent.Url</option>
		                		<option value="Response.Transaction.Processing.ConnectorSent.Body">Response.Transaction.Processing.ConnectorSent.Body</option>
		                		<option value="Response.Transaction.Processing.ConnectorReceived.timestamp">Response.Transaction.Processing.ConnectorReceived.timestamp</option>
		                		<option value="Response.Transaction.Processing.ConnectorReceived.Returned.code">Response.Transaction.Processing.ConnectorReceived.Returned.code</option>
		                		<option value="Response.Transaction.Processing.ConnectorReceived.Body">Response.Transaction.Processing.ConnectorReceived.Body</option>
		                		<option value="Response.Transaction.Processing.ConnectorReceived.Body.SessionId">Response.Transaction.Processing.ConnectorReceived.Body.SessionId</option>
		                		<option value="Response.Transaction.Processing.ConnectorReceived.Body.ClientToken">Response.Transaction.Processing.ConnectorReceived.Body.ClientToken</option>
		                		<option value="Response.Transaction.Processing.ConnectorDetails">Response.Transaction.Processing.ConnectorDetails</option>
		                		<option value="Response.Transaction.Processing.SecurityHash">Response.Transaction.Processing.SecurityHash</option>
		                	</select>
		                    <!-- <input type="text" class="form-control" name="responseName[]" placeholder="Request.Transaction.Payment.Amount"> -->
		                </div>
		                <label for="inputValue" class="col-md-1 control-label">Connector</label>
		                <div class="col-md-2">
		                    <input type="text" class="form-control" name="responseVal[]" placeholder="value">
		                </div>
		                <span href="<?php echo $config->rootDir;?>" onclick="removeRequestField(this)" class="btn btn-danger">-</span>
		            </div>
		            <span href="<?php echo $config->rootDir;?>" onclick="addResponseField(this)" class="btn btn-success">+</span>
		        </div>
		    </div>
		    <hr />
			  <div class="form-group">
                <label class="col-md-2 control-label">Response Code Mapping</label>
		        <div class="col-sm-10">
		            <div class="form-group row">
		                <label for="inputKey" class="col-md-1 control-label">PayPipe</label>
		                <div class="col-md-2">
		                    <input type="text" class="form-control" name="responseCodeName[]" placeholder="000.000.000">
		                </div>
		                <label for="inputValue" class="col-md-1 control-label">Connector</label>
		                <div class="col-md-2">
		                    <input type="text" class="form-control" name="responseCodeVal[]" placeholder="value">
		                </div>
		                <span href="<?php echo $config->rootDir;?>" onclick="removeRequestField(this)" class="btn btn-danger">-</span>
		            </div>
		            <span href="<?php echo $config->rootDir;?>" onclick="addResponseCodeField(this)" class="btn btn-success">+</span>
		        </div>
              </div>
              <hr />
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">Features</label>
                <div class="col-sm-10">
                  	<input type="text" class="form-control" name="features" value="ALL">
                </div>
              </div>
              <hr />
              <div class="form-group">
                <label for="inputEndpoint" class="col-sm-2 control-label">Currency</label>
                <div class="col-sm-10">
				    <input type="text" class="form-control" name="currency" value="ALL">
                </div>
              </div>
              <hr />
              <div class="form-group">
                <label class="col-md-2 control-label">Merchant Account Mapping</label>
		        <div class="col-sm-10">
		            <input type="text" class="form-control" name="merchantAccount" value="">
		        </div>
              </div>
				<hr />
			  <div class="form-group">
			    <div class="col-sm-offset-2 col-sm-10">
			      <input type="submit" class="btn btn-success" name="createConnector" value="Create" />
			      <a href="<?php echo $config->rootDir;?>" class="btn btn-danger">Cancel</a>
			    </div>
			  </div>
			</form>
		</div>
	</div>
</div>