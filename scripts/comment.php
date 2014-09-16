<?php
        
    require '../lib/setters.php';
    setSession();

    // Create our Application instance (replace this with your appId and secret).
    $facebook = fbInit();

    // Get User ID
    $user = $facebook->getUser();
    $accessToken = $facebook->getAccessToken();
    $logoutUrl = $facebook->getLogoutUrl(array('next'=>'logout.html'));

    $db = mysqlConnector();
    $sql = "SELECT coins FROM users WHERE user_id = '".$user."'";   
    $statement = $db->prepare($sql);    
    $statement->execute();
    $result = $statement->fetchAll();
    $coins = $result[0]['coins'];
    echo "<script>coins = " . $coins . "; nextPage = 'play'</script>";
   
    echo "<div class='playDiv'>";
        echo "<table id='pictureComments'>";
                echo "<tr>";
                    echo "<td>";
                        echo "<span class='headline'>Say something about<br></span>";
                        echo "<img id='unknown' class='personImg' style='margin:10px;' src='https://graph.facebook.com/".$_SESSION['top_friends'][$_SESSION['i']]['uid']."/picture?type=large'><br>";
                        echo "<span class='headline' style='font-size:50px;'>". $_SESSION['top_friends'][$_SESSION['i']]['name'] . "</span>";
                    echo "</td>";
                    echo "<td id='commentsTd' style='width:50%; text-align:left; padding-left:50px;'>";

                        echo "<form id='form' action='commit.php' method='POST'>";
                            echo "<input type='text' name='comment'autofocus='autofocus'/><br><br>";
                            echo "<input type='hidden' name='from' value='$user'>";
                            echo "<input type='hidden' name='to' value='".$_SESSION['top_friends'][$_SESSION['i']]['uid']."'> <br>";
                            echo "Make Anonymous &emsp;";
                                
                            if ($coins >= 10) {
                                echo "<input type='checkbox' id='anon' name='anon' value='Yes' /> <br> (Costs 10 coins)";
                            } else {
                                echo "<img height='10px' width='10px' src='http://t2ak.roblox.com/b3427ad800467f6784b422e4b952ef2d'/> <br> (Unavailable, you don't have enough coins)";
                                echo "<input type='hidden' id='anon' name='anon' value='No' />";
                            }

                        echo   "</form>";
                        
                        echo "<div id='submitComment' class='button'>";
                            echo "<a><span>Comment</span>";
                            echo "</a>";
                        echo "</div>";

                    echo "</td>";
                echo "</tr>";
            echo "</table>";



        //echo "<table border=0 cellpadding=20>"; 
        //echo "<tr><td><button id='next_comment' style='width:150px;height:45px;'>NEXT >></button></td><td>";
                //echo "</td></tr><tr><td>";
        //echo "<img src='https://graph.facebook.com/".$_SESSION['top_friends'][$_SESSION['i']]['uid']."/picture?type=large'> ";
        //echo "<br><br><h3>" .$_SESSION['top_friends'][$_SESSION['i']]['name']."</h3><br></td><td valign='top'><br><br><br>";

        //echo "</td></tr></table>";
        
        $_SESSION['i'] = ($_SESSION['i'] == 99) ? 0 : $_SESSION['i']+1 ;
    echo "</div>";

?>
