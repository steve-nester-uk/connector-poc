<?php
function getTimestamp()	{
	date_default_timezone_set('Asia/Seoul');
	$date = new DateTime();
	$milliseconds = round(microtime(true) * 1000);	
	$tempValue1 = round($milliseconds/1000);
	$tempValue2 = round(microtime(false) * 1000);
	switch (strlen($tempValue2)) {
		case '3':
			break;
		case '2':
			$tempValue2 = "0".$tempValue2;
			break;
		case '1':
			$tempValue2 = "00".$tempValue2;
			break;
		default:
			$tempValue2 = "000";
			break;
	}
	return "".$tempValue1.$tempValue2;
}
function makeSignature($signParam) {
	ksort($signParam);
	$string = "";
	foreach ($signParam as $key => $value) {
		$string .= "&$key=$value";
	}		
	$string = substr($string, 1);
	$sign = makeHash($string, "sha256");
	return $sign;
}

function makeHash($data, $alg) {
	$ret = openssl_digest($data, $alg);
	return $ret;
}

$mid = "";
$signKey = "";
$price = "";
$cardNoInterestQuota = "";
$cardQuotaBase = "";

if (isset($_SESSION["mid"])) {
	$mid = $_SESSION["mid"];
}
if (isset($_SESSION["signKey"])) {
	$signKey = $_SESSION["signKey"];
}
if (isset($_SESSION["price"])) {
	$price = $_SESSION["price"];
}
if (isset($_SESSION["cardNoInterestQuota"])) {
	$cardNoInterestQuota = $_SESSION["cardNoInterestQuota"];
}
if (isset($_SESSION["cardQuotaBase"])) {
	$cardQuotaBase = $_SESSION["cardQuotaBase"];
}

$timestamp = getTimestamp();
$orderNumber = $mid . "_" . $timestamp;

$mKey = makeHash($signKey, "sha256");
$params = array(
    "oid" => $orderNumber,
    "price" => $price,
    "timestamp" => $timestamp
);
$sign = makeSignature($params, "sha256");

$workflow = "internal-to-external";
$url = "callback.php?connector=" . $request["connector"] . "&handleAction=true" . "&type=" . $workflow . "&ndc=" . $request["ndc"];
//$this->envconfig->siteProtocol . $this->envconfig->siteHost . 
$successUrl = "http://localhost:8777/connector/" . $url . "&action=success";
$cancelUrl = "http://localhost:8777/connector/" . $url . "&action=cancel";

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
		<form action="https://stgstdpay.inicis.com/payMain/pay" method="POST" accept-charset="UTF-8">
            <input type="hidden" name="timestamp" value="<?php echo $timestamp ?>" >
            <input type="hidden" name="signature" value="<?php echo $sign ?>" >
            <input type="hidden" name="mKey" value="<?php echo $mKey ?>" >
            <input type="hidden" name="requestByJs" value="true" >
            <br/><b>version</b> :<input name="version" value="1.0" >
            <br/><b>mid</b> :<input name="mid" value="<?php echo $mid ?>" >
            <br/><b>goodname</b> :<input name="goodname" value="테스트" >
            <br/><b>goodname</b> :<input name="goodsname" value="테스트" >
            <br/><b>oid</b> :<input name="oid" value="<?php echo $orderNumber ?>" >
            <br/><b>price</b> :<input name="price" value="<?php echo $price ?>" >
            <br/><b>currency</b> :<input name="currency" value="WON" >
            <!-- <br/><b>buyername</b> :<input name="buyername" value="홍길동" > -->
            <br/><b>buyername</b> :<input name="buyerName" value="홍길동" >
            <br/><b>buyertel</b> :<input name="buyertel" value="010-1234-5678" >
            <br/><b>buyeremail</b> :<input name="buyeremail" value="test@inicis.com" >
            <br/><b>returnUrl</b> : <input name="returnUrl" value="<?php echo $successUrl ?>" >
            <br/><b>gopaymethod</b> : <input name="gopaymethod" value="" >
            <br/><b>offerPeriod</b> : <input name="offerPeriod" value="2015010120150331" >
            <br/><b>acceptmethod</b> : <input name="acceptmethod" value="HPP(1):no_receipt:va_receipt:vbanknoreg(0):vbank(20150611):below1000" >
            <br/><b>languageView</b> : <input name="languageView" value="" >
			<br/><b>charset</b> : <input name="charset" value="UTF-8" >
			<br/><b>payViewType</b> : <input name="payViewType" value="overlay" >
			<br/><b>payMethod</b> : <input name="payMethod" value="onlyhpp" >
			<br/><b>closeUrl</b> : <input name="closeUrl" value="<?php echo $cancelUrl ?>" >
            <input type="submit" value="Submit" />
        </form>
    </body>
</html>