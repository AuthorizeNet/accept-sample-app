<?php

error_reporting(E_ERROR);
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

// Validation URL is passed in the request
$validationUrl=$_POST['validationUrl'];


// JSON Payload 
$validationPayload = '{"merchantIdentifier":"merchant.authorize.net.test.dev15","domainName":"applepay-sample.azurewebsites.net","displayName":"MyStore"}';

print $validationPayload;
?>
