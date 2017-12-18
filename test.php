<!DOCTYPE html>
<html>
<head>
	<title>Entrainment Test</title>
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
	
	.centerDiv button,
	.centerDiv input,
	.centerDiv img,
	.centerDiv p {
		display: block;
		margin-left: auto;
		margin-right: auto;
		width: 40%;
	}
	
	.centerDiv video {
		display: block;
		margin-left: auto;
		margin-right: auto;
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
	</style>
</head>
<body>

	<?php
	session_start();
	if ( !isset( $_SESSION['clientLogin'] ) ) { 
    		header( "Location: http://uams.webutu.com/" );
	}

	include 'database.php';
	if ( hasTestedToday( $_SESSION['clientLogin'] ) && strtolower( $_SESSION['clientLogin'] ) != "admin"  )
	{
		$_SESSION['sessionDate'] = 'used';
		header( "Location: http://uams.webutu.com/" );
	}
	?>
	
	<button type="button" id="exit" class="button" onclick="gotoMain()">Exit</button>

	<div id="mainDiv" class="centerDiv">
		<img id="image" src="/resources/A/1.jpg" style="width: 300px; height: 300px; display: none">
		<video id="video" width="320" height="240" style="display: none" controls>
			<source id="videoSource" src="/resources/A/1.mp4" type="video/mp4">
			Your browser does not support the video tag.
		</video>
		<p id="question" style="font-family: arial; text-align: center; display: none">
			Did the video match the picture?
		</p>
		<button type="button" id="yes" class="button" style="display: none" onclick="evaluateAnswer( 'Y' )">Yes</button>
		<button type="button" id="no" class="button" style="display: none" onclick="evaluateAnswer( 'N' )">No</button>
	</div>

	<script>
	setTimeout( function() {
		start();
	}, 500 );
	
	var round = 1;
		
	var image = document.getElementById('image');
	var video = document.getElementById('video');
	var videoSource = document.getElementById('videoSource');
	var question = document.getElementById('question');
	var yesButton = document.getElementById('yes');
	var noButton = document.getElementById('no');
	
	var correctAnswers = [];
	var group;
	
	var answers = [];
	var times = [];
	var timeElapsed;

	function start()
	{
		$.post(
			'database.php',
			{
				id: "<?php echo $_SESSION['clientLogin'] ?>",
				action: "getGroup"
			},
			function ( response ) {
				group = response;
				readAnswerFile();
				showImage();
			}
		);
	}
	
	function readAnswerFile()
	{
		var file = "/resources/" + group + "/" + group + ".txt";
		$.get( file, function( result ) {
			correctAnswers = result.split( "\n" );
		});
	}
	
	function updateImageAndVideo()
	{
		image.src = "/resources/" + group + "/" + round + ".jpg";
		videoSource.src = "/resources/" + group + "/" + round + ".mp4";
	}

	function showImage()
	{
		updateImageAndVideo();
		image.style.display = "block";
		
		setTimeout( function() {
			showVideo();
		}, 5000 );
	}

	function showVideo()
	{
		image.style.display = "none";
		video.style.display = "block";
		video.play();
		
		video.addEventListener( 'ended', function() {
			setTimeout( function() {
				showQuestion();
			}, 1000 );
		}, false);
		
	}

	function showQuestion()
	{
		question.style.display = "block";
		yesButton.style.display = "block";
		noButton.style.display = "block";
		timeElapsed = Date.now();
	}
	
	function evaluateAnswer( answer )
	{
		timeElapsed = Date.now() - timeElapsed;
		times.push( timeElapsed );
		var isCorrect = ( answer.trim() == correctAnswers[round-1].trim() );
		answers.push( isCorrect );
		
		showResult( isCorrect );
	}

	function showResult( isCorrect )
	{
		video.style.display = "none";
		video.pause();
		video.currentTime = 0;
		question.style.display = "none";
		yesButton.style.display = "none";
		noButton.style.display = "none";
		image.style.display = "block";
		
		if ( isCorrect )
		{
			image.src = "/resources/correct.png";
		}
		else
		{
			image.src = "/resources/incorrect.png";
		}
		
		setTimeout( function() {
			if ( correctAnswers.length > round )
			{
				round++;
				showImage();
			}
			else
			{
				end();
			}
		}, 3000 );
	}

	function end() {
		$.post(
			'database.php',
			{
				id: "<?php echo $_SESSION['clientLogin'] ?>",
				group: group,
				answers: answers.join(),
				times: times.join(),
				action: "insertStats"
			},
			function ( response ) {
				gotoMain();
			}
		);
	}
	
	function gotoMain()
	{
		window.location.href = 'http://uams.webutu.com/index.php';
	}
	</script>
</body>
</html>