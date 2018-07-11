
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

      // Documentation Step 2 - Optional configuration to enable logging to developer console
      Conversion.configure({ logging: { level: 'verbose' } });

    } else {
      console.error('Conversion namespace is not available. Please check the script Url.');
    }

  } catch (error) {
    console.error('Conversion failed during startup', error);
  }
});
