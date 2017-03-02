<?php
$profileReq = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<getCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
<merchantAuthentication></merchantAuthentication>
<customerProfileId></customerProfileId>
</getCustomerProfileRequest>
XML;
$xml = new SimpleXMLElement($profileReq);
$xml->merchantAuthentication->addChild('name',$loginId);
$xml->merchantAuthentication->addChild('transactionKey',$transactionKey);
$xml->customerProfileId = $cpid;

try{	//setting the curl parameters.
        $ch = curl_init();
        if (FALSE === $ch)
        	throw new Exception('failed to initialize');
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
	// The following two curl SSL options are set to "false" for ease of development/debug purposes only.
	// Any code used in production should either remove these lines or set them to the appropriate
	// values to properly use secure connections for PCI-DSS compliance.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	//for production, set value to true or 1
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	//for production, set value to 2
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        $content = curl_exec($ch);
        $profileResponse = new SimpleXMLElement($content);
        if (FALSE === $content)
        	throw new Exception(curl_error($ch), curl_errno($ch));
        curl_close($ch);

    }catch(Exception $e) {
    	trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
	}

?>
