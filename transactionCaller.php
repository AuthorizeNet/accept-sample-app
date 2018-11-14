<?php

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

$transRequestXml->transactionRequest->amount=$_POST['amount'];
$transRequestXml->transactionRequest->payment->opaqueData->dataDescriptor=$_POST['dataDesc'];
$transRequestXml->transactionRequest->payment->opaqueData->dataValue=$_POST['dataValue'];

if($_POST['dataDesc'] === 'COMMON.VCO.ONLINE.PAYMENT')
{
    $transRequestXml->transactionRequest->addChild('callId',$_POST['callId']);  
}


if(isset($_POST['paIndicator'])){
    //$transRequestXml->transactionRequest->addChild('cardholderAuthentication');
    //$transRequestXml->transactionRequest->cardholderAuthentication->addChild('authenticationIndicator',$_POST['paIndicator']);
    //$transRequestXml->transactionRequest->cardholderAuthentication->addChild('cardholderAuthenticationValue',$_POST['paValue']);
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
	// The following two curl SSL options are set to "false" for ease of development/debug purposes only.
	// Any code used in production should either remove these lines or set them to the appropriate
	// values to properly use secure connections for PCI-DSS compliance.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	//for production, set value to true or 1
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	//for production, set value to 2
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
