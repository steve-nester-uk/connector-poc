<?php
require_once(__DIR__ . "/Processing.php");
require_once(__DIR__ . "/util/Flattener.php");
//Sunday
//09:35 to 10:55 = 1:20 => generic validation 
//11:05 to 12:45 = 1:40 => generic configuration
//13:00 to 13:25 = 0:25 => generic mapping (now I have sent a request to OPP, using mapping)
//13:25 to 13:30 = 0:05 => started response mapping
//15:05 to 16:05 = 1:00 => parsing json response to flattened xml
//16:10 to 17:10 = 1:00 => made payload parsing generic, based on mapping array
//19:50 to 20:10 = 0:20 => pulled out parameter mapper, to support different versions
//20:20 to 21:30 = 1:10 => add error parsing and some other refactoring
//21:30 to 21:45 = 0:15 => todo list
//Monday
//14:15 to 14:25 = 0:10 => added support for custom http headers and basic auth
//14:25 to 15:00 = 0:35 => added basic simulator
//16:45 to 17:25 = 0:40 => simplifed error handling and added tcp
//19:20 to 19:30 = 0:10 => made generic processing
//19:40 to 20:10 = 0:30 => started implementing callbacks
//-----------------9:30
//Tuesday
//08:25 to 08:55 = 0:30 => started async data
//10:20 to 10:30 = 0:10 => added hardcoded parameters
//10:40 to 11:30 = 0:50 => lists of data in response
//12:15 to 12:35 = 0:20 => refactoring
//12:50 to 13:30 = 0:40 => workflow types
//14:30 to 16:20 = 1:50 => first callback! (javascript pain!!)
//16:20 to 17:50 = 1:30 => callbacks, redirects, workflows, echo
//19:20 to 20:40 = 1:20 => list format, 3d secure redirect!
//Wednesday
//07:40 to 08:00 = 0:20 => sessions
//16:20 to 17:30 = 1:10 => working on final redirects and callback workflows
//17:30 to 18:20 = 0:50 => workflow diagrams
//19:00 to 19:15 = 0:15 => shopperReturn
//-----------------19:15
//Thursday
//13:40 to 13:50 = 0:10 => response workflow / callbacks
//14:30 to 15:10 = 0:40 => workflow refactoring
//15:30 to 16:10 = 0:40 => workflows are pretty much done!
//16:10 to 16:40 = 0:30 => more refactoring so callback classes are simpler
//16:40 to 17:20 = 0:40 => started implementing Klarna - error message locations, basic auth
//19:30 to 21:00 = 1:30 => implemented Klarna Credit!? (with hacks)
//Friday
//08:30 to 09:30 = 1:00 => json request format - just lists / {n}
//12:10 to 17:40 = 5:30 => json lists + klarna testing, sessions, refactoring, testing
//17:40 to 18:00 = 0:20 => reviewing todos and planning next
//Saturday
//08:45 to 11:15 = 2:30 => portal design/infrastructure
//13:00 to 14:00 = 1:00 => add page design / drop down list of connectors / added bootstrap
//17:00 to 17:30 = 0:30 => added connector page and start of 'PIA' information
//20:40 to 22:10 = 1:30 => add connector functionality, started options
//-----------------34:45
//Sunday
//17:10 to 17:50 = 0:40 => fake/example run tests page
//17:50 to 18:10 = 0:20 => edit connector page
//Monday
//11:30 to 11:40 = 0:10 => update TODOs
//11:40 to 12:00 = 0:20 => config
//14:50 to 15:30 = 0:40 => config, using rootDir + host instead of hardcoded across code base
//15:30 to 17:00 = 1:30 => add connector form updates
//19:30 to 21:00 = 1:30 => done reqest/response mapping in add connector - almost done!
//Tuesday
//15:10 to 16:30 = 1:20 => connector add validation, klarna bug (201 http code and callbackUrl)
//18:30 to 19:30 = 1:00 => response code mapping
//Thursday 23rd Feb
//19:00 to 22:00 = 3:00 => refactoring, workflow review
//Friday 24th Feb
//07:00 to 08:30 = 1:30 => workflows, offline / bfpe
//08:30 to 09:30 = 1:00 => testing OPP / implemented INICIS
//Tuesday 14th Mar
//10:05 to 11:35 = 1:30 => interface, XML flattening


if (isset($_POST["requestXml"])) {
	$request = $_POST["requestXml"];
	if (!validate($request)) {
		echo "Error! The request did not validate";
		die();
	}
	doPayPipe($request);
}
else {
	$request = file_get_contents('php://input');
	if (!validate($request)) {
		echo "Error! The request did not validate";
		die();
	}
	doOpp($request);
}

function validate($request) {
	if (isset($request) && !empty($request)) {
		return true;
	}
	else {
		return false;
	}
}

function doPayPipe($request) {
	//Flatten XML
	if (!isFlattened($request)) {
		$requestArr = xmlToFlattened($request);
		// print_r($requestArr);
		// exit();
	}
	$response = (new Processing())->send($requestArr);
	print_r ($response);
}
function doOpp($request) {
	$requestArr = convertToArray($request, "\n"); // delimiter = & ?
	$response = (new Processing())->send($requestArr);
	print_r ($response);
}

function isFlattened($request) {
	//if starts with "<"
	//return false
	return false;
}
function flattenedToXml($data) {
	return $data;
}
function xmlToFlattened($data) {
	$xml = simplexml_load_string($data);
	return (new Flattener())->flattenXml($xml, $xml->getName());
}
function convertToArray($request, $delimiter) {
	$arr = array();
	$temp = explode($delimiter, $request);
	for ($i = 0; $i < count($temp); $i++) {
		$parts = explode('=', $temp[$i]);
		$arr[$parts[0]] = $parts[1];
	}
	return $arr;
}
?>
