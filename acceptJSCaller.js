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

function createTransact(dataObj) {
	
	$.ajax({
		
		url: "transactionCaller.php",
		data: {amount: document.getElementById('amount').value, dataDesc: dataObj.dataDescriptor, dataValue: dataObj.dataValue},
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
	cardData.cardNumber  =  document.getElementById('creditCardNumber').value;
	cardData.cardCode = document.getElementById('cvv').value;
	cardData.month  =  document.getElementById('expiryDateMM').value;
	cardData.year  =  document.getElementById('expiryDateYY').value;
	secureData.cardData  =  cardData;
	authData.clientKey  =  '5FcB6WrfHGS76gHW3v7btBCE3HuuBuke9Pj96Ztfn5R32G5ep42vne7MCWZtAucY';
	authData.apiLoginID  =  '5KP3u95bQpv';
	secureData.authData  =  authData;
	Accept.dispatchData(secureData, 'responseHandler');
}
