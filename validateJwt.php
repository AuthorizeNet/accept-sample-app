<?php

require 'JWT.php';
header('Content-Type: application/json');

$jsonResponse = array();
$jsonResponse['ErrorNumber'] = '1001';
$jsonResponse['ErrorDescription'] = 'An error has occurred.';

try{
	// SAMPLE ONLY:
	// `responseJwt` is the Cardinal Cruise response JWT (3 dot-separated
	// Base64URL segments). Gate obviously-malformed values before the crypto
	// path. JWT::decode() will reject malformed input too, but a cheap
	// regex+length filter keeps this endpoint cheap and predictable.
	$cardinalResponseJWT = filter_input(INPUT_POST, 'responseJwt',
		FILTER_VALIDATE_REGEXP,
		['options' => ['regexp' => '/^[A-Za-z0-9_\-]{1,4096}\.[A-Za-z0-9_\-]{1,4096}\.[A-Za-z0-9_\-]{1,4096}$/']]);

	if ($cardinalResponseJWT) {
		$decodedJwt = (array) JWT::decode($cardinalResponseJWT, getenv("CARDINAL_API_KEY"), true);
		$jsonResponse = $decodedJwt['Payload'];
	} else {
		$jsonResponse['ErrorDescription'] = 'Unable to locate or validate the responseJwt in the POST Data.';
	}
} catch (Exception $e) {
	// We defaulted to an error response above.
	// $jsonResponse['ErrorDescription'] = $e->getMessage();
}

echo json_encode($jsonResponse);

?>