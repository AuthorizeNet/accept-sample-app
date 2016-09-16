if (window.ApplePaySession) {
   var merchantIdentifier = 'ApplePayDemoTestDev15';
   var promise = ApplePaySession.canMakePaymentsWithActiveCard(merchantIdentifier);
   promise.then( function (canMakePayments) {
      if (canMakePayments){
      	console.log("Apple Pay Payment Available");
      	$("#applePayButton").prop('disabled', false);
      }else{
      	console.log("Apple pay is available but not activated yet");
      }
	}); 
}
else{
	console.log("Apple pay not Available in this browser");
}

function applePayButtonClicked(){

	console.log('Apple Pay Initiated');

	var request = {
	  countryCode: 'US',
	  currencyCode: 'USD',
	  supportedNetworks: ['visa', 'masterCard'],
	  merchantCapabilities: ['supports3DS','supportsCredit', 'supportsDebit'],
	  total: { label: 'Your Label', amount: '5.00' },
	}
	
	var session = new ApplePaySession(1, request);

	// Merchant Validation
	session.onvalidatemerchant = function (event) {
		console.log(event);
		var promise = performValidation(event.validationURL);
		promise.then(function (merchantSession) {
			session.completeMerchantValidation(merchantSession);
		}); 
	}

	session.onpaymentmethodselected = function(event) {
		console.log(event);
		
		var newTotal = { type: 'final', label: 'Test Label', amount: '10.00' };
		session.completePaymentMethodSelection( newTotal);
		
		
	}

	session.onpaymentauthorized = function (event) {
		console.log('Payment Authorized');
	}

	session.oncancel = function(event) {
		console.log('starting session.cancel');
		console.log(event);
	}
	
	session.begin();

}

