<!DOCTYPE html>
<html>
<head>
	<title>Main Page</title>
	<link rel="icon" href="/resources/U.png">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<style>
	body {
		background: #d8d8d8;
	}
	
	input[type=text], input[type=password] {
		width: 100%;
		padding: 12px 20px;
		margin: 8px 0;
		display: inline-block;
		border: 1px solid #ccc;
		box-sizing: border-box;
	}
	
	.centerDiv {
		position: fixed;
		top: 30%;
		width: 100%;
	}
	
	.centerDiv .button, .centerDiv img {
		display: block;
		margin-left: auto;
		margin-right: auto;
		width: 40%;
	}
	
	.button {
		display: block;

		width: 200px;
		height: 50px;
		margin-top: 5px;
		background: #9D2235;
		border: none;
		color: #fff;
	    cursor: pointer;
		font-size: 14px;
		font-weight: bold;
	}

	.button:hover {
		background: #BD4255;
	}
	
	/*** MODAL ***/
	
	.modal {
		position: fixed; 
		z-index: 1; 
		left: 0;
		top: 0;
		width: 100%; 
		height: 100%; 
		overflow: auto; 
		background-color: rgb(0,0,0); 
		background-color: rgba(0,0,0,0.4); 
		padding-top: 60px;
	}

	.modal-content {
		background-color: #fefefe;
		margin: 5% auto 15% auto; 
		border: 1px solid #888;
		width: 80%; 
	}

	.modal-button {
	    background-color: #4CAF50;
	    color: white;
	    padding: 14px 20px;
	    margin: 8px 0;
	    border: none;
	    cursor: pointer;
	    width: 100%;
	}

	.modal-button:hover {
		background: #6CCF70;
	}

	.container {
		padding: 16px;
	}
	
	span.forgot {
		font-family: arial;
		float: right;
		padding-top: 16px;
	}

	.close-button {
	    color: white;
	    margin: 8px 0;
	    border: none;
	    cursor: pointer;
	    width: auto;
	    padding: 10px 18px;
	    background-color: #f44336;
	}

	.close-button:hover {
		background: #ff6356;
	}

	/* Animation */
	.animate {
	    -webkit-animation: animatezoom 0.6s;
	    animation: animatezoom 0.6s
	}

	@-webkit-keyframes animatezoom {
	    from {-webkit-transform: scale(0)}
	    to {-webkit-transform: scale(1)}
	}
	    
	@keyframes animatezoom {
	    from {transform: scale(0)}
	    to {transform: scale(1)}
	}

	/* Media Screens */
	@media screen and (max-width: 300px) {
		span.forgot {
			display: block;
			float: none;
		}
	    .close-button {
	       width: 100%;
	    }
	}
	</style>
</head>
<body>
	<button type="button" id="admin" class="button" onclick="gotoAdmin()">Admin</button>
	<form action="index.php" method='post'>
		<button type="submit" class="button">Log Out</button>
		<input type="hidden" name="logOutBool" />
	</form>

	<div class="centerDiv">
		<img src="resources/uams.jpg" alt="UAMS logo">
		<button type="button" id="test" class="button" onclick="gotoTest()">Start Test</button>
	</div>
	
	<div id="adminModal" class="modal" style="display: none">
		<form class="modal-content animate" action="index.php" method='post'>
			<div id="adminContainer" class="container">
				<label style="font-family: arial"><b>Admin</b></label>
				<input type="password" placeholder="Enter Password" name="password" required>
			</div>
			
			<div class="container">
				<button type="submit" class="modal-button">Login</button>
				<input type="hidden" name="adminLoginBool" />
			</div>
			<div class="container" style="background-color:#f1f1f1">
				<button type="button" onclick="hideModals()" class="close-button">Close</button>
				<span class="forgot"><a href="#" onclick="forgotPassword()">Forgot password?</a></span>
			</div>
		</form>
	</div>
	
	<div id="clientModal" class="modal" style="display: none">
		<form class="modal-content animate" action="index.php" method='post'>
			<div id="clientContainer" class="container">
				<label style="font-family: arial"><b>Client</b></label>
				<input type="text" placeholder="Enter Id Number" name="idNumber" required>
			</div>
			
			<div class="container">
				<button type="submit" class="modal-button">Login</button>
			</div>
			<div class="container" style="background-color:#f1f1f1">
				<button type="button" onclick="hideModals()" class="close-button">Close</button>
			</div>
		</form>
	</div>

	<?php
	session_start();
	include 'database.php';

	if ( isset( $_POST['logOutBool'] ) )
	{
		session_unset();
	}

	if ( isset( $_POST['password'] ) && isset( $_POST['adminLoginBool'] ) )
	{
		loginAsAdmin();
	}

	if ( isset( $_SESSION['sessionDate'] ) )
	{
		printAlert( "Cannot take test more than once a day." );
		unset( $_SESSION['sessionDate'] );
	}

	function loginAsAdmin()
	{
	    if ( empty( $_POST['password'] ) )
	    {
	        return false;
	    }
	     
	    $password = trim( $_POST['password'] );
	    if ( !checkPassword( $password ) )
	    {
			printAlert( "Password is Invalid." );
	        return false;
	    }
	    
	    session_unset(); 
	    $_SESSION['adminLogin'] = 'admin';
	    header("Location: http://uams.webutu.com/admin.php");
	     
	    return true;
	}

	function checkPassword( $password )
	{
	    if ( isValidPassword( $password ) )
	    {
	    	return true;
	    }

	    return false;
	}

	if ( isset( $_POST['idNumber'] ) && !isset( $_POST['adminLoginBool'] ) )
	{
		loginAsClient();
	}

	function loginAsClient()
	{
	    if ( empty( $_POST['idNumber'] ) )
	    {
	        return false;
	    }
	     
	    $idNumber = trim( $_POST['idNumber'] );
	    if ( !checkId( $idNumber ) )
	    {
			printAlert( "ID Number is Invalid." );
	        return false;
	    }
	    
	    session_unset(); 
	    $_SESSION['clientLogin'] = $idNumber;
	    header("Location: http://uams.webutu.com/test.php");
	     
	    return true;
	}

	function checkId( $id )
	{
	    if ( strtolower( $id ) === 'admin' || isValidId( $id ) )
	    {
	    	return true;
	    }

	    return false;
	}
	
	function printAlert( $message )
	{
		echo '<script type="text/javascript">alert("' . $message . '"); </script>';
	}
	?>
	
	<script>
	// When the user clicks anywhere outside of the modal, close it
	var adminModal = document.getElementById('adminModal');
	var clientModal = document.getElementById('clientModal');
	window.onclick = function( event )
	{
	    if ( event.target == adminModal || event.target == clientModal )
	    {
	        hideModals();
	    }
	}

	function logOut()
	{
		if ( <?php echo isset( $_SESSION['adminLogin'] ) ? "true" : "false" ?> )
		{
			window.location.href = 'http://uams.webutu.com/admin.php';
		}
		else
		{
			loginAdmin();
		}
	}

	function forgotPassword()
	{
		hideModals();
		$.post(
			'database.php',
			{
				action: "forgotPassword"
			},
			function ( response ) {
				alert( "A new password has been emailed to your Admin email address. Be sure to check your SPAM folder." );
			}
		);
	}

	function gotoAdmin()
	{
		if ( <?php echo isset( $_SESSION['adminLogin'] ) ? "true" : "false" ?> )
		{
			window.location.href = 'http://uams.webutu.com/admin.php';
		}
		else
		{
			loginAdmin();
		}
	}

	function gotoTest()
	{
		if ( <?php echo isset( $_SESSION['clientLogin'] ) ? "true" : "false" ?> )
		{
			window.location.href = 'http://uams.webutu.com/test.php';
		}
		else
		{
			loginClient();
		}
	}

	function loginAdmin()
	{
		adminModal.style.display = 'block';
	}

	function loginClient()
	{
		clientModal.style.display = 'block';
	}

	function hideModals()
	{
		adminModal.style.display = 'none';
		clientModal.style.display = 'none';
	}
	</script>

</body>
</html>