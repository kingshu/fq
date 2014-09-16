<html>
	<head>
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script> 
		<script src="http://malsup.github.com/jquery.form.js"></script>
		<script src="../js/jquery.flippy.js"></script>
<script>
    
	var coins = 0;
	var nextPage = "comment";

    $(document).ready(function() {

    	$('#header').slideDown(1000);
    	$('#footer').slideDown(1000);
    	$('#loadingDiv').fadeIn(500);
    	$('.title').mouseenter(function() {
			$(this).fadeTo('fast', 0.7, function() { });
		});
		$('.title').mouseleave(function() {
			$(this).fadeTo('fast', 1.0, function() { });
		});
		$('.button').mouseenter(function() {
			$(this).fadeTo('fast', 0.7, function() { });
		});
		$('.button').mouseleave(function() {
			$(this).fadeTo('fast', 1.0, function() { });
		});
		
	//	window.url = 'http://fq-kingshu.rhcloud.com';
		
		$('#mainContainer').load( $('#load').val()+'.php', function() {

			$('#coinsSpan').text(coins);
			$('#coins').animate({
				opacity: "1.0",
				marginTop: "10px"
			}, 500);

			var updateCoins = function(num) {
				coins += num;
				var correct;
				if(num == 0) {
					return;
				} else if(num > 0) {
					correct = true;
				} else {
					correct = false;
				}
				$('#coinsSpan').css("color", correct ? "green" : "red");
				$('#coinsSpan').animate({
					opacity: "0.0",
					marginTop: (correct ? "-" : "") + "10px",
					marginBottom: "-10px"
				}, 250, function() {
					if(correct) {
						$('#coinsSpan').css("margin-top", "10px");
					} else {
						$('#coinsSpan').css("margin-top", "-10px");
						$('#coinsSpan').css("margin-bottom", "-10px");
					}
					$('#coinsSpan').text(coins);
					$('#coinsSpan').animate({
						opacity: "1.0",
						marginTop: "0px",
						marginBottom: "0px"
					}, 250, function() {
						$('#coinsSpan').css("color", "#E8CAA4");
					});
				});
			};

			var sharedFunctions = function() {

	      		$('.button').mouseenter(function() {
	      			$(this).css("cursor", "pointer");
	      		});
			
				$('.button').mouseleave(function() {
	      			$(this).css("cursor", "auto");
	      		});
	      	};

			// ---- PLAY SCRIPTS ----//
			var playListeners = function() {
				
				$('#loadingDiv').stop(true, false).fadeOut(500);
				sharedFunctions();

				$('#mainContainer').fadeIn("fast");

				$('.c').click(function() {
					$('#correctness').val('correct');
				});

				$('#nextButton').click(function() {
					$('#theRealForm').submit();
				});

				function flipImage() {
					$('#nothing').load('hidden.php', function(result) {
					    var dataUrl = $('#nothing').html().split("~!|!~");
					    
					    // preload image so that the user doesn't have to wait after it flips
					    img1 = new Image();
					    img1.src = dataUrl[1];

					    $("#unknown").flippy({
					    	color_target: "#351330",
					    	duration: "500",
					    	onMidway: function() {
					    		$("#unknown").attr("src", "");
					    		$("#unknown").attr("src", dataUrl[1]);
					    		showAnswer(dataUrl[0]);
					    	}
					    });
					});
				}

				function showAnswer(answer) {
					$("#answerSpan").html("It is...<br><span style='font-size:50px;'>" + answer + "!</span>");
					$("#answerSpan").fadeIn();
				}

				$('#pictureComments').slideDown();

				$('#pictureComments').ready(function() {
					var comments = $('.comment');
					var i = 0;
					(function displayNextComment() {  
	         			comments.eq(i++).animate({
								opacity: "1.0",
								marginLeft: "0px"
							}, 150);
						setTimeout(function() { displayNextComment(); }, 100); 
	      			})();
	      		});  

				$('.buttonsDiv').ready(function() {
					setTimeout(function() {
						var buttons = $('.buttonsDiv .button');
						var i = 0;
						(function displayNextButton() {  
		         			buttons.eq(i++).animate({
									opacity: "1.0",
									marginBottom: "10px"
								}, 150);
							setTimeout(function() { displayNextButton(); }, 100); 
		      			})();
		      		}, 300);
	      		});

	      		$('.buttonsDiv .button').mouseenter(function() {
					$(this).animate({
						marginTop: "-=10",
						marginBottom: "+=10"
					}, 200, function() {

					});
				});

				$('.buttonsDiv .button').mouseleave(function() {
					$(this).animate({
						marginTop: "+=10",
						marginBottom: "-=10"
					}, 200, function() {

					});
				});

				$('.buttonsDiv .button').click(function() {
					flipImage();
					$('#commentsTd').fadeTo("fast", "0.3");
					$('.buttonsDiv').fadeTo("fast", "0.3");
					$(this).mouseleave();
					$('.buttonsDiv .button').off();
					setTimeout(function(){$('#nextButton').slideDown("fast")}, 500);
				});

				$('#theRealForm').ajaxForm(function() { 
					if($('#correctness').val() == "correct") {
						updateCoins(1);
					} else {
						//updateCoins(-5);
					}
					$('#mainContainer').stop(true, false).fadeOut("fast", function() {
						$('#loadingDiv').stop(true, false).fadeIn(500);
						$('#mainContainer').load('comment.php', comListeners);
					});
					console.log("Play form submitted via ajax");
				});
        
			}
			
			// ------ COMMENT SCRIPTS ------- //			
			var comListeners = function () {

				$('#loadingDiv').stop(true, false).fadeOut(500);
				sharedFunctions();

				$('#mainContainer').fadeIn("fast");

				$('#submitComment').click(function() {
					$('#form').submit();
				});
				
				$('#form').ajaxForm(function() { 
					if($('#anon').attr('checked')) {
						updateCoins(-10);
					} else {
						updateCoins(2);
					}
					$('#mainContainer').stop(true, false).fadeOut("fast", function() {
						$('#loadingDiv').stop(true, false).fadeIn(500);
						$('#mainContainer').load('play.php', playListeners);
					});
					console.log("Comment form submitted via ajax");
				});
			}
			
			if(nextPage == "play") {
				comListeners();
			} else {
				playListeners();
			}

		});			
		  
	});
	</script>
		<title>Who's that friend?</title> 
    		<link rel="stylesheet" type="text/css" href="../css/fonts/fonts.css">
    		<link rel="stylesheet" type="text/css" href="../css/container.css">
    		<link rel="stylesheet" type="text/css" href="../css/play.css">
	</head>
	<body>
		<div id='header' style='display:none;'>
			<div style='display:none'>
				<input id='load' value='<?php echo $_REQUEST['load'] ?>' type='hidden'>
			</div>
			<a class="title" href="/">who's that friend?</span>
			<div class="button">
				<a href='<?php echo urldecode($_REQUEST['logouturl']); ?>'>logout</a>
			</div>
			<div id="coins" style="opacity:0.0; margin-top:-10px;">
				<span id='coinsSpan'>?</span><span id='coinsWordSpan'>coins</span>
			</div>
			<div style='display:none'>
				<input id='load' value='<?php echo $_REQUEST['load'] ?>' type='hidden'>
			</div>
		</div>
		
		<div id='mainContainer'>
		</div>
		<div id="loadingDiv" style='display:none'>
			<img src="../css/loading.gif">
		</div>
		
		<div id='footer' style='display:none'>
			<span>Copyright 2013 KingShu, LLC</span>
			<!--<br><a href="container.php?load=invite">Invite friends</a>
			&emsp;Get <b>50</b> coins for each friend that joins!
			<br>
			<a href='<?php echo urldecode($_REQUEST['logouturl']); ?>'>Logout</a>  -->
		</div>
	</body>
</html>
