
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Hosted CIM</title>

	<!-- Bootstrap core CSS -->
	<link href="scripts/bootstrap.min.css" rel="stylesheet">
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
	var target;

	function returnLoaded() {
		showTab(target);
	}
	window.AuthorizeNetPopup = {};
	function parseQueryString(str) {
		var vars = [];
		var arr = str.split('&');
		var pair;
		for (var i = 0; i < arr.length; i++) {
			pair = arr[i].split('=');
			//vars.push(pair[0]);
			vars[pair[0]] = unescape(pair[1]);
		}
		return vars;
	}
	AuthorizeNetPopup.onReceiveCommunication = function (argument) {
		params = parseQueryString(argument.qstr)
		parentFrame = argument.parent.split('/')[4];
		console.log(params);
		console.log(parentFrame);
		$frame = null;
		switch(parentFrame){
			case "manage" : $frame = $("#load_profile");break;
			case "addPayment" : $frame = $("#add_payment");break;
			case "addShipping" : $frame = $("#add_shipping");break;
			case "editPayment" : $frame = $("#edit_payment");break;
			case "editShipping" : $frame = $("#edit_shipping");break;
		}

		switch(params['action']){
			case "resizeWindow" : if( parentFrame== "manage" && parseInt(params['height'])<1140) params['height']=1150;$frame.outerHeight(parseInt(params['height'])); break;
			case "successfulSave" : $("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit();break;
			case "cancel" : switch(parentFrame){
							case "addPayment": $("#send_token").attr({"action":baseUrl+"addPayment","target":"add_payment"}).submit(); break; 
							case "addShipping" : $("#send_token").attr({"action":baseUrl+"addShipping","target":"add_shipping"}).submit(); break;
							case "manage": $("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit(); break;
							}
			 				break;
		}
	}

	function getToken(){
		$.get('config.xml',function(xmlReq) {
			$req = (new XMLSerializer()).serializeToString(xmlReq);
			$.ajax({
				type : "POST" ,
				async: false,
				url : "https://apitest.authorize.net/xml/v1/request.api",
				data : $req ,
				success : function(response){
					$token = $(response).find("token").text();
					console.log($token);
					$("#send_token [name=token]").attr("value",$token);
					sessionStorage.setItem("lastTokenTime", Date.now());
					sessionStorage.setItem("token", $token);
				},
				error : function(errors){
					console.log(errors);
				},
				dataType : "xml"
			});
		});
	}

	function showTab(target){
		//onLoad = true;
		var currTime = sessionStorage.getItem("lastTokenTime");
		if (currTime === null || (Date.now()-currTime)/60000 > 15){
			getToken();
			onLoad = true;
		}
		if (onLoad) {
			$("#send_token [name=token]").attr("value",sessionStorage.getItem("token"));
			setTimeout(function(){ 
				$("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit();
				$("#send_token").attr({"action":baseUrl+"addPayment","target":"add_payment"}).submit();
				$("#send_token").attr({"action":baseUrl+"addShipping","target":"add_shipping"}).submit();
			} ,100);
			onLoad = false;
		}

		$("#iframe_holder iframe").hide();
		switch(target){
			case "#profile" : //$("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit();
								setTimeout(function(){$("#load_profile").show();},100);
								break;
			case "#payment" : // $("#add_payment").show(); break;
			case "#shipping" : // $("#add_shipping").show(); break;
		}
	}

	$(function(){

		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			target = $(e.target).attr("href") // activated tab
			showTab(target);
		});
		onLoad = true;
		showTab("#profile");
		//$('#load_profile').on('click', function(event) { console.log("Logged : "+event.currentTarget.URL);} );

		$("#editPaymentButton").click(function() {
			$("#send_token [name=paymentProfileId]").attr("value",$("#paymentId").val());
			$("#add_payment").hide();
			$("#send_token").attr({"action":baseUrl+"editPayment","target":"edit_payment"}).submit();
			$("#edit_payment").show().focus();
		});

		$("#addPaymentButton").click(function() {
			$("#edit_payment").hide();
			$("#add_payment").show().focus();
		});

		$("#editShippingButton").click(function() {
			$("#send_token [name=shippingAddressId]").attr("value",$("#shippingId").val());
			$("#add_shipping").hide();
			$("#send_token").attr({"action":baseUrl+"editShipping","target":"edit_shipping"}).submit();
			$("#edit_shipping").show().focus();
		});

		$("#addShippingButton").click(function() {
			$("#edit_shipping").hide();
			$("#add_shipping").show().focus();
		});

	});
</script>

</head>

<body>

	<div class="container">
		<h4 class="text-muted" style="background: #555; color: orange ; padding: 20px; font-weight: bold;"><b>Manage Your Account</b></h4>
		<div class="header clearfix" style="background:#D0DEEC">
			<nav>
				<ul class="nav nav-pills pull-right">
					<li role="presentation" class="active"><a href="#profile" data-toggle="tab">Manage Profiles</a></li>
					<li role="presentation"><a href="#payment" data-toggle="tab">Payment</a></li>
					<li role="presentation"><a href="#shipping" data-toggle="tab">Shipping</a></li>
				</ul>
			</nav>
		</div>
		<br/>

		<div class="tab-content">
			<div class="tab-pane" id="profile" hidden="true"></div>

			<div class="panel panel-info tab-pane" id="payment" style="width: 82%;margin-left: 9%">
				<div class="panel-heading">
					<h2 class="panel-title"><b>Edit Payment Profile</b></h2>
				</div>
				<div class="panel-body">
					<form class="form">
						<div class="form-group">
							<label for="exampleInputName2">Payment Profile Id</label>
							<input type="text" class="form-control" id="paymentId" placeholder="36694109">
						</div>
						<button type="button" id="editPaymentButton" class="btn btn-primary" style="margin: 5px" data-toggle="modal" data-target="#myModal">Edit Payment info</button>
						<button type="button" id="addPaymentButton" class="btn btn-success" style="margin: 5px">Add New Payment Method</button>
					</form>
				</div>
			</div>

			<div class="panel panel-info tab-pane" id="shipping"  style="width: 82%;margin-left: 9%">
				<div class="panel-heading">
					<h3 class="panel-title"><b>Edit Shipping Address</b></h3>
				</div>
				<div class="panel-body">
					<form class="form">
						<div class="form-group">
							<label for="exampleInputName2">Shipping Address Id</label>
							<input type="text" class="form-control" id="shippingId" placeholder="38180870">
						</div>
						<button type="button" id="editShippingButton" class="btn btn-primary" style="margin: 5px">Edit Address info</button>
						<button type="button" id="addShippingButton" class="btn btn-success" style="margin: 5px">Add New Shipping Address</button>
					</form>
				</div>
			</div>
		</div>

		<div class="panel" id="iframe_holder" >
			<iframe id="load_profile" class="embed-responsive-item" name="load_profile" width="100%" frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="add_payment" class="embed-responsive-item" name="add_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="add_shipping" class="embed-responsive-item" name="add_shipping" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="edit_payment" class="embed-responsive-item" name="edit_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="edit_shipping" class="embed-responsive-item" name="edit_shipping" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<form id="send_token" action="https://test.authorize.net/customer/manage" method="post" target="load_profile" >
				<input type="hidden" name="token" value="" />
				<input type="hidden" name="paymentProfileId" value="" />
				<input type="hidden" name="shippingAddressId" value="" />
				<!--<input type="submit" class="submit" value="Manage my payment and shipping information" align="center"/> -->
			</form>
		</div>

	</div> 
</body>
</html>
