<!DOCTYPE html>
<html>
<head>
	<title>Administration</title>
	<link rel="icon" href="/resources/U.png">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<style>
	body {
		background: #d8d8d8;
	}
	
	input[type=text], input[type=password] {
		width: 100%;
		padding: 12px 20px;
		display: inline-block;
		border: 1px solid #ccc;
		box-sizing: border-box;
	}
	
	.centerDiv {
		position: fixed;
		top: 30%;
		width: 100%;
	}
	
	.centerDiv .button, .centerDiv input, .centerDiv textarea {
		margin-left: 30%;
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
	
	span.extra {
		font-family: arial;
		float: right;
		padding-top: 16px;
	}

	.groupContainer {
	}

	.groupContainer label {
		padding: 12px 20px;
		font-family: arial;
	}

	.groupContainer input {
		float: none;
		width: 60px;
		text-align: center;
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

	<?php
	session_start();
	if ( !isset( $_SESSION['adminLogin'] ) ) { 
    		header( "Location: http://uams.webutu.com/" );
	}
	
	include 'database.php';
	?>
	
	<button type="button" id="exit" class="button" onclick="gotoMain()">Exit</button>
	<button type="button" id="editIds" class="button" onclick="editIds()">Edit ID Numbers</button>
	<button type="button" id="editGroups" class="button" onclick="editGroups()">Edit Groups</button>
	<button type="button" id="editAdmin" class="button" onclick="editAdmin()">Edit Admin Profile</button>
	<button type="button" id="adminLogin" class="button" onclick="downloadFileStart()">Download Spreadsheet</button>

	<div class="centerDiv">
		<input type="text" id="idNumber" placeholder="Enter Id Number" />
		<button type="button" id="treatmentLogin" class="button" onclick="displayStats()">Get Stats</button>
		<br />
		<textarea id="clientStats" style="display: none;" readonly></textarea> 
	</div>
	
	<div id="idModal" class="modal" style="display: none">
		<div class="modal-content animate">
			<div class="container">
				<label style="font-family: arial"><b>ID Numbers to Add</b></label>
				<input type="text" placeholder="0123,456,789" id="idNumbersAdd">
			</div>
			<div class="container">
				<label style="font-family: arial"><b>ID Numbers to Remove</b></label>
				<input type="text" placeholder="0123,456,789" id="idNumbersRemove">
			</div>
			
			<div class="container">
				<button type="button" class="modal-button" onclick="submitIds()">Submit</button>
			</div>
			<div class="container" style="background-color:#f1f1f1">
				<button type="button" class="close-button" onclick="hideModals()">Close</button>
				<span class="extra"><a href="#" onclick="removeAll()">Remove All</a></span>
			</div>
		</div>
	</div>
	
	<div id="groupModal" class="modal" style="display: none">
		<div class="modal-content animate">
			<div id="groupsContainer" class="container" style="overflow-y:scroll; height:400px;">
				<script>
				$.post(
					'database.php',
					{
						action: "getGroups"
					},
					function ( response ) {
						setGroupHTML( $.parseJSON( response ) );
					}
				);

				function setGroupHTML( groups )
				{
					var text = "";
					var keys = Object.keys( groups );
					for (var i = 0; i < keys.length; i++) {
						if ( keys[i].toLowerCase() != 'admin' )
						{
							text += "<div class='groupContainer'>";
							text += "<label><b> Group for Client " + keys[i] + " </b></label>";
							text += "<input type='text' placeholder='" + groups[keys[i]] + "' maxlength='1' name='groups' id='" + keys[i] + "'>";
							text += "</div>";
						}
					}
					
					document.getElementById("groupsContainer").innerHTML = text;
				}
				</script>
			</div>
			
			<div class="container">
				<button type="button" class="modal-button" onclick="submitGroups()">Submit</button>
			</div>
			<div class="container" style="background-color:#f1f1f1">
				<button type="button" onclick="hideModals()" class="close-button">Close</button>
				<span class="extra"><a href="#" onclick="switchAll()">Switch All</a></span>
			</div>
		</div>
	</div>
	
	<div id="adminModal" class="modal" style="display: none">
		<form class="modal-content animate" action="admin.php" method='post'>
			<div class="container">
				<label style="font-family: arial"><b>Old Password</b></label>
				<input type="password" placeholder="Enter Old Password" name="oldPassword" required>
			</div>
			<div class="container">
				<label style="font-family: arial"><b>New Password</b></label>
				<input type="password" placeholder="Enter New Password" name="newPassword">
			</div>
			<div class="container">
				<label style="font-family: arial"><b>Email</b></label>
				<input type="text" placeholder="<?php echo getEmailAddress() ?>" name="email" id="email">
			</div>
			
			<div class="container">
				<button type="submit" class="modal-button">Submit</button>
			</div>
			<div class="container" style="background-color:#f1f1f1">
				<button type="button" onclick="hideModals()" class="close-button">Close</button>
			</div>
		</form>
	</div>
	
	<?php
	if ( isset( $_POST['oldPassword'] ) )
	{
		if ( isValidPassword( $_POST['oldPassword'] ) )
		{
			if ( isset( $_POST['newPassword'] ) && strlen( trim( $_POST['newPassword'] ) ) > 0 )
			{
				changePassword( trim( $_POST['newPassword'] ) );
			}
			if ( isset( $_POST['email'] ) && strlen( trim( $_POST['email'] ) ) > 0 )
			{
				$result = changeEmail( trim( $_POST['email'] ) );
				if ( $result )
				{
					echo '<script type="text/javascript">
					var emailInput = document.getElementsByName("email")[0];
					emailInput.placeholder = "' . $_POST['email'] . '";
					</script>';
				}
			}
		}
		else
		{
			printAlert( "Invalid Password." );
		}
	}
	
	function printAlert( $message )
	{
		echo '<script type="text/javascript">alert("' . $message . '"); </script>';
	}
	?>
	
	<script>
	var clientStats = document.getElementById('clientStats');
	var idNumber = document.getElementById('idNumber');
	
	// When the user clicks anywhere outside of the modal, close it
	var idModal = document.getElementById('idModal');
	var groupModal = document.getElementById('groupModal');
	var adminModal = document.getElementById('adminModal');
	window.onclick = function( event )
	{
	    if ( event.target == idModal || event.target == adminModal || event.target == groupModal )
	    {
	        hideModals();
	    }
	}
	
	$( "#idNumber" ).on( 'keyup', function ( e ) {
		if ( e.keyCode == 13 ) {
			displayStats();
		}
	});
	
	$( "#idNumbersAdd" ).on( 'keyup', function ( e ) {
		if ( e.keyCode == 13 ) {
			submitIds();
		}
	});
	$( "#idNumbersRemove" ).on( 'keyup', function ( e ) {
		if ( e.keyCode == 13 ) {
			submitIds();
		}
	});
	
	function gotoMain()
	{
		window.location.href = 'http://uams.webutu.com/index.php';
	}
	
	function submitIds()
	{
		var idNumbersAdd = document.getElementById('idNumbersAdd').value;
		var idNumbersRemove = document.getElementById('idNumbersRemove').value;
		
		if ( idNumbersAdd.length > 0 )
		{
			var ids = idNumbersAdd.split(",");
			ids.forEach( editId.bind( null, "addId" ) );
		}
		if ( idNumbersRemove.length > 0 )
		{
			var ids = idNumbersRemove.split(",");
			ids.forEach( editId.bind( null, "removeId" ) );
		}
		
		document.getElementById('idNumbersAdd').value = "";
		document.getElementById('idNumbersRemove').value = "";
		hideModals();
	}
	
	function editId( functionName, id )
	{
		id = id.trim();
		if ( id.length > 0 )
		{
			$.post(
				'database.php',
				{
					id: id,
					action: functionName
				},
				function ( response ) {
					return true;
				}
			);
		}
	}
	
	function removeAll()
	{
		if ( confirm( "Are you sure you want to remove all Clients and their stats?" ) )
		{
			$.post(
				'database.php',
				{
					action: "removeAllIds"
				},
				function ( response ) {
					return true;
				}
			);
			hideModals();
		}
	}
	
	function submitGroups()
	{
		var groups = document.getElementsByName( "groups" );
		for ( var i = 0; i < groups.length; i++ )
		{
			if ( groups[i].value.length > 0 )
			{
				editGroup( groups[i].id, groups[i].value );
				groups[i].placeholder = groups[i].value;
				groups[i].value = "";
			}
		}
		hideModals();
	}
	
	function editGroup( id, group )
	{
		$.post(
			'database.php',
			{
				id: id,
				group: group,
				action: "changeGroup"
			},
			function ( response ) {
				return true;
			}
		);
	}
	
	function switchAll()
	{
		var groups = document.getElementsByName( "groups" );
		var groupIds = [];
		for ( var i = 0; i < groups.length; i++ )
		{
			if ( groups[i].value.length > 0 )
			{
				groupIds.push( groups[i].value );
			}
			else if ( groups[i].placeholder.length > 0 )
			{
				groupIds.push( groups[i].placeholder );
			}
			
			$.unique( groupIds );
			groupIds.sort();
			groupIds.map( function( x ){ return x.toUpperCase() } );
		}
		
		if ( groupIds.length == 2 )
		{
			for ( var i = 0; i < groups.length; i++ )
			{
				if ( groups[i].value == groupIds[0] )
				{
					groups[i].value = groupIds[1];
				}
				else
				{
					groups[i].value = groupIds[0];
				}
			}
		}
		else if ( groupIds.length == 1 )
		{
			var newChar = ( isNaN( groupIds[0] ) ) ? ( ( groupIds[0] == "A" ) ? "B" : "A" ) : ( ( groupIds[0] == "1" ) ? "2" : "1" );
			for ( var i = 0; i < groups.length; i++ )
			{
				groups[i].value = newChar;
			}
		}
		else
		{
			for ( var i = 0; i < groups.length; i++ )
			{
				groups[i].value = "A";
			}
		}
	}
	
	function displayStats()
	{
		if ( idNumber && idNumber.value )
		{
			var id = idNumber.value;
			$.post(
				'database.php',
				{
					id: id,
					action: "isValidId"
				},
				function ( response ) {
					if ( response )
					{
						getStats( id );
					}
					else
					{
						alert( "ID is Invalid" );
					}
				}
			);
		}
	}
	
	function getStats( id )
	{
		$.post(
			'database.php',
			{
				id: id,
				action: "getStats"
			},
			function ( response ) {
				setStats( response );
			}
		);
	}
	
	function setStats( result )
	{
		clientStats.style.display = "block";
		clientStats.value = result;
	}
		
	function downloadFileStart()
	{
		$.post(
			'database.php',
			{
				action: "getAllStats"
			},
			function ( response ) {
				response = getTitles( response ) + response;
				downloadFileFinish( response );
			}
		);
	}
		
	function downloadFileFinish( text )
	{
		var url = null;
		var blob = new Blob([text], {type: 'text/csv'});
		if ( url !== null )
		{
			window.URL.revokeObjectURL( url );
		}
		url = window.URL.createObjectURL( blob );
		
		var a = document.createElement("a");
		document.body.appendChild(a);
		a.style = "display: none";
        a.href = url;
        a.download = "All Stats.csv";
        a.click();
		window.URL.revokeObjectURL( url );
	}
	
	function getTitles( response )
	{
		var comma = ",";
		var commaCount = ( response.split( "\n" )[0].split( comma ).length - 3 ) / 2;
		var commas = comma;
		for ( var i = 0; i < commaCount - 1; i++ )
		{
			commas += comma;
		}
		return "Client ID,Group,Date,Answers" + commas + "Time (milliseconds)\n";
	}

	function editIds()
	{
		idModal.style.display = 'block';
	}

	function editGroups()
	{
		groupModal.style.display = 'block';
	}

	function editAdmin()
	{
		adminModal.style.display = 'block';
	}

	function hideModals()
	{
		idModal.style.display = 'none';
		groupModal.style.display = 'none';
		adminModal.style.display = 'none';
	}
	</script>

</body>
</html>