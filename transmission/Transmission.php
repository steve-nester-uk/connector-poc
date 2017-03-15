<?php
abstract class Transmission {
	protected $endpoint = "";
	protected $payload = "";
	public function __construct($endpoint, $payload) {
        $this->endpoint = $endpoint;
        $this->payload = $payload;
    }
    public abstract function transmit(&$response);
}
?>