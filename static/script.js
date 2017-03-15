$(document).ready(function(){
			submitInit();
			$("li.dropdown").click(function(){
				$(this).toggleClass("open");
			});
			$(".navbar-toggle").click(function(){
				$(".navbar-collapse").toggleClass("open");
			});
		});
		function submitInit() {
			$("#btn-submit").click(function(){
				$("#btn-submit").prop('disabled', true);
				$("#response").text("loading..");
				var requestData = "";
				if (document.getElementById('requestXML').checked) {
					//console.log("requestXML is checked");
					requestData = {requestXml:$("#request").val()};
				}
				else {
					//console.log("requestXML is NOT checked");
					requestData = $("#request").val();
				}
				$.post( "interface.php", requestData, function( data ) {
					console.log("data", data);
				  	
				  	if (IsJsonString(data)) {
				  		//TODO: calling same method twice
				  		data = JSON.parse(data);
				  		if (data.errors.length > 0) {
							$("#response").text(JSON.stringify(data.errors));
				  		}
				  		else {
				  			$("#response").text(JSON.stringify(data.response));
				  		}
				  		if (!data.response) {
				  			return;
				  		}
					  	var params = data.response["Response.Transaction.Processing.Redirect.Parameters"];
					  	var url = data.response["Response.Transaction.Processing.Redirect.url"];
					  	if (url === undefined) {
					  		return;
					  	}
						if (params === undefined) {
					  		window.location.replace(url);
						}
						else {
							var myHtml = "";
							myHtml += "<form id='myform' method='POST' action='" + url + "'>";						
							for(var param in params) {
								myHtml += "<input type='hidden' name='" + param + "' value='" + params[param] + "' />"
							}
							myHtml += "</form>";
							$("#redirectArea").html(myHtml);
							$("#myform").submit();
						}
				  	}
				  	$("#btn-submit").prop('disabled', false);
				});
			});
		}
		function IsJsonString(str) {
		    try {
		        JSON.parse(str);
		    } catch (e) {
		        return false;
		    }
		    return true;
		}

function addField(div, options, type) {
    var myHtml = "";
	myHtml += '<div class="form-group row">';
    myHtml += '        <label for="inputKey" class="col-md-1 control-label">PayPipe</label>';
    myHtml += '        <div class="col-md-2">';
    myHtml += '            ' + options;
    myHtml += '        </div>';
    myHtml += '        <label for="inputValue" class="col-md-1 control-label">Connector</label>';
    myHtml += '        <div class="col-md-2">';
    myHtml += '            <input type="text" class="form-control" name="' + type + 'Val[]" placeholder="value">';
    myHtml += '        </div>';
    myHtml += '        <label for="inputValue" class="col-md-1 control-label">For</label>';
    myHtml += '        <div class="col-md-2">';
    myHtml += '            <input type="text" class="form-control" name="requestFor[]" value="PA,CP,DB,RV,RF">';
    myHtml += '        </div>';
    myHtml += '        <span href="<?php echo $config->rootDir;?>" onclick="removeRequestField(this)" class="btn btn-danger">-</span>';
    myHtml += '    </div>';
	$(div).before(myHtml);
}
function addRequestField(div) {
	var options = "";
	options += '    <select class="form-control" name="requestName[]">';
	options += '		<option value="Request.Transaction.Identification.OrderID">Request.Transaction.Identification.OrderID</option>';
	options += '		<option value="Request.Transaction.Identification.ShortID">Request.Transaction.Identification.ShortID</option>';
	options += '		<option value="Request.Transaction.MerchantAccount.MerchantID">Request.Transaction.MerchantAccount.MerchantID</option>';
	options += '		<option value="Request.Transaction.MerchantAccount.MerchantName">Request.Transaction.MerchantAccount.MerchantName</option>';
	options += '		<option value="Request.Transaction.MerchantAccount.Password">Request.Transaction.MerchantAccount.Password</option>';
	options += '		<option value="Request.Transaction.Payment.type">Request.Transaction.Payment.type</option>';
	options += '		<option value="Request.Transaction.Payment.Amount">Request.Transaction.Payment.Amount</option>';
	options += '		<option value="Request.Transaction.Payment.Currency">Request.Transaction.Payment.Currency</option>';
	options += '		<option value="Request.Transaction.Payment.Descriptor">Request.Transaction.Payment.Descriptor</option>';
	options += '		<option value="Request.Transaction.CreditCardAccount.Holder">Request.Transaction.CreditCardAccount.Holder</option>';
	options += '		<option value="Request.Transaction.CreditCardAccount.Verification">Request.Transaction.CreditCardAccount.Verification</option>';
	options += '		<option value="Request.Transaction.CreditCardAccount.Brand">Request.Transaction.CreditCardAccount.Brand</option>';
	options += '		<option value="Request.Transaction.CreditCardAccount.Number">Request.Transaction.CreditCardAccount.Number</option>';
	options += '		<option value="Request.Transaction.CreditCardAccount.ExpiryMonth">Request.Transaction.CreditCardAccount.ExpiryMonth</option>';
	options += '		<option value="Request.Transaction.CreditCardAccount.ExpiryYear">Request.Transaction.CreditCardAccount.ExpiryYear</option>';
	options += '		<option value="Request.Transaction.Customer.Name.Family">Request.Transaction.Customer.Name.Family</option>';
	options += '		<option value="Request.Transaction.Customer.Name.Given">Request.Transaction.Customer.Name.Given</option>';
	options += '		<option value="Request.Transaction.Customer.Contact.Ip">Request.Transaction.Customer.Contact.Ip</option>';
	options += '		<option value="Request.Transaction.Customer.Contact.Email">Request.Transaction.Customer.Contact.Email</option>';
	options += '		<option value="Request.Transaction.Customer.Contact.Phone">Request.Transaction.Customer.Contact.Phone</option>';
	options += '		<option value="Request.Transaction.Customer.Contact.Mobile">Request.Transaction.Customer.Contact.Mobile</option>';
	options += '		<option value="Request.Transaction.Customer.Address.Country">Request.Transaction.Customer.Address.Country</option>';
	options += '		<option value="Request.Transaction.Customer.Address.City">Request.Transaction.Customer.Address.City</option>';
	options += '		<option value="Request.Transaction.Customer.Address.State">Request.Transaction.Customer.Address.State</option>';
	options += '		<option value="Request.Transaction.Customer.Address.Street">Request.Transaction.Customer.Address.Street</option>';
	options += '		<option value="Request.Transaction.Customer.Address.Zip">Request.Transaction.Customer.Address.Zip</option>';
	options += '	</select>';

    addField(div, options, "request");
}
function addResponseField(div) {
	var options = "";
	options += '    <select class="form-control" name="responseName[]">';
	options += '		<option value="Response.Transaction.mode">Response.Transaction.mode</option>';
	options += '		<option value="Response.Transaction.response">Response.Transaction.response</option>';
	options += '		<option value="Response.Transaction.response">Response.Transaction.response</option>';
	options += '		<option value="Response.Transaction.Identification.ShortID">Response.Transaction.Identification.ShortID</option>';
	options += '		<option value="Response.Transaction.Identification.UUID">Response.Transaction.Identification.UUID</option>';
	options += '		<option value="Response.Transaction.Identification.OrderID">Response.Transaction.Identification.OrderID</option>';
	options += '		<option value="Response.Transaction.Identification.ShopperID">Response.Transaction.Identification.ShopperID</option>';
	options += '		<option value="Response.Transaction.Processing.requestTimestamp">Response.Transaction.Processing.requestTimestamp</option>';
	options += '		<option value="Response.Transaction.Processing.responseTimestamp">Response.Transaction.Processing.responseTimestamp</option>';
	options += '		<option value="Response.Transaction.Processing.payPipeProcessingTime">Response.Transaction.Processing.payPipeProcessingTime</option>';
	options += '		<option value="Response.Transaction.Processing.connectorTime">Response.Transaction.Processing.connectorTime</option>';
	options += '		<option value="Response.Transaction.Processing.Return.code">Response.Transaction.Processing.Return.code</option>';
	options += '		<option value="Response.Transaction.Processing.Return">Response.Transaction.Processing.Return</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorTxID1">Response.Transaction.Processing.ConnectorTxID1</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorTxID1.description">Response.Transaction.Processing.ConnectorTxID1.description</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorTxID2">Response.Transaction.Processing.ConnectorTxID2</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorTxID2.description">Response.Transaction.Processing.ConnectorTxID2.description</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorTxID3">Response.Transaction.Processing.ConnectorTxID3</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorTxID3.description">Response.Transaction.Processing.ConnectorTxID3.description</option>';
	options += '		<option value="Response.Transaction.Processing.AVSResult">Response.Transaction.Processing.AVSResult</option>';
	options += '		<option value="Response.Transaction.Processing.CVVResult">Response.Transaction.Processing.CVVResult</option>';
	options += '		<option value="Response.Transaction.Processing.Redirect">Response.Transaction.Processing.Redirect</option>';
	options += '		<option value="Response.Transaction.Processing.Redirect.url">Response.Transaction.Processing.Redirect.url</option>';
	options += '		<option value="Response.Transaction.Processing.Redirect.Parameters">Response.Transaction.Processing.Redirect.Parameters</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorSent.Url">Response.Transaction.Processing.ConnectorSent.Url</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorSent.Body">Response.Transaction.Processing.ConnectorSent.Body</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorReceived.timestamp">Response.Transaction.Processing.ConnectorReceived.timestamp</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorReceived.Returned.code">Response.Transaction.Processing.ConnectorReceived.Returned.code</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorReceived.Body">Response.Transaction.Processing.ConnectorReceived.Body</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorReceived.Body.SessionId">Response.Transaction.Processing.ConnectorReceived.Body.SessionId</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorReceived.Body.ClientToken">Response.Transaction.Processing.ConnectorReceived.Body.ClientToken</option>';
	options += '		<option value="Response.Transaction.Processing.ConnectorDetails">Response.Transaction.Processing.ConnectorDetails</option>';
	options += '		<option value="Response.Transaction.Processing.SecurityHash">Response.Transaction.Processing.SecurityHash</option>';
	options += '	</select>';

	addField(div, options, "response");
}
function addResponseCodeField(div) {
	var options = '<input type="text" class="form-control" name="responseCodeName[]" placeholder="000.000.000">';
    addField(div, options, "responseCode");
}
function removeRequestField(div) {
	$(div).parent().remove();
}