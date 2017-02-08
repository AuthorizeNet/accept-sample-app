<?php
    session_start();
    $newURL="index.php";
	if(isset($_POST['submit'])) 
	{
		if(isset($_POST['cookieCheck'])){
			setcookie("cpid",$_POST['form-username'], time() + (86400*7), "/");
			header('Location: '.$newURL);
		}
		else{
			setcookie("temp_cpid",$_POST['form-username'], 0, "/");
			header('Location: '.$newURL);
		}
	}

	if(isset($_COOKIE['cpid'])){
		header('Location: '.$newURL);
	}
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Accept Profile Sample App</title>

        <!-- CSS -->
        <link rel="stylesheet" href="scripts/bootstrap.min.css">
		<link rel="stylesheet" href="scripts/form-elements.css">
        <link rel="stylesheet" href="scripts/style_sheet.css">


        <!-- Javascript -->
        <script src="scripts/jquery-2.1.4.min.js"></script>
        <script src="scripts/bootstrap.min.js"></script>
        <script src="scripts/jquery.backstretch.min.js"></script>
        <script src="scripts/localscripts.js"></script>

    </head>

    <body>
        <!-- Top content -->
        <div class="top-content">
        	
            <div class="inner-bg">
                <div class="container">
                    <div class="row">
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 form-box" >
                        	<div style="box-shadow: 15px 20px 10px #222;">
                        	<div class="form-top">
                        		<div class="form-top-left">
                        			<h3>Login to the Accept Sample App</h3>
                            		<p>Enter your Customer ID below</p>
                        		</div>
                            </div>
                            <div class="form-bottom">
			                    <form role="form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" class="login-form">
			                    	<div class="form-group">
			                    		<label class="sr-only" for="form-username">Customer ID</label>
			                        	<input type="text" name="form-username" placeholder="Customer ID .." class="form-username form-control" id="form-username">
			                        	<input type="checkbox" name="cookieCheck" value=""><label>&nbsp; Remember Me</label><br><span style="color:red"><?php if($_SESSION["cpid_error"]=='true'){ echo "Customer Profile ID not Valid"; } ?></span>
			                        </div>
			                        <button type="submit" name="submit" class="btn">Sign in !</button>
			                    </form>
		                    </div>
		                    </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

    </body>

</html>