
# Accept Hosted Step-by-Step
Accept Hosted provides a fully hosted payment transaction solution, Authorize.Net takes care of the payment form, the transaction itself and (optionally) the receipt generation.  This example demonstrates using an embedded iFrame to display the page, but you could also use a lightbox style popup iFrame.  See our [developer documentation](http://developer.authorize.net/api/reference/features/accept_hosted.html) for more details.  
  
![Accept Hosted Screenshot](screenshots/AcceptHosted-Tablet.PNG "Screenshots showing Accept Hosted.")

## Step 1. Create a Secure Form Token

In this step we will request an Accept Hosted form token using the Authorize.Net API.  
You can try out the call in our sandbox API Explorer here:  http://developer.authorize.net/api/reference/#payment-transactions-get-an-accept-payment-page   
  
**NOTE: This should be a fully authenticated server-side call for your application, for example if you had a .NET application this call would be in the code-behind .cs files, for a mobile app this could be in your node.js backend, etc**  
  
In this Accept Sample application you can find the sample code in https://github.com/AuthorizeNet/accept-sample-app/blob/master/getHostedPaymentForm.php
  
See below for an example of the output from getHostedPaymentPage method.  The important response data is the token element as you will use this for the next step:
  
````xml
<getHostedPaymentPageResponse>
  <messages>
    <resultCode>Ok</resultCode>
    <message>
      <code>I00001</code>
      <text>Successful.</text>
    </message>
  </messages>
  <token>
66oTeT0c6/r39pwCcfkNpcjF+I01JUvaULhkE3qWzfzOYbXu0xGbd+Toc5FGTK2WZFP0GbMRbLD7I3tkW2vLEwUkBSMn9WZcuCkNF/jzXi19To1sk+Z8K7aOHs04dhTF1el1qLC+eeWcOsZTYA8GioV6XTNMTQ7j6lLp9HZtci3541iRJET61/VMvhp1iTc5qbWJIAF/DwqxWS5JReAQP9NZE6hvwxJ2s4CpgQwAB8IidWsSpTE+n13otQd/DciRwsZCnz1oXmcqjKoaBoNm3HO0ZI2lDF13sJtBPa6wPB1gbIi/SoPGqY56Qu26VTHGTNlh6tXpda7q/L8sJtBw0oPLXqvhGthBD5JIzFOfQOvo24cgF6B88v2xOnYRkZPh+YeyngQ3z7d4vuouFrQkzQxbvoScA/JRbR09Z69CQ1xZyB0GZYBcPg8QPiWyhQ4sycS10W+hL5BeKqb6Sud1rIcZVmX7/YodESBjPUarjbLbLMJxDXtBobJ00UjNl2o0cFWllujS91qQCa3I2S0EO65m+5WH4YuFbYafoUSj7eCgI4Bm9jIYTYQpgJQ8GNPC1WcqDMhMxsr9R0PXj4tbUqwuizRyFQU7E86Jkhu/lGgvOUmKU/GobNZbgpTsvloMMrS39a/w6mk036Jz42CorSomAqTf9dEFqwA0T6KmEWcm/XplpaEEeoARHdX0x+n/GL5niAp3blDhbJubTyQDPQ6jqFP4NifhICcCFV2oVLcYhG1Hr70A+Y0HA+V58czHysQDu8RyV97Ssz7FY8nChFQXqpHzIhMdygliKx8+sizarzdDbX3KDjK5QhsYDk13xU4epF5X9L6lHMokEOuJ9UAoXZfxmuy3Uhs8tBkya3A+XFi+fIn/ilrJwOyMtMdm8iBVgs+CstNzH49rCWCrU5en4Yjt93tSqrCuNouvCbY=.89nE4Beh
  </token>
</getHostedPaymentPageResponse>
````
  
## Step 2. Incorporate Accept Hosted form into your payment flow.

In this step we will embed the payment form in a web page using an iFrame and complete the payment transaction.  
  
See our sample code, https://github.com/AuthorizeNet/accept-sample-app/blob/master/index.php 
  
  Here's an example of the iFrame which will contain the Accept Hosted page:
  ````html
  <iframe id="load_payment" class="embed-responsive-item" name="load_payment" width="100%" height="650px" frameborder="0" scrolling="no" hidden="true">
			</iframe>
 ````
  
  You could then load the Accept Hosted from into your iFrame like this:
  ````html
  <form id="send_hptoken" action="https://test.authorize.net/payment/payment" method="post" target="load_payment" >
				<input type="hidden" name="token" value="<?php echo $hostedPaymentResponse->token ?>" />
			</form>
  ````
  **NOTE:  The token passed to Accept Hosted is generated in Step 1 above.**
    

## Step 3.  Display a custom receipt using the transaction response.  
  
In this step we will receive the payment form response via the iFrameCommunicatorURL and use that response data to present a custom receipt

