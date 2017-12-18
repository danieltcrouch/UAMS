<?php

function isValidPassword( $password )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$isValid = false;
	$result = $mysqli->query( "SELECT 1 FROM admin WHERE password = PASSWORD('" . $password . "') AND name = 'admin'" );
	if ( $result && $result->num_rows > 0 )
	{
		$isValid = true;
	}

	return $isValid;
}

function isValidId( $id )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$isValid = false;
	$result = $mysqli->query( "SELECT 1 FROM client_info WHERE client_id = '" . $id . "'" );
	if ( $result && $result->num_rows > 0 )
	{
		$isValid = true;
	}

	return $isValid;
}

function hasTestedToday( $id )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$isValid = false;
	$result = $mysqli->query( "SELECT 1 FROM sessions WHERE client_id = '" . $id . "' AND DATE( date ) = DATE( NOW() )" );
	if ( $result && $result->num_rows > 0 )
	{
		$isValid = true;
	}

	return $isValid;
}

function addId( $id )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$isValid = false;
	$result = $mysqli->query( "INSERT INTO client_info (client_id) VALUES ('" . $id . "')" );
	if ( $result && $result->num_rows > 0 )
	{
		$isValid = true;
	}

	return $isValid;
}

function removeId( $id )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$isValid = false;
	$result = $mysqli->query( "DELETE ci, s FROM client_info ci LEFT JOIN sessions s ON s.client_id = ci.client_id WHERE ci.client_id = '" . $id . "'" );
	if ( $result && $result->num_rows > 0 )
	{
		$isValid = true;
	}

	return $isValid;
}

function removeAllIds()
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$isValid = false;
	$result = $mysqli->query( "DELETE ci, s FROM client_info ci LEFT JOIN sessions s ON s.client_id = ci.client_id " );
	if ( $result && $result->num_rows > 0 )
	{
		$isValid = true;
	}

	return $isValid;
}

function random_str( $length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' )
{
    $str = '';
    $max = mb_strlen( $keyspace, '8bit' ) - 1;
    for ( $i = 0; $i < $length; ++$i) {
        $str .= $keyspace[ random_int( 0, $max ) ];
    }
    return $str;
}

function forgotPassword()
{
	$newPassword = random_str( 16 );
	changePassword( $newPassword );
	
	$recipient = getEmailAddress();
	$subject = "Reset Password";
	$message = "Your password has been set to: " . $newPassword . ". Please use this to login and reset your password.";
	$message = wordwrap( $message, 70 );
	$headers = "From: UAMS<noreply@uams.webutu.com>";
	$headers .= 'MIME-Version: 1.0' . "";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "";
	
	return mail($recipient, $subject, $message, $headers);
}

function changePassword( $password )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	return $mysqli->query( "UPDATE admin SET password = PASSWORD('" . $password . "') WHERE admin.name = 'admin'" );
}

function changeEmail( $email )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	return $mysqli->query( "UPDATE admin SET email = '" . $email . "' WHERE admin.name = 'admin'" );
}

function stringifyStats( $row )
{
	$date = ( new DateTime( $row['date'] ) )->format('Y-m-d');
	return $row['client_id'] . "," .
			$row['group_id'] . "," .
			$date . "," .
			$row['answers'] . "," .
			$row['times'];
}

function labelStats( $row )
{
	$date = ( new DateTime( $row['date'] ) )->format('Y-m-d');
	return "Client ID: " . $row['client_id'] . "; " .
			"Group: " . $row['group_id'] . "; " .
			"Date: " . $date . "; " .
			"Answers: " . $row['answers'] . "; " .
			"Times: " . $row['times'];
}

function getStats( $id )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$result = "";
	$queryResult = $mysqli->query( "SELECT * FROM sessions WHERE client_id = '" . $id . "'" );
	if ( $queryResult && $queryResult->num_rows > 0 )
	{
		while( $row = $queryResult->fetch_array() )
		{
			$result .= labelStats( $row ) . "\n\n";
		} 
	}

	return $result;
}

function getAllStats()
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$result = "";
	$queryResult = $mysqli->query( "SELECT * FROM sessions WHERE client_id <> 'admin'" );
	if ( $queryResult && $queryResult->num_rows > 0 )
	{
		while( $row = $queryResult->fetch_array() )
		{
			$result .= stringifyStats( $row ) . "\n";
		} 
	}

	return $result;
}

function getEmailAddress()
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$row = null;
	$result = $mysqli->query( "SELECT email FROM admin LIMIT 1" );
	if ( $result && $result->num_rows > 0 )
	{
		$row = $result->fetch_assoc();
	}

	return $row['email'];
}

function changeGroup( $id, $group )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	return $mysqli->query( "UPDATE client_info SET group_id = '" . $group . "' WHERE client_id = '" . $id . "'" );
}

function getGroup( $id )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$row = null;
	$result = $mysqli->query( "SELECT group_id FROM client_info WHERE client_id = '" . $id . "'" );
	if ( $result && $result->num_rows > 0 )
	{
		$row = $result->fetch_assoc();
	}

	return $row['group_id'];
}

function getGroups()
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	
	$groups = array();	
	if ( $result = $mysqli->query( "SELECT * FROM client_info" ) ) {
		while ( $row = $result->fetch_assoc() ) {
			$groups[ $row["client_id"] ] = $row["group_id"];
		}
		$result->free();
	}

	return json_encode( $groups );
}

function insertStats( $id, $group, $answers, $times )
{
	$mysqli = new mysqli( 'localhost', 'id167914_dcrouch1', 'password', 'id167914_uams' );
	$mysqli->query( "INSERT INTO sessions ( client_id, group_id, answers, times ) VALUES ( '" . $id . "', '" . $group . "', '" . $answers . "', '" . $times . "' )" );

	return "true";
}

if ( isset( $_POST['action'] ) && function_exists( $_POST['action'] ) )
{
	$action = $_POST['action'];
    $result = null;
	
	if ( isset( $_POST['id'] ) && isset( $_POST['group'] ) && isset( $_POST['answers'] ) && isset( $_POST['times'] )  )
	{
		$result = $action( $_POST['id'], $_POST['group'], $_POST['answers'], $_POST['times'] );
	}
	elseif ( isset( $_POST['id'] ) && isset( $_POST['group'] ) )
	{
		$result = $action( $_POST['id'], $_POST['group'] );
	}
	elseif ( isset( $_POST['id'] ) )
	{
		$result = $action( $_POST['id'] );
	}
	else
	{
		$result = $action();
	}
	
	echo $result;
}
elseif ( isset( $_POST['id'] ) )
{
	$isValid = isValidId( $_POST['id'] );
	echo $row['test_count'];
}

?>