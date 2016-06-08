function messageFunc(msg)
{
	try{
		responseObj=JSON.parse(msg);
		if(responseObj.transactionResponse.responseCode=='1'){
			message="Transaction Successful!<br>Transaction ID: "+responseObj.transactionResponse.transId;
		}
		else{
			message="Transaction Unsuccessful.<br>";//+responseObj.messages.message[0].text;
			if(responseObj.transactionResponse.errors[0]!=null)
				message+=responseObj.transactionResponse.errors[0].errorText;
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
	
	//make server put name, transactionKey, currencyCode, transactionType, (retail too)
	var transactRequest = {
		'createTransactionRequest' : {
			'merchantAuthentication' : {
				'name' : '',
				'transactionKey' : ''
			},
			'transactionRequest' : {
				'transactionType' : '',
				'amount' : document.getElementById('amount').value,
				'currencyCode' : '',
				'payment' : {
					'opaqueData' : {
						'dataDescriptor' : dataObj.dataDescriptor,
						'dataValue' : dataObj.dataValue
					}
				},
				'retail' : {
					'marketType' : '0',
					'deviceType' : '0'
				}
			}
		}
	}
	
	$.ajax({
		
		url: "transactionCaller.php",
		data: {request: JSON.stringify(transactRequest)},
		method: 'POST',
		timeout: 5000
		
	}).done(function(data){
		
		console.log('Success');
		console.log(data);
		
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
	//add cvv
	cardData.month  =  document.getElementById('expiryDateMM').value;
	cardData.year  =  document.getElementById('expiryDateYY').value;
	secureData.cardData  =  cardData;
	authData.clientKey  =  '3QCgrSr75c36yDE3x7eEWDcfa8CsCJE47qZV52kQLUV52rp5tGjYjApuqJ2wMjea';
	authData.apiLoginID  =  '48uDp4QBA';
	secureData.authData  =  authData;
	Accept.dispatchData(secureData, 'responseHandler');
}
