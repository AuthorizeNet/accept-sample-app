<?php

$transReqJson=json_decode($_POST['request']);

$transReqJson->createTransactionRequest->merchantAuthentication->name='48uDp4QBA';//getenv('prod_api_login_id');
$transReqJson->createTransactionRequest->merchantAuthentication->transactionKey='947j5q7tBgmAS378';//getenv('prod_transaction_key');
$transReqJson->createTransactionRequest->transactionRequest->currencyCode='USD';
$transReqJson->createTransactionRequest->transactionRequest->transactionType='authCaptureTransaction';

$url="https://api.authorize.net/xml/v1/request.api";
//$url="https://downloadvposCED.labwebapp.com/xml/v1/request.api";

try{	//setting the curl parameters.
        $ch = curl_init();
        if (FALSE === $ch)
        	throw new Exception('failed to initialize');
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transReqJson));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_PROXY, "http://internet.visa.com:80");
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        $content = curl_exec($ch);
        if (FALSE === $content)
        	throw new Exception(curl_error($ch), curl_errno($ch));
        curl_close($ch);
		echo $content;
    }catch(Exception $e) {
    	trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
	}

?>