<?php
require_once(__DIR__ . "/../util/Flattener.php");
class Mapper {
	public function createPostParams($request, $requestMappingArr, $paymentType, &$errors) {
		$payload = "";
		$count = 0;
		foreach ($request as $key => $value) {
			if(array_key_exists($key, $requestMappingArr) 
				&& (strpos($requestMappingArr[$key]["for"], $paymentType) !== false)) {
				if ($count > 0) {
					$payload .= "&";
				}
				$payload .= $requestMappingArr[$key]["to"] . "=" . $value;
				$count++;
			}
		}
		if (isset($requestMappingArr["hardcodedParams"]) 
			&& isset($requestMappingArr["hardcodedParams"]["params"])
			&& (strpos($requestMappingArr["hardcodedParams"]["for"], $paymentType) !== false)) {
			if ($count > 0) {
				$payload .= "&";
			}
			foreach ($requestMappingArr["hardcodedParams"]["params"] as $pKey => $pVal) {
				$payload .= $pKey . "=" . $pVal;
			}
		}
		$this->payloadChecks($payload, $errors);
		return $payload;
	}
	public function createJson($request, $requestMappingArr, $paymentType, &$errors) {
		$payload = "";
		$arr = array();
		$templistArr = array();
		$tempnum = "";
		//initial pass
		foreach ($request as $key => $value) {
			$newkey = $key;
			$matches = array();
			$isList = false;
			if (preg_match("/\[[(0-9){2}]\]/", $key, $matches)) {
				$num = $matches[0];
				$num = str_replace("[", "", $num);
				$num = str_replace("]", "", $num);
				$num = intval($num);
				if ($num > $tempnum) $tempnum = $num;
				$isList = true;
				$newkey = preg_replace("/\[[(0-9){2}]\]/", "[n]", $key);
			}
			$value = str_replace("\r", "", $value);
			if(array_key_exists($newkey, $requestMappingArr) 
				&& (strpos($requestMappingArr[$newkey]["for"], $paymentType) !== false)) {
				if ($isList) {
					$to = str_replace(".{n}.", "." . $num .".", $requestMappingArr[$newkey]["to"]);
					$templistArr[$to] = $request[$key];
					unset($request[$key]);
					continue;
				}
				else {
					$this->keyValIntoArray($arr, $requestMappingArr[$key]["to"], $value);
				}
			}
		}
		//if there are list items, second pass
		$anotherTemp = array();
		if (count($templistArr) > 0) {
			while ($tempnum > 0) {
				foreach($templistArr as $key => $val) {
					if (strpos($key, "." . $tempnum . ".") !== false) {
						$anotherTemp[$tempnum][$key] = $val;
						unset($templistArr[$key]);
					}
				}
				$tempnum--;
			}
			unset($templistArr);
		}
		foreach($anotherTemp as $arrVal) {
			$name = "mapping-error";
			foreach ($arrVal as $key => $value) {
				$value = str_replace("\r", "", $value);
				
				$newkey = $key;
				if (strpos($key, ".") !== false) {
					$arrKeyParts = explode(".", $key);
					$name = $arrKeyParts[0];
					$count = count($arrKeyParts);
					if ($count == 1) {
						$newkey = $arrKeyParts[0];
					}
					else if ($count == 2) {
						$newkey = $arrKeyParts[1];
					}
					else if ($count == 3) {
						$newkey = $arrKeyParts[2];
					}
				}
				unset($arrVal[$key]);
				$arrVal[$newkey] = $value;
			}
			$arr[$name][] = $arrVal;
		}
		//hardcoded
		if (isset($requestMappingArr["hardcodedParams"]) 
			&& isset($requestMappingArr["hardcodedParams"]["params"])
			&& (strpos($requestMappingArr["hardcodedParams"]["for"], $paymentType) !== false)) {
			foreach ($requestMappingArr["hardcodedParams"]["params"] as $pKey => $pVal) {
				$this->keyValIntoArray($arr, $pKey, $pVal);
			}
		}
		$arr = json_encode($arr, JSON_UNESCAPED_SLASHES);

		$this->payloadChecks($arr, $errors);
		return $arr;
	}
	private function keyValIntoArray(&$arr, $key, $val) {
		if (strpos($key, ".") !== false) {
			$arrKeyParts = explode(".", $key);
			$count = count($arrKeyParts);
			//TODO: can this be made to support any count?
			if ($count == 1) {
				$arr[$arrKeyParts[0]] = $val;
			}
			else if ($count == 2) {
				if (!array_key_exists($arrKeyParts[0], $arr)) $arr[$arrKeyParts[0]] = array();

				$arr[$arrKeyParts[0]][$arrKeyParts[1]] = $val;
			}
			else if ($count == 3) {
				if (!array_key_exists($arrKeyParts[0], $arr)) $arr[$arrKeyParts[0]] = array();
				if (!array_key_exists($arrKeyParts[1], $arr[$arrKeyParts[0]])) $arr[$arrKeyParts[0]][$arrKeyParts[1]] = array();
				$arr[$arrKeyParts[0]][$arrKeyParts[1]][$arrKeyParts[2]] = $val;
			}
		}
		else {
			$arr[$key] = $val;
		}
	}
	public function parseJsonResponse(&$responseInit, $responseMappingArr, &$errors) {
		$json = json_decode($responseInit["body"], true);
		if (!isset($json)) {
			$errors[] = "Cannot parse JSON response";
			return $responseInit;
		}
		$flatjson = (new Flattener())->flattenJson($json);
		$response = array();
		$count = 0;
		foreach ($flatjson as $key => $value) {
			$newkey = $key;
			$num = "";
			$matches = array();
			if (preg_match("/\.[(0-9){2}]\./", $key, $matches)) {
				$num = $matches[0];
				$newkey = preg_replace("/\.[(0-9){2}]\./", ".{n}.", $key);
			}
			if(in_array($newkey, $responseMappingArr)) {
				//TODO: can array_search be used earlier to save in_array?
				$mapkey = array_search($newkey, $responseMappingArr);
				$mapkey = str_replace(".{n}.", $num, $mapkey);
				$response[$mapkey] = $value;
				$count++;
			}
		}
		$this->responseChecks($response, $errors);
		$responseInit["body"] = $response;
		//return $response;
	}
	public function parseEchoResponse(&$responseInit, $responseMappingArr, &$errors) {
		$arr = array("echoResponse"=>$response["body"]);
		$responseInit["body"] = $arr;
		//return $arr;
	}
	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	private function payloadChecks($payload, &$errors) {
		if (!isset($payload) || empty($payload)) {
			$errors[] = "error occurred while parsing payload";
		}
	}
	private function responseChecks($response, &$errors) {
		if (!isset($response) || empty($response)) {
			$errors[] = "error occurred while parsing response";
		}
	}
}
?>