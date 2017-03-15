<?php
require_once(__DIR__ . "/Transmission.php");
class TcpTransmission extends Transmission {
    public function transmit(&$response) {
    	$parts = explode(":", $this->endpoint);
    	$ip = $parts[0];
    	$port = $parts[1];
    	return "ip: " . $ip . " port: " . $port . "\n";

    	///TODO:
        /* Create a TCP/IP socket. */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
		    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		} else {
		    echo "OK.\n";
		}

		echo "Attempting to connect to '$ip' on port '$port'...";
		$result = socket_connect($socket, $ip, $port);
		if ($result === false) {
		    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
		} else {
		    echo "OK.\n";
		}

		$in = "HEAD / HTTP/1.1\r\n";
		$in .= "Host: www.example.com\r\n";
		$in .= "Connection: Close\r\n\r\n";
		//$in = $this->payload;
		$out = '';

		echo "Sending HTTP HEAD request...";
		socket_write($socket, $in, strlen($in));
		echo "OK.\n";

		echo "Reading response:\n\n";
		while ($out = socket_read($socket, 2048)) {
		    echo $out;
		}

		echo "Closing socket...";
		socket_close($socket);
		echo "OK.\n\n";
		return "got there";
    }
}
?>