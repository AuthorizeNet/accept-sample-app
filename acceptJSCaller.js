// The result of the transaction processing will be returned from the processing script as a JSON object. Parse the object to determine success or failure, and alert the user.
function messageFunc(msg)
{
	try{
		responseObj=JSON.parse(msg);
		if(responseObj.transactionResponse.responseCode=='1'){
			message="Transaction Successful!<br>Transaction ID: "+responseObj.transactionResponse.transId;
		}
		else{
			message="Transaction Unsuccessful.";//+responseObj.messages.message[0].text;
			if(responseObj.transactionResponse.errors!=null)//to do: take care of errors[1] array being parsed into single object
			{
				message+=responseObj.transactionResponse.errors.error.errorText;
			}
			/*else if(responseObj.transactionResponse.errors[0]!=null)
			{
				for(i=0;i<responseObj.transactionResponse.errors.length;i++)
				{
					message+="<br>";
					message+=responseObj.transactionResponse.errors[i].error.errorText;
				}
			}*/
			if(responseObj.transactionResponse.transId!=null)
			{
				message+="<br>";
				message+=("Transaction ID: "+responseObj.transactionResponse.transId)
			}
		}
	}
	catch(error){
		console.log("Couldn't parse result string");
		message="Error.";
	}
	
	//alert(message);
	
	$('#acceptJSReceiptBody').html(message);
	//jQuery.noConflict();
	$('#acceptJSPayModal').modal('hide');
	$('#acceptJSReceiptModal').modal('show');
}

// Do an AJAX call to submit the transaction data and the payment none to a separate PHP page to do the actual transaction processing.
function createTransact(dataObj) {
	
	// Set Amount for demo purposes if not set by callers form
	myAmt = document.getElementById('amount').value;
	if(!myAmt)
	{
		myAmt = Math.floor((Math.random() * 100) + 1);
	}
	console.log('Amount = '+myAmt);
	
	$.ajax({
		
		url: "transactionCaller.php",
		data: {amount: myAmt, dataDesc: dataObj.dataDescriptor, dataValue: dataObj.dataValue},
		method: 'POST',
		timeout: 5000
		
	}).done(function(data){
		
		console.log('Success');
		
	}).fail(function(){
		
		console.log('Error');
		
	}).always(function(textStatus){
		
		console.log(textStatus);
		messageFunc(textStatus);
		
	})
	
}

// Process the response from Authorize.Net to retrieve the two elements of the payment nonce.
// If the data looks correct, record the OpaqueData to the console and call the transaction processing function.
function  responseHandler(response) {
	if (response.messages.resultCode === 'Error') {
		for (var i = 0; i < response.messages.message.length; i++) {
			console.log(response.messages.message[i].code + ':' + response.messages.message[i].text);
		}
		alert("acceptJS library error!")
	} else {
		console.log(response.opaqueData.dataDescriptor);
		console.log(response.opaqueData.dataValue);
		createTransact(response.opaqueData);
	}
}

function acceptJSCaller()
{
	var  secureData  =  {}  ,  authData  =  {}  ,  cardData  =  {};
	
	// Extract the card number and expiration date.
	cardData.cardNumber  =  document.getElementById('creditCardNumber').value;
	cardData.cardCode = document.getElementById('cvv').value;
	cardData.month  =  document.getElementById('expiryDateMM').value;
	cardData.year  =  document.getElementById('expiryDateYY').value;
	secureData.cardData  =  cardData;

	// The Authorize.Net Client Key is used in place of the traditional Transaction Key. The Transaction Key
	// is a shared secret and must never be exposed. The Client Key is a public key suitable for use where
	// someone outside the merchant might see it.

	authData.clientKey  =  '6jZy4G5vmCEat9G3xjtNguj7DLw5NhgS4PBr4KNp7tV2tXa34E3BkdG33dcX4S84';
	authData.apiLoginID  =  '3e3b5H4YLP';
	secureData.authData  =  authData;
	
    // Pass the card number and expiration date to Accept.js for submission to Authorize.Net.
	Accept.dispatchData(secureData, 'responseHandler');
}
