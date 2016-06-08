<?php

$transRequestXmlStr=<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
      <merchantAuthentication>
         <name>assignAPI_LOGIN_ID</name>
         <transactionKey>assignAPI_TRANSACTION_KEY</transactionKey>
      </merchantAuthentication>
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
         <retail>
            <marketType>0</marketType>
            <deviceType>0</deviceType>
         </retail>
      </transactionRequest>
</createTransactionRequest>
XML;

$transRequestXml=new SimpleXMLElement($transRequestXmlStr);

$transRequestXml->merchantAuthentication->name='48uDp4QBA';//getenv('prod_api_login_id');
$transRequestXml->merchantAuthentication->transactionKey='947j5q7tBgmAS378';//getenv('prod_transaction_key');

$transRequestXml->transactionRequest->amount=$_POST['amount'];
$transRequestXml->transactionRequest->payment->opaqueData->dataDescriptor=$_POST['dataDesc'];
$transRequestXml->transactionRequest->payment->opaqueData->dataValue=$_POST['dataValue'];

$url="https://api.authorize.net/xml/v1/request.api";

try{	//setting the curl parameters.
        $ch = curl_init();
        if (FALSE === $ch)
        	throw new Exception('failed to initialize');
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $transRequestXml->asXML());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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