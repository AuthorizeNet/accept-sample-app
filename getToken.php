<?php
error_reporting(E_ERROR);
$param = parse_ini_file("config.txt");
$xmlStr = <<<XML
﻿<?xml version="1.0" encoding="utf-8"?>
<getHostedProfilePageRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
<merchantAuthentication></merchantAuthentication>
<customerProfileId></customerProfileId>
<hostedProfileSettings>
<setting><settingName>hostedProfileReturnUrl</settingName></setting>
<setting><settingName>hostedProfileIFrameCommunicatorUrl</settingName></setting>
<setting><settingName>hostedProfileReturnUrlText</settingName><settingValue>Back to Confirmation Page</settingValue></setting>
<setting><settingName>hostedProfilePageBorderVisible</settingName><settingValue>true</settingValue></setting>
</hostedProfileSettings>
</getHostedProfilePageRequest>
XML;
$xml = new SimpleXMLElement($xmlStr);
$xml->merchantAuthentication->addChild('name',$param['name']);
$xml->merchantAuthentication->addChild('transactionKey',$param['transactionKey']);
$xml->customerProfileId = $param['customerProfileId'];

$xml->hostedProfileSettings->setting[0]->addChild('settingValue',$param['Home']."return.html");
$xml->hostedProfileSettings->setting[1]->addChild('settingValue',$param['Home']."iCommunicator.html");

$url = "https://downloadvposcad.labwebapp.com/xml/v1/request.api";

    try{	//setting the curl parameters.
        $ch = curl_init();
        if (FALSE === $ch)
        	throw new Exception('failed to initialize');
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_PROXY, "http://internet.visa.com:80");
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        $content = curl_exec($ch);
        $response = new SimpleXMLElement($content);
        if (FALSE === $content)
        	throw new Exception(curl_error($ch), curl_errno($ch));
        curl_close($ch);

    }catch(Exception $e) {
    	trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
	}
?>