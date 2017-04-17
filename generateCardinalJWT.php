<?php

require 'JWT.php';

$APIKEY = getenv("CARDINAL_API_KEY");
$APIID = getenv("CARDINAL_API_ID");
$ORGUNIT = getenv("CARDINAL_ORG_UNIT");

function generateCardinalJwt($jwtId, $apiKeyId, $apiKey, $orgUnitId, $orderNumber)
{
	$currentTime = time();
	$expireTime = 3600; // expiration in seconds - this equals 1hr
	
	$orderDetails = array(
		"OrderDetails" => array(
			"OrderNumber" =>  $orderNumber
		)
	);

	$token = array();
	$token['jti'] = $jwtId;
	$token['iss'] = $apiKeyId;  // API Key Identifier
	$token['iat'] = $currentTime; // JWT Issued At Time
	$token['exp'] = $currentTime + $expireTime; // JWT Expiration Time
	$token['OrgUnitId'] = $orgUnitId; // Merchant's OrgUnit
	$token['Payload'] = $orderDetails;
	$token['ObjectifyPayload'] = true;

	return JWT::encode($token, $apiKey, 'HS256');
}

$cardinalRequestJwt = generateCardinalJwt(
		'MYJWT', $APIID, $APIKEY, $ORGUNIT, 'ORDER-' . strval(mt_rand(1000, 10000))
	);

?>