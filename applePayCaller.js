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
	  total: { label: 'My Label', amount: '5.00' },
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

	function performValidation(valURL) {
		return new Promise(function(resolve, reject) {
			var xhr = new XMLHttpRequest();
			xhr.onload = function() {
				var data = JSON.parse(this.responseText);
				console.log(data);
				resolve(data);
			};
			xhr.onerror = reject;
			xhr.open('POST', 'validateApplePayMerchant.php', true);
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xhr.send('validationUrl='+valURL);
		});
	}

	session.onpaymentmethodselected = function(event) {
		console.log('starting onpaymentmethodselected');
		console.log(event);
		var newTotal = { type: 'final', label: 'Products', amount: '10.00' };
		var newLineItems =[{type: 'final',label: 'Product-1', amount: '10.00' }]
		session.completePaymentMethodSelection( newTotal, newLineItems);
	}

	session.onpaymentauthorized = function (event) {
		console.log('starting session.onpaymentauthorized');
		console.log(event);
		var promise = sendPaymentToken(event.payment.token);
		promise.then(function (success) {	
			var status;
			if (success){
				status = ApplePaySession.STATUS_SUCCESS;
				alert('Apple Pay Payment done successfully ');
				console.log('Apple Pay Payment SUCCESS ');
			} else {
				status = ApplePaySession.STATUS_FAILURE;
			}		
			console.log( "result of sendPaymentToken() function =  " + success );
			session.completePayment(status);
		});
	}

	function sendPaymentToken(paymentToken) {
		return new Promise(function(resolve, reject) {
			console.log('starting function sendPaymentToken()');
			console.log(paymentToken);
			
			/* Send Payment token to Payment Gateway, here its defaulting to True just to mock that part */
				returnFromGateway = true;
			/*-----------------------*/

			console.log("defaulting to successfull payment by the Token");

			if ( returnFromGateway == true )
				resolve(true);
			else
				reject;
		});
	}

	session.oncancel = function(event) {
		console.log('starting session.cancel');
		console.log(event);
	}
	
	session.begin();

}

