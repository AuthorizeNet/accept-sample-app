# Accept Sample Application
This application provides examples of how to use the Authorize.Net Accept products to integrate secure payment acceptance into your applications.

![Accept Screenshots](screenshots/AcceptTrioScreenShots.png "Screenshots showing the Accept hosted forms.")

## How to Use the Sample App
+ Clone or download this repo
+ Host the sample app in any web server supporting PHP like IIS (with PHP) or XAMPP (Apache web server with PHP). __HTTPS (SSL) must be enabled for your website.__
+ Set your authentication values by setting the ENVIRONMENT variables API_LOGIN_ID and TRANSACTION_KEY.  For example, in httpd.conf:
````
SetEnv API_LOGIN_ID your_id
SetEnv TRANSACTION_KEY your_key
````
For IIS, you could set these in FastCGI Settings -> Environment Variables

+ Update acceptJSCaller.js's function, acceptJSCaller(), with your authentication values (clientKey, apiLoginID)
+ Browse the application (**index.php**) over HTTPS connection.
+ To "login" use an existing customer profile ID or create a new one (http://developer.authorize.net/api/reference/index.html#customer-profiles-create-customer-profile)
+ Payment forms are shown in the same page and Shipping forms are handled in a separate modal popup. Any of the types can be chosen to display the forms.

  
## Examples Included

### Accept Customer
Accept Customer is the new name for Hosted CIM, part of our [Customer Profiles API](http://developer.authorize.net/api/reference/features/customer_profiles.html)
  
The sample application shows how to:  
1. Incorporate the Manage Customer hosted page into your application (Profiles tab).  
2. Embed the hosted "Add/Edit Payment" page into your application as an iFrame (Payments tab).  
3. Pop up the hosted "Add/Edit Shipping" page in a light-box mode (Shipping tab).  
  
  
### Accept.js
Accept.js is a new integration option which allows you to leverage the full power of the Authorize.Net API while avoiding the PCI burden of credit card information hitting your servers.  See our [developer documentation](http://developer.authorize.net/api/reference/features/acceptjs.html) for more details.  
  
The sample application shows how to:  
1.  Incorporate the Accept.js library into your existing payment flow (Home page, PAY button)  


### Accept Hosted
Accept Hosted provides a fully hosted payment transaction solution, Authorize.Net takes care of the payment form, the transaction itself and (optionally) the receipt generation.  We have a Step-by-Step guide to the sample implementation here : https://github.com/AuthorizeNet/accept-sample-app/blob/master/README-AcceptHosted.md

The sample application shows how to:
1.  Request an Accept Hosted form token using the Authorize.Net API (GetHostedPaymentForm)  
2.  Incorporate Accept Hosted into your existing payment flow (Pay tab)  
3.  Display a custom receipt using the transaction response.  
  

### Apple Pay On The Web
Authorize.Net supports Apple Pay on the Web in addition to our in-app Apple Pay Support.  

![Apple Pay Screenshot](screenshots/apple-pay.png "Screenshots showing Apple Pay on the Web.")

In this sample we demonstrate how to:  

1.  Integrate with the ApplePay.js library  
2.  Validate your merchant identity from your server.  
3.  Complete the transaction by passing the Apple Pay payment data in the Authorize.Net createTransaction API.  

Please note that you will need to have a merchant ID set up with Apple as described in the Apple Pay documentation https://developer.apple.com/reference/applepayjs/

