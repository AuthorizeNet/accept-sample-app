<?php
session_start();

// Security: Verify user is authenticated via server-side session
if(!isset($_SESSION['authenticated_cpid'])){
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized', 'message' => 'Authentication required']);
    exit;
}

// Security: Validate CSRF token (constant-time compare)
if(!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Forbidden', 'message' => 'Invalid CSRF token']);
    exit;
}

// SAMPLE ONLY:
// This sample validates the posted amount for demonstration purposes.
// In a production application, do NOT rely on the client-submitted amount.
// Instead, calculate the amount server-side from the authenticated user's
// cart or order. The pattern below shows the minimum input-validation
// shape (filter_input + range check + canonical decimal form).
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
if ($amount === false || $amount === null || $amount <= 0 || $amount > 10000) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid transaction amount. Amount must be between $0.01 and $10,000.00']);
    exit;
}
$amountCanonical = number_format($amount, 2, '.', '');

// SAMPLE ONLY:
// `dataDescriptor` selects which Authorize.Net wallet/integration this
// payment came from. In production, accept descriptors only via a
// server-side allowlist tied to the merchant's enabled wallets.
$allowedDataDesc = [
    'COMMON.ACCEPT.INAPP.PAYMENT',  // Accept.js
    'COMMON.APPLE.INAPP.PAYMENT',   // Apple Pay on the Web
    'COMMON.VCO.ONLINE.PAYMENT',    // Visa Checkout
];
$dataDesc = filter_input(INPUT_POST, 'dataDesc', FILTER_DEFAULT) ?? '';
if (!in_array($dataDesc, $allowedDataDesc, true)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid dataDescriptor.']);
    exit;
}

// SAMPLE ONLY:
// `dataValue` is the Accept.js / wallet opaque-token payload. It is short,
// JSON/Base64-shaped text — never free-form HTML/SQL. Pin a sane size and
// character class. In production, also pin to your wallet provider's exact
// token grammar.
$dataValue = filter_input(INPUT_POST, 'dataValue', FILTER_DEFAULT) ?? '';
if ($dataValue === '' || strlen($dataValue) > 8192 || !preg_match('/^[A-Za-z0-9+\/=._:{}",\s\-]+$/', $dataValue)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid dataValue.']);
    exit;
}

// SAMPLE ONLY:
// `callId` is only present for the Visa Checkout branch and is a numeric
// call identifier. In production, validate against your wallet provider's
// exact call-id grammar.
$callId = null;
if ($dataDesc === 'COMMON.VCO.ONLINE.PAYMENT') {
    $callId = filter_input(INPUT_POST, 'callId',
        FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[A-Za-z0-9_\-]{1,64}$/']]);
    if (!$callId) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Bad Request', 'message' => 'Invalid Visa Checkout callId.']);
        exit;
    }
}

$transRequestXmlStr=<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
      <merchantAuthentication></merchantAuthentication>
      <transactionRequest>
         <transactionType>authCaptureTransaction</transactionType>
         <amount>assignAMOUNT</amount>
         <currencyCode>USD</currencyCode>
         <payment>
            <opaqueData>
               <dataDescriptor>assignDD</dataDescriptor>
               <dataValue>assignDV</dataValue>
            </opaqueData>
         </payment>
      </transactionRequest>
</createTransactionRequest>
XML;

$transRequestXml=new SimpleXMLElement($transRequestXmlStr);

$loginId = getenv("API_LOGIN_ID");
$transactionKey = getenv("TRANSACTION_KEY");

$transRequestXml->merchantAuthentication->addChild('name',$loginId);
$transRequestXml->merchantAuthentication->addChild('transactionKey',$transactionKey);

// SAMPLE ONLY:
// Use the validated locals from above (NOT raw $_POST) when assigning into
// the outbound XML. This guarantees the value the gate validated is the
// exact value sent on the wire.
$transRequestXml->transactionRequest->amount = $amountCanonical;
$transRequestXml->transactionRequest->payment->opaqueData->dataDescriptor = $dataDesc;
$transRequestXml->transactionRequest->payment->opaqueData->dataValue = $dataValue;

if ($dataDesc === 'COMMON.VCO.ONLINE.PAYMENT') {
    $transRequestXml->transactionRequest->addChild('callId', $callId);
}

$url="https://apitest.authorize.net/xml/v1/request.api";

//print_r($transRequestXml->asXML());

try{	//setting the curl parameters.
        $ch = curl_init();
        if (FALSE === $ch)
        	throw new Exception('failed to initialize');
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $transRequestXml->asXML());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
	// SSL certificate verification enabled for secure connections and PCI-DSS compliance
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);	//verify SSL certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);	//verify certificate matches hostname
	curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);	//enforce TLS 1.2 minimum
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        $content = curl_exec($ch);
        if (FALSE === $content)
        	throw new Exception(curl_error($ch), curl_errno($ch));
        curl_close($ch);
		
		$xmlResult=simplexml_load_string($content);

		$jsonResult=json_encode($xmlResult);
		
		echo $jsonResult;
		
    }catch(Exception $e) {
    	trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
	}

?>
