<!DOCTYPE html>
<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script src="https://code.jquery.com/jquery-3.1.0.min.js" type="text/javascript"></script>
	<script src="https://credit.klarnacdn.net/lib/v1/api.js?1477597860253"></script>
	<style>
		body {
			padding:0;
			margin:auto;
			width:600px;
		}
	</style>
</head>
</head>
<body>
<?php
$workflow = "internal-to-external";
$url = "callback.php?connector=" . $request["connector"] . "&handleAction=true" . "&type=" . $workflow . "&ndc=" . $request["ndc"];

if (isset($_SESSION["client_token"])) {
	//this is set in setSessionData() in response to sync request
	$token = $_SESSION["client_token"];
	?>
	<div id="klarna_container"></div>
	<button id="submitKlarna">Submit</button>

	<script type="text/javascript">
	Klarna.Credit.init ({
	  client_token : '<?php echo $token; ?>'
	});
	Klarna.Credit.load ({
		container : "#klarna_container"
	}, function(res) {
		console.debug("load", res);
	});
	$("#submitKlarna").click(function(){
		$("#submitKlarna").attr('disabled', true);
		Klarna.Credit.authorize(function(res) {
			console.log("token", res.authorization_token);
			window.location.replace("<?php echo $url;?>&token=" + res.authorization_token);
		});
	});
	</script>
<?php
}
else {
	echo "<br />Error: client_token is not set";
}
?>
</body>
</html>