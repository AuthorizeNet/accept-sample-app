function showResult(msg)
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
	
	$('#acceptJSReceiptHeader').html("<h4 class='modal-title'>ACCEPT.JS EXAMPLE</h4>");
	$('#acceptJSReceiptBody').html(message);
	//jQuery.noConflict();
	$('#acceptJSReceiptModal').modal('show');
}

function createProfileTransaction() {
	
	$.ajax({
		
		url: "createTransactionWithProfile.php",
		// These profiles IDs would NOT BE HARDCODED of course but rather taken from the logged in user
		data: {amount: Math.floor((Math.random() * 100) + 1), customerProfileId: "1808251712", paymentProfileId: "1803116214"},
		method: 'POST',
		timeout: 5000
		
	}).done(function(data){
		
		console.log('Success');
		
	}).fail(function(){
		
		console.log('Error');
		
	}).always(function(textStatus){
		
		console.log(textStatus);
		showResult(textStatus);
		
	})
	
}


