<?php
session_start();

// Security: Verify user is authenticated via server-side session
if(!isset($_SESSION['authenticated_cpid'])){
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized', 'message' => 'Authentication required']);
    exit;
}

error_reporting(E_ERROR);

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");


//$param = parse_ini_file("config.txt");
$xmlStr = <<<XML
﻿<?xml version="1.0" encoding="utf-8"?>
<getHostedProfilePageRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
<merchantAuthentication></merchantAuthentication>
<customerProfileId></customerProfileId>
<hostedProfileSettings>
<setting><settingName>hostedProfileReturnUrl</settingName></setting>
<setting><settingName>hostedProfileIFrameCommunicatorUrl</settingName></setting>
<setting><settingName>hostedProfileReturnUrlText</settingName><settingValue>Back to Confirmation Page</settingValue></setting>
<setting><settingName>hostedProfilePageBorderVisible</settingName><settingValue>false</settingValue></setting>
<setting><settingName>hostedProfileBillingAddressOptions</settingName><settingValue>showBillingAddress</settingValue></setting>
<!--<setting><settingName>hostedProfileManageOptions</settingName><settingValue>showPayment</settingValue></setting> -->
</hostedProfileSettings>
</getHostedProfilePageRequest>
XML;
$xml = new SimpleXMLElement($xmlStr);

$loginId = getenv("API_LOGIN_ID");
$transactionKey = getenv("TRANSACTION_KEY");

$xml->merchantAuthentication->addChild('name', $loginId);
$xml->merchantAuthentication->addChild('transactionKey', $transactionKey);

// Security: Read customer profile ID from server-side session (not user-controllable cookie)
// This prevents authenticated attackers from accessing other users' profiles by modifying the cookie
$cpid = $_SESSION['authenticated_cpid'];

$xml->customerProfileId = $cpid;
$xml->hostedProfileSettings->setting[0]->addChild('settingValue', curPageURL()."return.html");
$xml->hostedProfileSettings->setting[1]->addChild('settingValue', curPageURL()."IFrameCommunicator.html");

$url = "https://apitest.authorize.net/xml/v1/request.api";

try {    //setting the curl parameters.
        $ch = curl_init();
    if (false === $ch) {
        throw new Exception('failed to initialize');
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    // SSL certificate verification enabled for secure connections and PCI-DSS compliance
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);    //verify SSL certificate
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    //verify certificate matches hostname
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);    //enforce TLS 1.2 minimum
    curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
    $content = curl_exec($ch);
    $response = new SimpleXMLElement($content);
    if (false === $content) {
        throw new Exception(curl_error($ch), curl_errno($ch));
    }
    curl_close($ch);
} catch (Exception $e) {
        trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
}

function curPageURL()
{
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }

     $pageLocation = str_replace('index.php', '', $pageURL);

     return $pageLocation;
}
