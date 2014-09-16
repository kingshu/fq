<?php
    require '../lib/setters.php';
    setSession();

    $printed = false;
    $got4 = false;
	$db = mysqlConnector();
    
    $facebook = fbInit();

    // Get User ID
    $user = $facebook->getUser(); 
    $accessToken = $facebook->getAccessToken();
    $logoutUrl = $facebook->getLogoutUrl(array('next'=>'logout.html'));
    
    $sql = "SELECT coins FROM users WHERE user_id = '".$user."'";   
    $statement = $db->prepare($sql);    
    $statement->execute();
    $result = $statement->fetchAll();
    $coins = $result[0]['coins'];
    echo "<script>coins = " . $coins . "; nextPage = 'comment';</script>";
    
	foreach ($_SESSION['tfIDs'] as $fnd) {                  // Pick one friend at a time
		$in = 0 ;
		$comArr = array();									// Empty out the arrays
		$indxs = array();
		$alrdyCom = array();
		foreach ($_SESSION['comments'] as $comm) {			// Go through each mysql result of comments
			if ( $comm['to'] == $fnd && !in_array($comm['from'], $alrdyCom) ) {						// If the 'to' in the result is same as the picked friend and 'from' has not already commented
				array_push ($comArr, $comm) ;				// Put the comment in comArr
				array_push ($indxs, $in) ;					// And put the index of that comment in indxs
				array_push ($alrdyCom, $comm['from']) ;		// And put the commenter in already commented pile
			}
			if ( count($comArr) == 4 ) {					// Break when we get 4 comments
				break ;
			}
			$in++ ;
		}
		if ( count($comArr) == 4 ) {
			$got4 = true;
            foreach ($indxs as $ind)                          // Unset those comments for this session (avoid repition)
                unset ($_SESSION['comments'][$ind]);
            $_SESSION['comments'] = array_values ($_SESSION['comments']);   // Normalize indexing
			shuffle ($_SESSION['tfIDs']);
			break;
		}	
	}	

    echo "<form id='theRealForm' action='answer.php' method='post' style='display:none;'>";
    echo 	"<input type='hidden' name='user' value='".$user."'>";
    echo 	"<input type='hidden' id='correctness' name='correct' value='wrong'>";
    //echo 	"<button id='next_play' style='width:150px;height:45px;'> NEXT >> </button>";
    echo "</form>";

// div before answer with ? pic
    echo "<div id='Qimage'></div>";
			
	echo "<div class='playDiv'>";
            echo "<table id='pictureComments' style='display:none;'>";
                echo "<tr>";
                    echo "<td>";
                        echo "<span id='answerSpan' class='headline' style='display:none;'></span>";
                        echo "<div class='flipbox-container'>";
                            echo "<img id='unknown' class='personImg' src='../css/silhouette.jpg'>";
                        echo "</div>";
                        echo "<div id='nextButton' class='button' style='display:none;'>";
                            echo "<a><span>Next     ></span>";
                            echo "</a>";
                        echo "</div>";
                    echo "</td>";
                    echo "<td id='commentsTd' style='width:50%; text-align:left;'>";
                    echo "<span class='headline'>";
                        echo "here's what people are saying...<br>";
                    echo "</span>";
					
                    	if ( !$got4 )	{			// if 4 comments couldn't be found 
                    		// Do all the profile pic shenanigans
                            while (!$printed) {
                                $rFnd = $_SESSION['tfIDs'][rand (0, count($_SESSION['tfIDs'])-1)];      // Pick a friend
                                $albums = $facebook->api('/'.$rFnd.'/albums',array('access_token'=>$accessToken));  // Get albums of said friend
                                $profilePics = $facebook->api('/'.$albums['data'][0]['id'].'/photos',array('access_token'=>$accessToken));  // Get 0th album (profile pics) of friend

                                foreach ($profilePics['data'] as $p) {                  // Go through each profile pic

                                    if (isset($p['comments'])) {        // if there are comments on chosen pic
                                    
                                        $alrdyCom = array();
                                        $comArr = array();
                                        
                                        foreach ($p['comments']['data'] as $comt) {         // Go through each comment
                                            if ( !($comt['from']['id']==$user || $comt['from']['id']==$rFnd || in_array($comt['from']['id'],$alrdyCom)) ) {  // If the chosen comment is suitable 
                                                array_push ($comArr, $comt);   // Push the comment into comArr
                                                if ( count($comArr) >= 4 )
                    								break;
                                                array_push ($alrdyCom, $comt['from']['id']);   // and the commenter into alrdyCom
                                            }
                                        }

                                        if ( count($comArr) >= 4 ) {   // If 4 comments on the pic
                                            
                                            $opts = array($rFnd);   // Start opts array with chosen friend (correct ans).
                                            
                                            $_SESSION['hidden']['Rname'] = $p['from']['name'];
                                            $_SESSION['hidden']['Rimage'] = $p['images'][5]['source'];
                                            
                                            // div after answer with real profile pic
                                            //echo "<div id='Rimage'></div>";
                                        
                                            // Display Comments
                                            $i=0;
                                            foreach ($comArr as $comt) {
                                                
                                                $wordsInName = explode(" ", $p['from']['name']);
                                                
                                                //Redact the name when bitches put spoilers in comments
                                                for ($k=0; $k<count($wordsInName); $k++) {
                                                     $comt['message'] = preg_replace("/ ".$wordsInName[$k]." /i", "[NAME REDACTED]", $comt['message']);
                                                }

                                                //echo "<div class='comment' style='margin-left:" . (($i % 2 == 0 ? (-350/2-250) : (-350/2+225))+rand(-10, 10)) . "px; margin-top:" . (($i >= 2 ? (-75/2+75) : (-75/2-75))+rand(-10, 10)) . "px;'>";
                                                echo "<div class='comment' style='opacity:0.0; margin-left:50px'>";
                                                	echo "<table>";
                                                		echo "<tr>";
                                                			echo "<td rowspan='2' class='picture'>";
                                                				echo "<img src='https://graph.facebook.com/".$comt['from']['id']."/picture'/>";
                                                			echo "</td>";
                                                			echo "<td class='personName'>";
                                                				echo $comt['from']['name'];
                                                			echo "</td>";
                                                		echo "</tr>";
                                                		echo "<tr>";
                                                			echo "<td>";
                                                				echo $comt['message'];
                                                			echo "</td>";
                                                		echo "</tr>";
                                                	echo "</table>";
                                                	//echo "<div class='triangleThing' style='bottom:-25px; " . ($i % 2 == 0 ? "right" : "left") . ":-50px; background-image:url(\"../css/triangleThing" . $i%2 . ".png\")'></div>";
                                                    //echo "<div class='triangleThing' style='bottom:-25px; right:-50px; background-image:url(\"../css/triangleThing0.png\")'></div>";
                                                echo "</div>";
                                                $i++;       
                                            }             
                                            $printed=true;
                                            break; 
                                        } // End of if (count($p['comments']['data']) >= 4)                       
                                    }  // End of if (isset($p['comments']))
                                }  // End of foreach ($profilePics['data'] as $p)
                            } // End of while (!$printed)
                        } // End of if  
                        else { 									// else use them motherfuckin' comments!		
                    		//print_r ($comArr);
                    //		$comArr[0]['to'] = '684838147';		
                    		$rFnd = $comArr[0]['to'];
                    		$tName = $facebook->api('/'.$rFnd);
                    		
                    		$_SESSION['hidden']['Rname'] = $tName['name'];
                            $_SESSION['hidden']['Rimage'] = "https://graph.facebook.com/".$comArr[0]['to']."/picture?type=large";
                                            
                    		
                    		echo "<div id='Rimage'></div>";
                    		echo "</td><td>";
                    		foreach ($comArr as $com) {
                    			$frm = $facebook->api('/'.$com['from']);
                    			$wordsInName = explode(" ", $tName['name']);
                    			for ($k=0; $k<count($wordsInName); $k++) {
                    				 $com['comment'] = preg_replace("/".$wordsInName[$k]."/i", "[NAME REDACTED]", $com['comment']);
                    			}
                    			if ($frm['id'] != '0') 
                    				echo "<img src='https://graph.facebook.com/".$frm['id']."/picture'/>&emsp; <b>".$frm['name']."</b> : &nbsp; ". $com['comment']."<br><br>" ;
                    			else
                    				echo "<img height='50px' width='50px' src='http://www.chapman.edu/scst/_files/unisex-silhouette.jpg'/>&emsp; <b>Anonymous</b> : &nbsp; ". $com['comment']."<br><br>" ;
                    		}
                    		$opts = array($comArr[0]['to']);
                    	
                    	} // End of else
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
            echo "<div id='nothing' style='display:none'></div>";
    						
    		// Create options     
    		$rNums = array($opts[0]);
    		for ($j=0; $j<3; $j++) {
    			do {
    				$rNum = rand (0, count($_SESSION['top_friends'])-1);
    			} while (in_array($rNum, $rNums));
    			array_push ($rNums, $rNum);
    			array_push ($opts, $_SESSION['top_friends'][$rNum]['uid']);
    		}
    		shuffle($opts);

    	echo "<div class='buttonsDiv'>";
        echo "<span class='headline'>";
            echo "...who are they talking about?<br>";
        echo "</span>";
    		// Display options
    		foreach ($opts as $opt) {
    			$optUsr = $facebook->api ('/'.$opt);
                $divId = ($opt == $rFnd) ? "c" : "w";
    			echo "<div class='button " . $divId . "' style='opacity:0.0; margin-bottom:-20px;'>";
    				echo "<a><table><tr>";
    					echo "<td><img src='https://graph.facebook.com/".$opt."/picture'/></td>";
    					echo "<td style='padding-left:10px;'><span>" . $optUsr['name'] . "</span></td>";
    				echo "</tr></table></a>";
    			echo "</div>";
    		} 
    	echo "</div>";
    echo "</div>";
?>