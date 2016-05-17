
<!DOCTYPE html>
<html lang="en">
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
	ul li [data-toggle] {
		font-size: 15px;
	}
	</style>
<!--
<link href="css/ie10-viewport-bug-workaround.css" rel="stylesheet">
<link href="jumbotron-narrow.css" rel="stylesheet">
-->
<script src="scripts/jquery-2.1.4.min.js"></script>
<script src="scripts/bootstrap.min.js"></script>
<!--<script src="js/sample.js"></script> -->

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
										$frame.css("border","2px double #CCC");
										break;
			case "successfulSave" 	: $('#myModal').modal('hide'); location.reload(false); break;
			case "cancel" 			: 	switch(parentFrame){
										case "addPayment"   : $("#send_token").attr({"action":baseUrl+"addPayment","target":"add_payment"}).submit(); $("#add_payment").hide(); break; 
										case "addShipping"  : $("#send_token").attr({"action":baseUrl+"addShipping","target":"add_shipping"}).submit(); $("#add_shipping").hide(); $('#myModal').modal('toggle'); break;
										case "manage"       : $("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit(); break;
										case "editPayment"  : $("#payment").show(); break; 
										case "editShipping" : $('#myModal').modal('toggle'); $("#shipping").show(); break; 
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

		$("#iframe_holder iframe").hide();$("#payment").hide();$("#shipping").hide();$("#home").hide();
		switch(target){
			case "#home" 		: $("#home").show();break;
			case "#profile" 	: $("#load_profile").show(); break;
			case "#payment" 	: $("#payment").show(); break;
			case "#shipping" 	: $("#shipping").show(); break;
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
			$("#myModalLabel").text("Edit Shipping Address");
			$(window).scrollTop($("#edit_shipping").offset().top-30);
		});

		$("#addShippingButton").click(function() {
			$("#myModalLabel").text("Add New Shipping Address");
			$("#edit_shipping").hide();
			$("#add_shipping").show();
			$(window).scrollTop($("#add_shipping").offset().top-30);
		});

	});
</script>

</head>

<body >
	<?php include 'getToken.php'; ?>
	<div class="container" style="width: 100%">
		<h4 class="text-muted" style="background: #444 ; color: orange; padding: 10px; font-size: 24px; text-align: center; font-style: oblique; font-family: century"><b>Have a Cup of Coffee</b></h4>
		<div class="header clearfix" style="background:#D0DEEC">
			<nav>
				<ul class="nav nav-pills pull-right">
					<li role="presentation" ><a href="#home" data-toggle="tab">Home</a></li>
					<li role="presentation" ><a href="#profile" data-toggle="tab">Profile</a></li>
					<li role="presentation"><a href="#payment" data-toggle="tab">Payment</a></li>
					<li role="presentation"><a href="#shipping" data-toggle="tab">Shipping</a></li>
				</ul>
			</nav>
		</div>
		<br/>

		<?php include 'getProfiles.php'; ?>

		<div class="panel" id="iframe_holder">
			<iframe id="load_profile" class="embed-responsive-item" name="load_profile" width="100%" height="1150px" frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="add_payment" class="embed-responsive-item" name="add_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="edit_payment" class="embed-responsive-item" name="edit_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<form id="send_token" action="" method="post" target="load_profile" >
				<input type="hidden" name="token" value="<?php echo $response->token ?>" />
				<input type="hidden" name="paymentProfileId" value="" />
				<input type="hidden" name="shippingAddressId" value="" />
			</form>
		</div>

		<div class="tab-content">

		<div class="tab-pane panel col-centered text-center" id="home" style="background: floralwhite; ">
	      <h1 style="background:#C3A878; font-family:Algerian">Coffee Shop</h1><hr/>
	      <img src="scripts/logo.jpg" class="img-circle" alt="Coffee Shop" style ="width:30%" /><hr/>
		  <h3 style="background:#C3A878; font-family:Algerian">Authorize .Net Accept Profiles</h3><hr/>
	    </div>

		<div class="tab-pane" id="profile" hidden="true"></div>

		<div class="panel panel-info tab-pane" id="payment" style="width: 100%;margin-left: 0%; ">
			<div class="panel-heading">
				<h2 class="panel-title"><b>Edit Payment Profile</b></h2>
			</div>
			<div class="panel-body">
			<hr/><p><button type="button" id="addPaymentButton" class="btn btn-success btn-lg" style="margin: 5px">Add New Payment</button><p><hr/>
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

		<div class="panel panel-info tab-pane" id="shipping"  style="width: 100%;margin-left: 0%; ">
			<div class="panel-heading">
				<h3 class="panel-title"><b>Edit Shipping Address</b></h3>
			</div>
			<div class="panel-body">
				<hr/><p><button type="button" id="addShippingButton" class="btn btn-success btn-lg" data-toggle="modal" data-target="#myModal" style="margin: 5px">Add New Address</button></p><hr/>
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
		        <h4 class="modal-title" id="myModalLabel" style="color: teal">Edit Title</h4>
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
</html>
