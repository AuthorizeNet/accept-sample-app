/**
 * HTML Id's for payerAuthCaller() to pick values off the HTML page to send on the CCA start request
 * 
 * NOTE: The index.html file contains duplicate ID's for the card fields. It looks like the acceptJSModal html was duplicated and the field ID's for the HTML were never update.
 * We may need to adjust the Ids of the HTML for this to work properly
 */
var payerAuthHtmlIds = {
  card: {
    number: 'creditCardNumberPA',
    expiration: {
      year: 'expiryDateYYPA',
      month: 'expiryDateMMPA'
    }
  },
  amount: 'amountPA',
  submitButton: 'submitButton'
};

/**
 * Initalize Songbird.js
 * 
 * When the page loads we should setup Songbird to that by the time the user tries to click the submit button we're already setup
 */
$(function () {
  try {
    // Make sure the Cardinal namespace is available before we set anything up
    if ('Cardinal' in window) {

      // Documentation Step 2 - Optional configuration to enable logging to developer console
      Cardinal.configure({ logging: { level: 'verbose' } });

      // Documentation Step 3 - Initalize Cardinal Cruise
      Cardinal.setup('init', {
        jwt: document.getElementById('cardinalRequestJwt').value
      });

      // Optional event to inform us that Songbird has initialzed properly
      Cardinal.on('payments.setupComplete', function () {
        console.log('Cardinal Cruise is ready to be used');
      });

      // Documentation Step 5
      // Required event that will trigger when any kind of result is reached. Songbird will always end the transaction by calling this method, regardless of the resulting state.
      // This includes failure to initialize
      Cardinal.on('payments.validated', function (data, jwt) {
        try {
          // It is very important to make sure that the signature is verified with our ApiKey before accepting the results. Here we send the JWT to the back end to be verified via Ajax,
          // but in most flows a form would be posting to a new page to complete authorization.
          $.ajax({
            method: 'post',
            url: 'validateJwt.php',
            data: { 'responseJwt': jwt },
            dataType: 'json'
          })
            .done(function (responseData) {
              if (responseData !== undefined && typeof responseData === 'object') {
                if ('ActionCode' in responseData) {
                  switch (responseData.ActionCode) {
                    case "SUCCESS":
                    case "NOACTION":
                      // Success indicates that we got back CCA values we can pass to the gateway
                      // No action indicates that everything worked, but there is no CCA values to worry about, so we can move on with the transaction
                      console.warn('The transaction was completed with no errors', responseData.Payment.ExtendedData);
                      break;
                    case "FAILURE":
                      // Failure indicates the authentication attempt failed
                      console.warn('The authentication attempt failed', responseData.Payment);
                      break;

                    case "ERROR":
                    default:
                      // Error indicates that a problem was encountered at some point in the transaction
                      console.warn('An issue occurred with the transaction', responseData.Payment);
                      break;
                  }
                } else {
                  console.error("Failure while attempting to verify JWT signature: ", responseData)
                }

              } else {
                console.error('Response data was incorrectly formatted: ', responseData);
              }

            })
            .fail(function (xhr, ajaxError) {
              console.log('Connection failure:', ajaxError)
            });


        } catch (validateError) {
          console.error('Failed while processing validate', validateError);
        }
      });

    } else {
      console.error('Cardinal namespace is not available. Please check the script Url.');
    }

  } catch (error) {
    console.error('Cardinal Cruise failed during startup', error);
  }
});

/**
 * Handler to trigger CCA
 * 
 * Here we collect any data we need to complete the CCA request to Cardinal and start authentication. We should not call 'Cardinal.start' if we do not have
 * all the required fields
 */
function payerAuthCaller() {
  try {
    var cardNumber = document.getElementById(payerAuthHtmlIds.card.number).value,
      cardExpMonth = document.getElementById(payerAuthHtmlIds.card.expiration.month).value,
      cardExpYear = document.getElementById(payerAuthHtmlIds.card.expiration.year).value,
      orderObject;

    // Do whatever field validation we want to make sure the form is properly filled out before we run the CCA call
    if (isCardFieldValid(cardNumber) && isCardFieldValid(cardExpMonth) && isCardFieldValid(cardExpYear)) {
      // Expiration fields in the Cardinal Cruise API are integers
      if (typeof cardExpMonth === 'string') {
        cardExpMonth = parseInt(cardExpMonth, 10);
      }
      if (typeof cardExpYear === 'string') {
        // Cardinal Cruise is expecting a 2 digit Expiration Year, so if we have a 4 digit year, grab the last 2 digits
        if(cardExpYear.length === 4){
          cardExpYear = cardExpYear.substring(2,4);
        }
        cardExpYear = parseInt(cardExpYear, 10);        
      }

      // Assemble the order object from the input fields on the page.
      orderObject = {
        OrderDetails: {
          Amount: document.getElementById(payerAuthHtmlIds.amount).value,
          CurrencyCode: "840"
        },
        Consumer: {
          Account: {
            AccountNumber: cardNumber,
            ExpirationMonth: cardExpMonth,
            ExpirationYear: cardExpYear
          }
        }
      }

      // Documentation Step 4
      // Start the CCA transaction, passing the order data we collected on the page
      Cardinal.start('cca', orderObject);
    } else {
      console.log('Card Number was not a valid number, skipping CC');
    }
  } catch (error) {
    console.error('Error while trying to start CCA', error);
  }
}

/**
 * Simple helper for field verification before we start CCA. 
 * @param {string} fieldValue - The value to check for validity
 * @returns {boolean} is the field value valid to start CCA
 */
function isCardFieldValid(fieldValue) {
  if (fieldValue != undefined && fieldValue.length > 0) {
    return true;
  }
  return false;
}
