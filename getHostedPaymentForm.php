<?php

$xmlStr = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<getHostedPaymentPageRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
    <merchantAuthentication></merchantAuthentication>
    <transactionRequest>
        <transactionType>authCaptureTransaction</transactionType>
        <amount>0.50</amount>
        <order>
            <invoiceNumber>INV-12345</invoiceNumber>
            <description>Product Description</description>
        </order>
        <poNumber>456654</poNumber>
        <customerIP>192.168.1.1</customerIP>
    </transactionRequest>
    <hostedPaymentSettings>
        <setting>
            <settingName>hostedPaymentIFrameCommunicatorUrl</settingName>
        </setting>
        <setting>
            <settingName>hostedPaymentButtonOptions</settingName>
            <settingValue>{"text": "Pay"}</settingValue>
        </setting>
        <setting>
            <settingName>hostedPaymentReturnOptions</settingName>
        </setting>
        <setting>
            <settingName>hostedPaymentOrderOptions</settingName>
            <settingValue>{"show": false}</settingValue>
        </setting>
        <setting>
            <settingName>hostedPaymentPaymentOptions</settingName>
            <settingValue>{"cardCodeRequired": true}</settingValue>
        </setting>
        <setting>
            <settingName>hostedPaymentBillingAddressOptions</settingName>
            <settingValue>{"show": true, "required":true}</settingValue>
        </setting>
        <setting>
            <settingName>hostedPaymentShippingAddressOptions</settingName>
            <settingValue>{"show": false, "required":false}</settingValue>
        </setting>
        <setting>
            <settingName>hostedPaymentSecurityOptions</settingName>
            <settingValue>{"captcha": false}</settingValue>
        </setting>
        <setting>
            <settingName>hostedPaymentStyleOptions</settingName>
            <settingValue>{"bgColor": "green"}</settingValue>
        </setting>
        <setting>
            <settingName>hostedPaymentCustomerOptions</settingName>
            <settingValue>{"showEmail": true, "requiredEmail":true}</settingValue>
        </setting>
    </hostedPaymentSettings>
</getHostedPaymentPageRequest>
XML;
$xml = simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOWARNING);
// $xml = new SimpleXMLElement($xmlStr);
$xml->merchantAuthentication->addChild('name', getenv('API_LOGIN_ID'));
$xml->merchantAuthentication->addChild('transactionKey', getenv('TRANSACTION_KEY'));

$commUrl = json_encode(array('url' => thisPageURL()."IFrameCommunicator.html" ), JSON_UNESCAPED_SLASHES);
$xml->hostedPaymentSettings->setting[0]->addChild('settingValue', $commUrl);

$retUrl = json_encode(array("showReceipt" => false , 'url' => thisPageURL()."return.html", "urlText"=>"Continue to site", "cancelUrl" => thisPageURL()."return.html", "cancelUrlText" => "Cancel" ), JSON_UNESCAPED_SLASHES);
$xml->hostedPaymentSettings->setting[2]->addChild('settingValue', $retUrl);

$url = "https://apitest.authorize.net/xml/v1/request.api";

try {   //setting the curl parameters.
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
    //curl_setopt($ch, CURLOPT_PROXY, 'userproxy.visa.com:80');
    $content = curl_exec($ch);
    $content = str_replace('xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd"', '', $content);

    $hostedPaymentResponse = new SimpleXMLElement($content);
    if (false === $content) {
            throw new Exception(curl_error($ch), curl_errno($ch));
    }
    curl_close($ch);
} catch (Exception $e) {
        trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
}

function thisPageURL()
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
