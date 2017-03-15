<?php
class Flattener {
	public function flattenXml($xml, $parent = "") {
		//TODO: clean up
		$arr = array();
		// echo "<br />parent:" . $parent . "<br />XML:";
		// print_r($xml);
		// echo "<br />:::<br />";
		$attr = $xml->attributes(); 
		if (isset($attr)) {
			foreach($attr as $a => $b) {
			    //echo $a,'="',$b,"\"\n";
			    if ($parent != "") {
    				$arr[$parent . "." . $a] = (string) $b;
	    		}
	    		else {
	    			$arr[$a] = (string) $b;
	    		}
			}
		}
		foreach($xml as $key => $value) {
			// echo "key: " . $key . "<br />";
			// echo "value: " . $value . " " . gettype($value) . " count: " . count($value) . "<br />";
			
			

			if ((gettype($value) == "object" || gettype($value) == "array") && count($value) > 0) {
				
				$childkey = $key;
	    		if ($parent != "") {
	    			$childkey = $parent . "." . $childkey;
	    		}
	    		//echo "<br />row: " . $childkey . " " . $value;
	    		$temp = $this->flattenXml($value, $childkey);
	    		// echo "<br />row3:<br />";
	    		// print_r($temp);
	    		$arr = array_merge($arr, $temp);
			}
			else {
				$value = (string) $value;
				if (!isset($value) || empty($value)) {
					$attr = $xml->$key->attributes(); 
					if (isset($attr)) {
						foreach($attr as $a => $b) {
						    //echo $a,'="',$b,"\"\n";
						    if ($parent != "") {
			    				$arr[$parent . "." . $key . "." . $a] = (string) $b;
				    		}
				    		else {
				    			$arr[$key . "." . $a] = (string) $b;
				    		}
						}
					}
				}
				else {
					//echo "row2: <br />" . $key . " " . $value;
					if ($parent != "") {
		    			$arr[$parent . "." . $key] = $value;
		    		}
		    		else {
		    			$arr[$key] = $value;
		    		}	
				}
			}
		}
		//echo "<br />:::<br />";
		return $arr;
	}
	public function flattenJson($json, $parent = "") {
	    $arr = array();
	    if (gettype($json) == "string") {
	    	return $json;
	    }
	    //TODO: need to test this against other data formats
	    //need a strong way to test different cases, expected output
	    //can visit http://localhost:8777/rootDir/util/Flattener.php and test this directly
	    foreach ($json as $key => $value) {
	    	if (gettype($value) == "array") {
	    		if($this->is_assoc($value)) {
	    			$childkey = $key;
		    		if ($parent != "") {
		    			$childkey = $parent . "." . $childkey;
		    		}
		    		$temp = $this->flattenJson($value, $childkey);
		    		$arr = array_merge($arr, $temp);
	    		}
	    		else {
	    			//This case deals with generic 'parameters' with name values
	    			$childkey = $key;
		    		if ($parent != "") {
		    			$childkey = $parent . "." . $childkey;
		    		}
		    		$temp = array();
		    		$temp2 = array();
		    		$i = 0;
		    		foreach ($value as $childArr) {
		    			$countChildren = count($childArr);
		    			if(gettype($childArr) == "array" && $countChildren == 1) {
		    				$temp[$childkey . "." . key($childArr)] = $childArr[key($childArr)];
		    			}
		    			else if(gettype($childArr) == "array" && $countChildren == 2 
		    				&& array_key_exists("name", $childArr)
		    				&& array_key_exists("value", $childArr)) {
		    				$temp2 = array_merge($temp2, array($childArr["name"] => $childArr["value"]));
		    			}
		    			else {
		    				$temp[$childkey . "." .$i++] = $childArr;
		    			}
		    		}
		    		if (count($temp2) > 0) {
		    			$temp[$childkey] = $temp2;
		    		}
		    		$arr = array_merge($arr, $temp);
	    		}
	    	}
	    	else {
	    		if ($parent != "") {
	    			$arr[$parent . "." . $key] = $value;
	    		}
	    		else {
	    			$arr[$key] = $value;
	    		}
	    	}
	    }
	    return $arr;
	}
	private function is_assoc($array){
	    return array_values($array)!==$array;
	}
}
//  $f = new Flattener();
// // $arr = array("a"=>"b","params"=>array("c","d"));
//  $arr = array("a"=>"b","params"=>array(array("namea"=>"mynamea","vala"=>"myvala"),array("nameb"=>"mynameb","valb"=>"myvalb")));
//  $arr = array("a"=>"b","params"=>array(array("name"=>"mynamea"),array("name"=>"mynameb","value"=>"myvalb")));
// // //$arr = array("a"=>"b","params"=>array("c"=>"cc","d"=>"dd"));
//  print_r($f->flattenJson($arr));

$req = '<?xml version="1.0" encoding="UTF-8"?>
<Request version="1.0">
 <Transaction mode="TEST" requestTimestamp="2008-12-01 11:37:47">
 <Identification>
 <ShortID>2894.9340.3418</ShortID>
 <UUID>MGQTluFAfgvoBpOOs8Uc2eKmj96lFRrC</UUID>
 </Identification>
 <MerchantAccount type="WIRECARD">
 <MerchantID>56500</MerchantID>
 <MerchantName>56500</MerchantName>
 <Password>TestXAPTER</Password>
 <Country>DE</Country>
 </MerchantAccount>
 <Payment type="PA">
 <Amount>37.00</Amount>
 <Currency>EUR</Currency>
 <Descriptor>2894.9340.3418 TPX_order# PSP_A/MER_A/DEFAULT</Descriptor>
 </Payment>
 <CreditCardAccount>
 <Holder>jean le coq</Holder>
 <Verification>123</Verification>
 <Brand>VISA</Brand>
 <Number>4200000000000000</Number>
 <Expiry month="01" year="2018" />
 </CreditCardAccount>
 <Customer>
 <Name>
 <Family>kosel</Family>
 <Given>bobby</Given>
 </Name>
 <Contact>
 <Ip>101.202.011.022</Ip>
 <Email>bob_kosel@mailserver.com</Email>
 <Phone>0049 89 6542123</Phone>
 <Mobile>0049 177 6542123</Mobile>
 </Contact>
 <Address>
 <Country>DE</Country>
 <City>Frankfurt</City>
 <State>DE7</State>
 <Street>Hauptstrasse</Street>
 <Zip>61821</Zip>
 </Address>
 </Customer>
 <Authentication>
 <Eci>05</Eci>
 <Verification>AAACAgSRBklmQCFgMpEGAAAAAAA=</Verification>
 <Xid>CAACCVVUlwCXUyhQNlSXAAAAAAA=</Xid>
 </Authentication>
 </Transaction>
</Request>';
// $xml = simplexml_load_string($req);
// $f = new Flattener();
// print_r($f->flattenXml($xml, $xml->getName()));
?>