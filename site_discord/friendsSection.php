<?php 

require_once('config.php');
session_start();
// echo $_SESSION["email"];
 $row;
 $email=$_SESSION["email"];

{
$sql="SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_assoc($result);

//$thisUsername=$row['username'];
//$thisUsername=mysqli_real_escape_string($link,$_GET['username']);
$activeUsername=$row['username'];

}

?>

<div id="friendsContainer">
    <style>
    .x:hover{
    cursor:pointer;
    }
    
        .friendpp{
    //border-style: solid;
    //border-width:1px;
    //border-color:white;    
    box-shadow: 1px 1px 5px black;
    margin-left:6px;
    }
    
    .friend{
       border-style: solid;
    border-width:2px;
    border-color:white;    
    box-shadow: 1px 1px 5px black;
    margin-left:5px;
    }
    
        div.activity_bubble{
    width:15px;
    height:15px;
    //background-color:green;
    margin-left:5px;
    border-style: solid;
    border-width:1px;
    border-color:black;
    }

    a:link {
        color:inherit;
  text-decoration: none;
}

a:visited {
    color:inherit;
  text-decoration: none;
}

a:hover {
    color:inherit;
  text-decoration: none;
}

a:active {
    color:inherit;
  text-decoration: none;
}

</style>
<div class="col-md-2 column" id="friends">
                <h2 id="friends_heading">Friends: </h2>     <!--  FRIENDS SECTION HERE  -->
                
                
                <?php 
            
                $location=$activeUsername."_friends";
                $expSql = "SELECT * FROM users WHERE username IN (SELECT person FROM `".$location."` WHERE friend = '1') ORDER BY last_activity DESC;";
                $result3 = mysqli_query($link,$expSql);
                while($rowfr = mysqli_fetch_assoc($result3))
                {
                
                $profilepicfriend=$rowfr['profile'];

                    $timedif=time()-$rowfr['last_activity'];
                    if ($timedif<120)
                        {$color="green"; $status=$rowfr['status'];}
                        else
                        {   $mins=floor($timedif/60);
                            $color="gray"; $status="Has been offline for ".$mins." mins";
                            if ($mins>60)
                            {//$status="Has been offline for some time";
                            $status="Has been offline for ".floor($mins/60)." hours";
                            }
                            }
                            $usernameFriend=$rowfr['username'];
                echo "<div class='friend'><img src='../register//".$profilepicfriend."' class='friendpp'>
                    <small><a href='../profile/index.php?username=$usernameFriend'>".$rowfr['username']."<a/> 
                    
                    <a href='../chat/index.php?username=$usernameFriend'> <span class='glyphicon glyphicon-envelope' aria-hidden='true' style='margin-left:10px;'></span> </a>
                    
                    <br/> </small><font color='red' style='float:right;' class='x' onclick={getname('$usernameFriend')}><span class='glyphicon glyphicon-remove' aria-hidden='true' style='float:right;'></span></font>
                    <div class='activity_bubble' style='background-color:$color'></div><small class='friendstatus'>".$status." </small></div>";
               }
               
               ?>
               
               <!-- <div><img src="assets/img/city_bg.jpg" class="friendpp">
                    <small>Namee of the fucking person <br/> </small>
                    <div class="activity_bubble"></div><small class="friendstatus">status </small></div> -->
            </div>
            </div>
            
