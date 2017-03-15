<?php
require_once(__DIR__ . "/Transmission.php");
class HttpTransmission extends Transmission {
    private $headers = array();
    private $basicAuth = "";
    public function addHeader($key, $value) {
        $this->headers[] = $key . ": " . $value;
    }
    public function addBasicAuth($username, $password) {
        $this->basicAuth = $username . ":" . $password;
    }
    public function transmit(&$response) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if (isset($this->headers) && count($this->headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        if (isset($this->basicAuth) && !empty($this->basicAuth)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->basicAuth);
        }

        $responseData = curl_exec($ch);
        
        if($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            $response = "cURL error ({$errno}): {$error_message}";
            curl_close($ch);
            return false;
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpintcode = intval($httpcode);
        curl_close($ch);
        $response = array();
        $response["body"] = $responseData;
        $response["httpResponseCode"] = $httpcode;

        if ($httpintcode >= 200 && $httpintcode <= 299) {
            return true;    
        }
        else {
            return false;
        }
    }
}
?>