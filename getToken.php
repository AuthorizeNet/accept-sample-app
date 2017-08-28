<?php
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
if (isset($_COOKIE['cpid'])) {
    $cpid = $_COOKIE['cpid'];
} else if (isset($_COOKIE['temp_cpid'])) {
    $cpid = $_COOKIE['temp_cpid'];
}

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
    // The following two curl SSL options are set to "false" for ease of development/debug purposes only.
    // Any code used in production should either remove these lines or set them to the appropriate
    // values to properly use secure connections for PCI-DSS compliance.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    //for production, set value to true or 1
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    //for production, set value to 2
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
