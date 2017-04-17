<?php

require 'JWT.php';
header('Content-Type: application/json');

$jsonResponse = array();
$jsonResponse['ErrorNumber'] = '1001';
$jsonResponse['ErrorDescription'] = 'An error has occurred.';

try{
	$cardinalResponseJWT = $_POST['responseJwt'];

	if(isset($cardinalResponseJWT)) {
		$decodedJwt = (array) JWT::decode($cardinalResponseJWT, getenv("CARDINAL_API_KEY"), true);
		$jsonResponse = $decodedJwt['Payload']; 
	} else {
		$jsonResponse['ErrorDescription'] = 'Unable to locate the responseJwt in the POST Data.';
	}
} catch (Exception $e) {
	// We defaulted to an error response above.
	// $jsonResponse['ErrorDescription'] = $e->getMessage();
}

echo json_encode($jsonResponse);

?>