
/**
 * HTML Id's for conversion to pick values off the HTML page to send to the conversion tracker API
 * 
 */
var conversionHtmlIds = {
  amount: 'amountPA',
  submitButton: 'submitButton'
};

/**
 * Initalize conversion.js
 * 
 * When the page loads we should
 */
$(function () {
  try {
    // Make sure the Conversion namespace is available before we set anything up
    if ('Conversion' in window) {

      // Call API to register "START" and to issue the unique conversion token
      
      // If this token is never received by a payment API, this is abandonment
      
      // If this token is received by our payments API, then it will log the conversion, with the time difference. 
      // EVerything else is an attribute of the payment transaction itself,
      // e.g. was the authorization successful, was it held for fraud, what was the payment type

    } else {
      console.error('Conversion namespace is not available. Please check the script Url.');
    }

  } catch (error) {
    console.error('Conversion failed during startup', error);
  }
});
