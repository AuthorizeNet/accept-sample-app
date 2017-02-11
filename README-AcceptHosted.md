
# Accept Hosted Step-by-Step
Accept Hosted provides a fully hosted payment transaction solution, Authorize.Net takes care of the payment form, the transaction itself and (optionally) the receipt generation.  This example demonstrates using an embedded iFrame to display the page, but you could also use a lightbox style popup iFrame.  See our [developer documentation](http://developer.authorize.net/api/reference/features/accept_hosted.html) for more details.

![Accept Hosted Screenshot](screenshots/AcceptHosted-Tablet.png "Screenshots showing Accept Hosted.")

## Step 1. Create a Secure Form Token

In this step we will request an Accept Hosted form token using the Authorize.Net API, you can try out the call in our sandbox API Explorer here:  http://developer.authorize.net/api/reference/#payment-transactions-get-an-accept-payment-page   
**NOTE: This should be a fully authenticated server-side call for your application, for example if you had a .NET application this call would be in the code-behind .cs files, for a mobile app this could be in your node.js backend, etc**  
In this Accept Sample application you can find the sample code in https://github.com/AuthorizeNet/accept-sample-app/blob/master/getHostedPaymentForm.php

## Step 2. Incorporate Accept Hosted form into your payment flow.

In this step we will embed the payment form in a web page and complete the payment transaction


## Step 3.  Display a custom receipt using the transaction response.  
  
In this step we will receive the payment form response via the iFrameCommunicatorURL and use that response data to present a custom receipt

