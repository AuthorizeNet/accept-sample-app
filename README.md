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
+ Browse the application (**index.php**) over HTTPS connection.
+ To "login" use an existing customer profile ID or create a new one (http://developer.authorize.net/api/reference/index.html#customer-profiles-create-customer-profile)
+ Payment forms are shown in the same page and Shipping forms are handled in a separate  modal popup. Any of the types can be choosen to display the forms.
+ 
  
## Examples Included

### Accept Customer
Accept Customer is the new name for Hosted CIM, part of our [Customer Profiles API](http://developer.authorize.net/api/reference/features/customer_profiles.html)
  
The sample application shows how to:  
1. Incorporate the Manage Customer hosted page into your application (Profiles tab).  
2. Embed the hosted "Add/Edit Payment" page into your application as an iFrame (Payments tab).  
3. Pop up the hosted "Add/Edit Shipping" page in a light-box mode (Shipping tab).  
  
  
### Accept.js
Accept.js is a new integration option which allows you to leverage the full power of the Authorize.Net API while avoiding the PCI burden of credit card information hitting your servers.  Documentation coming soon.  
  
The sample application shows how to:  
1.  Incorporate the Accept.js library into your existing payment flow (Home page, PAY button)

