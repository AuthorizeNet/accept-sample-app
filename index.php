
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
	var baseUrl = "https://securecad.labwebapp.com/customer/";
	var onLoad = true;
	var target;
	tab = null;

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
			case "successfulSave" : $("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit(); location.reload();break;
			case "cancel" : switch(parentFrame){
							case "addPayment": $("#send_token").attr({"action":baseUrl+"addPayment","target":"add_payment"}).submit(); $("#add_payment").hide(); break; 
							case "addShipping" : $("#send_token").attr({"action":baseUrl+"addShipping","target":"add_shipping"}).submit(); $("#add_shipping").hide(); break;
							case "manage": $("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit(); break;
							}
			 				break;
		}
	}

	function showTab(target){
		//onLoad = true;
		var currTime = sessionStorage.getItem("lastTokenTime");
		if (currTime === null || (Date.now()-currTime)/60000 > 15){
			onLoad = true;
		}
		if (onLoad) {
			//$("#send_token [name=token]").attr("value",sessionStorage.getItem("token"));
			setTimeout(function(){ 
				$("#send_token").attr({"action":baseUrl+"manage","target":"load_profile" }).submit();
				$("#send_token").attr({"action":baseUrl+"addPayment","target":"add_payment"}).submit();
				$("#send_token").attr({"action":baseUrl+"addShipping","target":"add_shipping"}).submit();
			} ,100);
			onLoad = false;
		}

		$("#iframe_holder iframe").hide();
		$("#home").hide();
		switch(target){
			case "#home" : $("#home").show();break;
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
			sessionStorage.setItem("tab",target);
		});
		onLoad = true;
		/*tab = sessionStorage.getItem("tab");
		if (tab === null) {
			showTab("#profile");
		}
		else{
			showTab(tab);
		}*/
		showTab("#home");
		//$('#load_profile').on('click', function(event) { console.log("Logged : "+event.currentTarget.URL);} );

		$(".editPay").click(function(e) {
			$ppid = $(this).attr("value");
			$("#send_token [name=paymentProfileId]").attr("value",$ppid);
			$("#add_payment").hide();
			$("#send_token").attr({"action":baseUrl+"editPayment","target":"edit_payment"}).submit();
			$("#edit_payment").show().focus();
			$("#send_token [name=paymentProfileId]").attr("value","");
			$(window).scrollTop($("#edit_payment").offset().top-30);
		});

		$("#addPaymentButton").click(function() {
			$("#edit_payment").hide();
			$("#add_payment").show();
			$(window).scrollTop($('#add_payment').offset().top-30);
		});

		$(".editShip").click(function() {
			$shid = $(this).attr("value");
			$("#send_token [name=shippingAddressId]").attr("value",$shid);
			$("#add_shipping").hide();
			$("#send_token").attr({"action":baseUrl+"editShipping","target":"edit_shipping"}).submit();
			$("#edit_shipping").show().focus();
			$("#send_token [name=shippingAddressId]").attr("value","");
			$(window).scrollTop($("#edit_shipping").offset().top-30);
		});

		$("#addShippingButton").click(function() {
			$("#edit_shipping").hide();
			$("#add_shipping").show().focus();
			$(window).scrollTop($("#add_shipping").offset().top-30);
		});

	});
</script>

</head>

<body>

	<div class="container">
		<h4 class="text-muted" style="background: #555; color: orange ; padding: 20px; font-weight: bold;"><b>Coffee Shop Web Application</b></h4>
		<div class="header clearfix" style="background:#D0DEEC">
			<nav>
				<ul class="nav nav-pills pull-right">
					<li role="presentation" class="active"><a href="#home" data-toggle="tab">Home</a></li>
					<li role="presentation" ><a href="#profile" data-toggle="tab">Manage Profiles</a></li>
					<li role="presentation"><a href="#payment" data-toggle="tab">Payment</a></li>
					<li role="presentation"><a href="#shipping" data-toggle="tab">Shipping</a></li>
				</ul>
			</nav>
		</div>
		<br/>

<?php
error_reporting(E_ERROR);
$param = parse_ini_file("config.txt");
$xmlStr = <<<XML
﻿<?xml version="1.0" encoding="utf-8"?>
<getHostedProfilePageRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
<merchantAuthentication></merchantAuthentication>
<customerProfileId></customerProfileId>
<hostedProfileSettings>
<setting><settingName>hostedProfileReturnUrl</settingName></setting>
<setting><settingName>hostedProfileIFrameCommunicatorUrl</settingName></setting>
<setting><settingName>hostedProfileReturnUrlText</settingName><settingValue>Back to Confirmation Page</settingValue></setting>
<setting><settingName>hostedProfilePageBorderVisible</settingName><settingValue>true</settingValue></setting>
</hostedProfileSettings>
</getHostedProfilePageRequest>
XML;
$xml = new SimpleXMLElement($xmlStr);
$xml->merchantAuthentication->addChild('name',$param['name']);
$xml->merchantAuthentication->addChild('transactionKey',$param['transactionKey']);
$xml->customerProfileId = $param['customerProfileId'];

$xml->hostedProfileSettings->setting[0]->addChild('settingValue',$param['Home']."return.html");
$xml->hostedProfileSettings->setting[1]->addChild('settingValue',$param['Home']."iCommunicator.html");

$url = "https://downloadvposcad.labwebapp.com/xml/v1/request.api";

    try{	//setting the curl parameters.
        $ch = curl_init();
        if (FALSE === $ch)
        	throw new Exception('failed to initialize');
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_PROXY, "http://internet.visa.com:80");
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        $content = curl_exec($ch);
        $response = new SimpleXMLElement($content);
        if (FALSE === $content)
        	throw new Exception(curl_error($ch), curl_errno($ch));
        curl_close($ch);

    }catch(Exception $e) {
    	trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
	}
?>

<?php
$profileReq = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<getCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
<merchantAuthentication></merchantAuthentication>
<customerProfileId></customerProfileId>
</getCustomerProfileRequest>
XML;
$xml = new SimpleXMLElement($profileReq);
$xml->merchantAuthentication->addChild('name',$param['name']);
$xml->merchantAuthentication->addChild('transactionKey',$param['transactionKey']);
$xml->customerProfileId = $param['customerProfileId'];

try{	//setting the curl parameters.
        $ch = curl_init();
        if (FALSE === $ch)
        	throw new Exception('failed to initialize');
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_PROXY, "http://internet.visa.com:80");
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        $content = curl_exec($ch);
        $profileResponse = new SimpleXMLElement($content);
        if (FALSE === $content)
        	throw new Exception(curl_error($ch), curl_errno($ch));
        curl_close($ch);

    }catch(Exception $e) {
    	trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
	}

?>
			<div class="tab-content">
			<div class="tab-pane panel" id="home" >
				<div class="container col-centered text-center" style="background: whitesmoke">
			      <hr/><h1 style="background:#C3A878; font-family:Algerian">Coffee Shop</h1><hr/>
			      <img src="scripts/logo.jpg" class="img-circle" alt="Coffee Shop" style ="width:60%" /><hr/>
				  <h3 style="background:#C3A878; font-family:Algerian">Authorize .Net Accept Profiles</h3><hr/>
			    </div>
			</div>
			<div class="tab-pane" id="profile" hidden="true"></div>

			<div class="panel panel-info tab-pane" id="payment" style="width: 84%;margin-left: 8%">
				<div class="panel-heading">
					<h2 class="panel-title"><b>Edit Payment Profile</b></h2>
				</div>
				<div class="panel-body">
				<hr/><p><button type="button" id="addPaymentButton" class="btn btn-success btn-lg" style="margin: 5px">Add New Payment Method</button><p><hr/>
				<div class="row">
				<?php foreach ($profileResponse->profile->paymentProfiles as $item) {
				?>				
					<div class="col-sm-6 col-md-4 embed-responsive-item">
						<div class="thumbnail">
							<div class="caption">
								<h3><?php echo $item->payment->creditCard->cardNumber ?></h3>
								<h4><?php echo $item->billTo->firstName ?> &nbsp; <?php echo $item->billTo->lastName ?></h4>
								<h5><?php echo $item->billTo->address ?> </h5>
								<h5> <?php echo $item->billTo->city ?></h5>
								<p><button class="btn btn-primary editPay" role="button" value="<?php echo $item->customerPaymentProfileId ?>" >Edit Details</button></p>
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
				</div>
			</div>

			<div class="panel panel-info tab-pane" id="shipping"  style="width: 84%;margin-left: 8%">
				<div class="panel-heading">
					<h3 class="panel-title"><b>Edit Shipping Address</b></h3>
				</div>
				<div class="panel-body">
					<hr/><p><button type="button" id="addShippingButton" class="btn btn-success btn-lg" style="margin: 5px">Add New Shipping Address</button></p><hr/>
					<div class="row">
						<?php foreach ($profileResponse->profile->shipToList as $item) {
						?>				
							<div class="col-sm-6 col-md-4 embed-responsive-item">
								<div class="thumbnail">
									<div class="caption">
										<h3><?php echo $item->firstName ?> &nbsp; <?php echo $item->lastName ?></h3>
										<h4><?php echo $item->address ?> </h4>
										<h5> <?php echo $item->city ?></h5>
										<h5> <?php echo $item->state ?></h5>
										<h5> <?php echo $item->zip ?></h5>
										<p><button class="btn btn-primary editShip" role="button" value="<?php echo $item->customerAddressId ?>" >Edit Details</button></p>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

<!--<textarea rows=30 cols=100 wrap=virtual>
<?= $profileResponse->asXML() ?>
</textarea> -->
		<div class="panel" id="iframe_holder" >
			<iframe id="load_profile" class="embed-responsive-item" name="load_profile" width="100%" height="1150px" frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="add_payment" class="embed-responsive-item" name="add_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="add_shipping" class="embed-responsive-item" name="add_shipping" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="edit_payment" class="embed-responsive-item" name="edit_payment" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<iframe id="edit_shipping" class="embed-responsive-item" name="edit_shipping" width="100%"  frameborder="0" scrolling="no" hidden="true">
			</iframe>

			<form id="send_token" action="" method="post" target="load_profile" >
				<input type="hidden" name="token" value="<?php echo $response->token ?>" />
				<input type="hidden" name="paymentProfileId" value="" />
				<input type="hidden" name="shippingAddressId" value="" />
			</form>
		</div>

	</div> 
</body>
</html>
