# accept-sample-app
This application provides examples of how to use the Authorize.Net Accept products to integrate secure payment acceptance into your applications.

## How to Use the Sample App

+ Host the sample app in any web server supporting PHP like IIS (with PHP) or XAMPP (Apache web server with PHP). __HTTPS (SSL) must be enabled for your website.__
+ Edit the **Config.txt** to put the correct value for profile ID.
+ Set your authentication values by setting the ENVIRONMENT variables API_LOGIN_ID and TRANSACTION_KEY.  For example, in httpd.conf:
````
SetEnv API_LOGIN_ID your_id
SetEnv TRANSACTION_KEY your_key
````
+ Browse the application (**index.php**) over HTTPS connection.
+ Payment forms are shown in the same page and Shipping forms are handled in a separate  modal popup. Any of the types can be choosen to display the forms.
