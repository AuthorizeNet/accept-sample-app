
<!DOCTYPE html>
<html lang="en">
<?php
	session_start();
	include 'getToken.php';
	if ($response->messages->resultCode != "Ok") {
			$_SESSION["cpid_error"]='true';
			setcookie("cpid",'', time() -1, "/");
			setcookie("temp_cpid",'', time() -1, "/");
			header('Location: login.php');
			exit();	
    }else{
    	$_SESSION["cpid_error"]='false';
    }
?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Accept Sample App</title>

	<!-- Bootstrap core CSS -->
	<link href="scripts/bootstrap.min.css" rel="stylesheet">

	<style type="text/css">

		.navbar {min-height: 0px; margin-bottom: 0px; border: 0px}
		.nav>li {display: inline-block;}
		.navbar-centered .nav > li > a {color: white}
		.navbar-inverse { background-color: #555  } /* #7B7B7B */
		.navbar-centered .nav > li > a:hover{ background-color: white; color: black }
		.navbar-centered .nav .active > a,.navbar-centered .navbar-nav > .active > a:focus { background-color: white; color: black; font-weight:bold; }
		.navbar-centered .navbar-nav { float: none; text-align: center; }
	    .navbar-centered .navbar-nav > li { float: none; }
	    .navbar-centered .nav > li { display: inline; }
	    .navbar-centered .nav > li > a {display: inline-block}
	    #home { color:ivory; margin-left: 15%; margin-right: 15% }

		@media (min-width: 768px) {
	    	.navbar-centered .nav > li > a { width:12%; }
	    	#home { font-size: 30px}
	    }

	    @media (min-width:360px ) and (max-width: 768px){
	    	.navbar-centered .nav > li > a {font-size: 12px}
	    	#home { font-size: 20px}
	    }

	    @media (max-width: 360px) {
	    	.navbar-centered .nav > li > a {font-size: 10px}
	    	#home { font-size: 15px}
	    }

		/* vertically center the Bootstrap modals */
		.modal {
			text-align: center;
			padding: 0!important;
		}

		.modal:before {
			content: '';
			display: inline-block;
			height: 100%;
			vertical-align: middle;
			margin-right: -4px;
		}

		.modal-dialog {
			display: inline-block;
			text-align: left;
			vertical-align: middle;
		}

	</style>

	<script src="scripts/jquery-2.1.4.min.js"></script>
	<script src="scripts/bootstrap.min.js"></script>
	<script src="scripts/jquery.cookie.js"></script>
	
	
	<script src="https://jstest.authorize.net/v1/Accept.js"></script>
	<script src="acceptJSCaller.js"></script>

<script type="text/javascript">
	var baseUrl = "https://test.authorize.net/customer/";
	var onLoad = true;
	tab = null;

	function returnLoaded() {
		console.log("Return Page Called ! ");
		showTab(tab);
	}
	window.CommunicationHandler = {};
	function parseQueryString(str) {
		var vars = [];
		var arr = str.split('&');
		var pair;
		for (var i = 0; i < arr.length; i++) {
			pair = arr[i].split('=');
			vars[pair[0]] = unescape(pair[1]);
		}
		return vars;
	}
	CommunicationHandler.onReceiveCommunication = function (argument) {
		params = parseQueryString(argument.qstr)
		parentFrame = argument.parent.split('/')[4];
		console.log(params);
		console.log(parentFrame);
		//alert(params['height']);
		$frame = null;
		switch(parentFrame){
			case "manage" 		: $frame = $("#load_profile");break;
			case "addPayment" 	: $frame = $("#add_payment");break;
			case "addShipping" 	: $frame = $("#add_shipping");break;
			case "editPayment" 	: $frame = $("#edit_payment");break;
			case "editShipping"	: $frame = $("#edit_shipping");break;
		}

		switch(params['action']){
			case "resizeWindow" 	: if( parentFrame== "manage" && parseInt(params['height'])<1150) params['height']=1150;
										$frame.outerHeight(parseInt(params['height']));
										//$frame.css("border","1px double #CCC");
										break;
			case "successfulSave" 	: $('#myModal').modal('hide'); location.reload(false); break;
			case "cancel" 			: 	switch(parentFrame){
										case "addPayment"   : $("#send_token").attr({"action":baseUrl+"addPayment","target":"add_payment"}).submit(); $("#add_payment").hide(); break; 
										case "addShipping"  : $("#send_token").attr({"action":baseUrl+"addShipping","target":"add_shipping"}).submit(); $("#add_shipping").hide(); $('#myModal').modal('toggle'); break;
										case "manage"       : $("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit(); break;
										case "editPayment"  : $("#payment").show(); $("#addPayDiv").show(); break; 
										case "editShipping" : $('#myModal').modal('toggle'); $("#shipping").show(); $("#addShipDiv").show(); break; 
										}
						 				break;
		}
	}

	function showTab(target){
		//onLoad = true;
		var currTime = sessionStorage.getItem("lastTokenTime");
		if (currTime === null || (Date.now()-currTime)/60000 > 15){
			location.reload(true);
			onLoad = true;
		}
		if (onLoad) {
			setTimeout(function(){ 
				$("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit();
				$("#send_token").attr({"action":baseUrl+"addPayment","target":"add_payment"}).submit();
				$("#send_token").attr({"action":baseUrl+"addShipping","target":"add_shipping"}).submit();
			} ,100);
			onLoad = false;
		}

		$("#iframe_holder iframe").hide();$("#payment").hide();$("#shipping").hide();
		$("#home").hide();$("#acceptJSPayDiv").hide();$("#addPayDiv").hide(); $("#addShipDiv").hide();
		//$("body").css("background",""); $("body").css("background","url('scripts/background.png')");
		switch(target){
			case "#home" 		: $("#home").show();$("#acceptJSPayDiv").show();break;
			case "#profile" 	: $("#load_profile").show(); break;
			case "#payment" 	: $("#payment").show(); $("#addPayDiv").show(); break;
			case "#shipping" 	: $("#shipping").show(); $("#addShipDiv").show(); break;
		}
	}

	$(function(){

		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			tab = $(e.target).attr("href") // activated tab
			sessionStorage.setItem("tab",tab);
			showTab(tab);
		});
		onLoad = true;
		sessionStorage.setItem("lastTokenTime",Date.now());
		tab = sessionStorage.getItem("tab");
		if (tab === null) {
			$("[href='#home']").parent().addClass("active");
			tab = "#home";
		}
		else{
			$("[href='"+tab+"']").parent().addClass("active");
		}
		console.log("Tab : "+tab);
		showTab(tab);

		$(".editPay").click(function(e) {
			$ppid = $(this).attr("value");
			$("#send_token [name=paymentProfileId]").attr("value",$ppid);
			$("#add_payment").hide();
			$("#edit_payment").show();
			$("#send_token").attr({"action":baseUrl+"editPayment","target":"edit_payment"}).submit();
			$("#send_token [name=paymentProfileId]").attr("value","");
			$(window).scrollTop($("#edit_payment").offset().top-30);
		});

		$("#addPaymentButton").click(function() {
			$("#edit_payment").hide();
			$("#add_payment").show();
			$(window).scrollTop($('#add_payment').offset().top-50);
		});

		$(".editShip").click(function() {
			$shid = $(this).attr("value");
			$("#send_token [name=shippingAddressId]").attr("value",$shid);
			$("#add_shipping").hide();
			$("#send_token").attr({"action":baseUrl+"editShipping","target":"edit_shipping"}).submit();
			$("#edit_shipping").show();
			$("#send_token [name=shippingAddressId]").attr("value","");
			$("#myModalLabel").text("Edit Details");
			$(window).scrollTop($("#edit_shipping").offset().top-30);
		});

		$("#addShippingButton").click(function() {
			$("#myModalLabel").text("Add Details");
			$("#edit_shipping").hide();
			$("#add_shipping").show();
			$(window).scrollTop($("#add_shipping").offset().top-30);
		});

		vph = $(window).height();
		$("#home").css("margin-top",(vph/4)+'px');

		$(window).resize(function(){
			$('#home').css({'margin-top':(($(window).height())/4)+'px'});
		});

		$(window).keydown(function(event) {
		  if(event.ctrlKey && event.keyCode == 69) { 
		  	event.preventDefault(); 
		    logOut();
		  }
		});

	});

	function logOut() {
		console.log("Log Out event Triggered ..!");
	    $.removeCookie('cpid', { path: '/' });
	    $.removeCookie('temp_cpid', { path: '/' });
	    window.location.href = 'login.php';
	}

</script>

</head>

<body style=" background: url('scripts/background.png'); padding-top: 50px;">
	<div class="container-fluid" style="width: 100%; margin: 0; padding:0">
		
		<div class="navbar navbar-inverse" role="navigation">
			<div class="container-fluid navbar-centered">
				<ul class="nav navbar-nav" style="margin-top: 0px; margin-bottom:0px; margin-left:auto">
					<li role="presentation"><a href="#home" data-toggle="tab">HOME</a></li>
					<li role="presentation"><a href="#profile" data-toggle="tab">PROFILE</a></li>
					<li role="presentation"><a href="#payment" data-toggle="tab">PAYMENT</a></li>
					<li role="presentation"><a href="#shipping" data-toggle="tab">SHIPPING</a></li>
				</ul>
			</div>
		</div>
		<br/>

		<?php include 'getProfiles.php'; ?>
		
		<div id="acceptJSPayDiv" style="position:absolute; bottom:15%; width: 100%; text-align:center">
			<br><p><button type="button" id="acceptJSPayButton" class="btn btn-primary btn-lg col-sm-offset-5 col-sm-2 col-xs-offset-3 col-xs-6" style="font-weight: bolder; font-size: 30px;" data-toggle="modal" data-target="#acceptJSPayModal">Pay</button></p><br>
		</div>

		<div id="acceptJSReceiptModal" class="modal fade" role="dialog">
			<div class="modal-dialog" style="display: inline-block; vertical-align: middle;">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Accept.js Example</h4>
					</div>
					<div class="modal-body" id="acceptJSReceiptBody">
					</div>
				</div></div>
			</div>
		</div>
		
		<!-- Modal -->
		<div id="acceptJSPayModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="display: inline-block; vertical-align: middle;">
			<!-- Modal content-->
			<div class="modal-content">
				
				<div class="modal-header">
					<h4 class="modal-title">ACCEPT.JS EXAMPLE</h4>
				</div>
				
				<div class="modal-body" id="acceptJSPayBody">
					<!--form role="form"-->
					
					
					
					
						<div class="form-group col-xs-8">
							<label for="creditCardNumber">CREDIT CARD NUMBER</label>
							<input type="tel" class="form-control" id="creditCardNumber" placeholder="4111111111111111" value="4111111111111111" autocomplete="off"/>
						</div>
						<div class="form-group col-xs-4">
							<label for="cvv">CVV</label>
							<input type="text" class="form-control" id="cvv" placeholder="123" autocomplete="off"/>
						</div>
						
						
						
						
						<!--div class="form-group col-xs-6 col-xs-offset-1" style="margin-bottom: 2px; border: 2px solid; border-color: #ccc; border-radius: 3px">
							<span style="color: #999; font-weight: 550;">Expiry Date</span>
						</div>
						<div class="form-group col-xs-5" style="margin-bottom: 7px;">
							<span style="opacity: 0">Filler</span>
						</div-->
						
						
						
					<div>
					
						<div class="form-group col-xs-5">
							<label for="expiryDateYY">EXP. DATE</label>
							<input type="text" class="form-control" id="expiryDateYY" placeholder="YYYY"/>
						</div>
						
						<div class="form-group col-xs-3">
							<label for="expiryDateMM" style="opacity: 0">MONTH</label>
							<input type="text" class="form-control" id="expiryDateMM" placeholder="MM"/>
						</div>

					
						<div class="form-group col-xs-4">
						<label for="amount">AMOUNT</label>
							<input type="text" class="form-control" id="amount" placeholder="0.5"/>
						</div>
						
					</div>
						
					<!--/form-->
					<div style="text-align: center; margin-top: 20%;">
						<button type="button" id="submitButton" class="btn btn-primary" style="width: 95%;">SUBMIT</button>
					</div>
					
				</div>
				
			</div>
		</div>
		</div>
		
		<div id="addPayDiv" style="margin-left:5%">
			<br><p><button type="button" id="addPaymentButton" class="btn btn-success btn-lg" style="margin: 5px">Add New Payment</button></p><br>
		</div>

		<div id="addShipDiv" style="margin-left:5%">
			<br><p><button type="button" id="addShippingButton" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" style="margin: 5px">Add New Address</button></p><br>
		</div>

		<div  id="iframe_holder" class="center-block" style="width:90%;max-width: 1000px">
			<iframe id="load_profile" class="embed-responsive-item" name="load_profile" width="100%" height="1150px" frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="add_payment" class="embed-responsive-item panel" name="add_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="edit_payment" class="embed-responsive-item panel" name="edit_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<form id="send_token" action="" method="post" target="load_profile" >
				<input type="hidden" name="token" value="<?php echo $response->token ?>" />
				<input type="hidden" name="paymentProfileId" value="" />
				<input type="hidden" name="shippingAddressId" value="" />
			</form>
		</div>

		<div class="tab-content panel-group">

		<div class="tab-pane" id="home" align="center" >
	      “Our cuisine is handmade with fresh organic and fair-trade spices for an aromatic and succulent dining experience.”
	    </div>

		<div class="tab-pane" id="profile" hidden="true"></div>

		<div class="panel panel-info tab-pane center-block" id="payment" style="width:90%">
			<div class="panel-heading">
				<h2 class="panel-title text-center"><b>Payment Profiles</b></h2>
			</div>
			<div class="panel-body">
			<div class="row">
			<?php foreach ($profileResponse->profile->paymentProfiles as $item) {
			?>				
				<div class="col-sm-6 col-md-3 embed-responsive-item">
					<div class="thumbnail">
						<div class="caption">
							<h4><?php echo isset($item->payment->creditCard) ? "Card &nbsp;&nbsp;: &nbsp;".$item->payment->creditCard->cardNumber : "Account : &nbsp;".$item->payment->bankAccount->accountNumber.", ".$item->payment->bankAccount->bankName ?></h4>
							<h4>Name :&nbsp; <?php echo isset($item->payment->creditCard) ? $item->billTo->firstName." ". $item->billTo->lastName : $item->payment->bankAccount->nameOnAccount ?></h4>
							<h5>Address : <?php echo $item->billTo->address ?> </h5>
							<h5>City : <?php echo $item->billTo->city ?></h5>
							<p align="right"><button class="btn btn-primary editPay" role="button" value="<?php echo $item->customerPaymentProfileId ?>" >Edit Details</button></p>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
			</div>
		</div>

		<div class="panel panel-info tab-pane center-block" id="shipping" style="width:90%">
			<div class="panel-heading">
				<h3 class="panel-title text-center"><b>Shipping Addresses</b></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<?php foreach ($profileResponse->profile->shipToList as $item) {
					?>				
						<div class="col-sm-6 col-md-3 embed-responsive-item">
							<div class="thumbnail">
								<div class="caption">
									<h4>Name &nbsp;  &nbsp;: &nbsp;<?php echo $item->firstName ?> <?php echo $item->lastName ?></h4>
									<h4>Address : &nbsp;<?php echo $item->address ?> </h4>
									<h5>City &nbsp;: &nbsp;<?php echo $item->city.", ".$item->state ?></h5>
									<h5>Zip  &nbsp;: <?php echo $item->zip ?></h5>
									<p align="right"><button class="btn btn-primary editShip" role="button" value="<?php echo $item->customerAddressId ?>" data-toggle="modal" data-target="#myModal" >Edit Details</button></p>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

		

		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel" style="font-weight: bold">Edit Title</h4>
		      </div>
		      <div class="modal-body">
		          	<iframe id="add_shipping" class="embed-responsive-item" name="add_shipping" width="100%"  frameborder="0" scrolling="no" hidden="true"></iframe>
					<iframe id="edit_shipping" class="embed-responsive-item" name="edit_shipping" width="100%"  frameborder="0" scrolling="no" hidden="true"></iframe> 
		      </div>
		    </div>
		  </div>
		</div>

	</div> 
</body>

<script>
	$('#acceptJSPayButton').click(function(e){
		e.preventDefault();
	});
	$('#submitButton').click(function(e){
		e.preventDefault();
		acceptJSCaller();
	});
</script>

</html>
