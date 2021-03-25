<?php 
require_once('../config.php');
session_start();
// echo $_SESSION["email"];
 $row;
 $email=$_SESSION["email"];
if (!isset($_SESSION["email"]))
{header('Location: ../login/index.php');
    //echo $_SESSION["email"];
    }
else
{
$sql="SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_assoc($result);

//$thisUsername=$row['username'];
$thisUsername=mysqli_real_escape_string($link,$_GET['username']);
$activeUsername=$row['username'];
//echo "thisUsername=".$thisUsername;
//echo " | activeUsername=".$activeUsername;
//echo $count;
}
//print_r($row);
$status="";
$errormsgadd="";
$currentUsername=$row["username"];
$location=$row['username']."_friends";
if($_POST["search"])    // ADD FRIEND*
{
$search_name=mysqli_real_escape_string($link,$_POST["search"]);
$sql="SELECT * FROM users WHERE username='$search_name'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);


$sql="SELECT * FROM `" .$location."` WHERE person='$search_name' AND friend='1';";
$result3 = mysqli_query($link,$sql);
$count3 = mysqli_num_rows($result3);

$sql="SELECT * FROM `" .$location."` WHERE person='$search_name' AND request='1';";
$result33 = mysqli_query($link,$sql);
$count33 = mysqli_num_rows($result33);

if($count3==1 || $count33==1)
{
//already friends
$errormsgadd.="You are already friends/Friend request already sent. ";

}
else
if($count==1)
{   //person exists among users
    //echo "PERSON EXISTS";
    $sql="SELECT * FROM `" .$location."` WHERE person='$search_name';";
    $resultDUP=mysqli_query($link,$sql);
    $countDUP=mysqli_num_rows($resultDUP);
    //echo "DUP=".$countDUP;
    $locationMutual=$search_name."_friends";
    $countMutual=0;
    if($countDUP==1)
    {//$sql="UPDATE `" .$location."` SET friend = '1' WHERE person='$search_name';"; 
      //  mysqli_query($link,$sql);
        $countMutual=1;}
    else
    {$sql="INSERT INTO `" .$location."` (person, request,friend) VALUES ('$search_name','1',0);";


    $result=mysqli_query($link,$sql);
    $status.="A friend request has been sent. ";

    }
    
    if ($countMutual==1)
    {  // echo "MUTUAL FRIENDS";
        //become friends
        $status.="You are now friends.";
        
        $sql=" UPDATE `" .$location."` SET friend = '1' WHERE person = '$search_name';";
        $resultAdd1 = mysqli_query($link,$sql);
        $sql=" UPDATE `" .$location."` SET notif = '0' WHERE person = '$search_name';";
        $resultAdd1 = mysqli_query($link,$sql);
        
        $sql=" UPDATE `" .$locationMutual."` SET friend = '1' WHERE person = '$currentUsername';";
        $resultAdd2 = mysqli_query($link,$sql);        
        
    }
    else
    {
    //echo "A AJUNS AICI; <br>";
    $sqlFr="INSERT INTO `" .$locationMutual."` (person, notif) VALUES ('$currentUsername','1')";
    //echo $sqlFr;
    mysqli_query($link,$sqlFr);  
    }
  
}
else
$errormsgadd="Person not found. Please try again. ";

}   //                                                  ADD FRIEND

                                    //ADD POST*
//print_r($_FILES);

$postType=0;    //NO POST
if ($_FILES['file']['name'])
 $postType=1;   //IMG ONLY
 if ($_POST['myPostText'])
 $postType=2;   //TEXT ONLY
 if ($_FILES['file']['name'] && $_POST['myPostText']) 
 $postType=3;   //FULL POST
 
 //echo "->".$postType;
 switch ($postType)
 {
    case 0: break;
    case 1: postImg(); break;
    case 2: postText(); break;
    case 3: postTextImg(); break;
 }

function postImg(){
    global $row;
    global $_POST;
    global $link;
    global $_FILES;
  //  echo $_FILES['file']['name'];
    
  $file_name= $_FILES['file']['name'];
  $file_type= $_FILES['file']['type'];
  $file_size = $_FILES['file']['size'];
 // echo "file size = ".$file_size;
  if ($file_size>1000000)
    $errormsg.="Please use an image that's smaller than 1MB. ";
  $file_tem_loc = $_FILES['file']['tmp_name'];
  $file_name= preg_replace('/[^A-Za-z0-9-.]/', '', $file_name);
  $file_store=$file_name;

  move_uploaded_file($file_tem_loc, $file_store);
  chmod($file_store, 0777);
  $imgLocation=$file_store;
  
    $temp=$row['username'];
  $date=time();
  $sql="INSERT INTO posts (author, img, date) VALUES ('$temp','$file_store','$date');";
  mysqli_query($link,$sql);
    
    
    }
    
function postText(){
    global $row;
    global $_POST;
    global $link;
    //echo $row['username'];
    //echo $_POST['myPostText'];
    $text=mysqli_real_escape_string($link,$_POST['myPostText']);
      $date=time();
      $temp=$row["username"];
    $sql="INSERT INTO posts (author, text, date) VALUES ('$temp','$text','$date');";
    //echo $sql;
    mysqli_query($link,$sql);
    //echo mysqli_error($link);
    
    
    
    }
    
function postTextImg(){
        global $row;
    global $_POST;
    global $link;
    global $_FILES;
    
    
  $file_name= $_FILES['file']['name'];
  $file_type= $_FILES['file']['type'];
  $file_size = $_FILES['file']['size'];
  //echo "file size = ".$file_size;
  if ($file_size>1000000)
    $errormsg.="Please use an image that's smaller than 1MB. ";
  $file_tem_loc = $_FILES['file']['tmp_name'];
  $file_name= preg_replace('/[^A-Za-z0-9-.]/', '', $file_name);
  $file_store="../main/".$file_name;
  


  move_uploaded_file($file_tem_loc, $file_store);
  chmod($file_store, 0777);
  $imgLocation=$file_store;
    $text=mysqli_real_escape_string($link,$_POST['myPostText']);
    
    $temp=$row['username'];
      $date=time();
  $sql="INSERT INTO posts (author, img, date,text) VALUES ('$temp','$file_name','$date','$text');";
  mysqli_query($link,$sql);
    
    
    
    
    }   //ADD POST

//                                              check friend requests
$sql="SELECT * FROM users WHERE username IN (SELECT person FROM `" .$location."` WHERE notif='1' ORDER BY id DESC);";
$friendList=mysqli_query($link,$sql);



// EDIT PROFILE

if($_POST['newStatus'])
{
$newStatus=mysqli_real_escape_string($link,$_POST['newStatus']);
$sql = "UPDATE users SET status='$newStatus' WHERE username='$activeUsername';";
mysqli_query($link,$sql);

}

if($_POST['newPassword'])
{
$newPassword=md5(mysqli_real_escape_string($link,$_POST['newPassword']));
$sql = "UPDATE users SET password='$newPassword' WHERE username='$activeUsername';";
mysqli_query($link,$sql);

}

if($_FILES['newProfile']['name'])
{echo "Attempting to change the profile";
  $file_name= $_FILES['newProfile']['name'];
  $file_type= $_FILES['newProfile']['type'];
  $file_size = $_FILES['newProfile']['size'];
  //echo "file size = ".$file_size;
  if ($file_size>1000000)
    $errormsg.="Please use an image that's smaller than 1MB. ";
  $file_tem_loc = $_FILES['newProfile']['tmp_name'];
  $file_name= preg_replace('/[^A-Za-z0-9-.]/', '', $file_name);
  $file_store="../register/".$file_name;
  echo "fs=".$file_store;
  


  move_uploaded_file($file_tem_loc, $file_store);
  chmod($file_store, 0777);
  $imgLocation=$file_store;
    
  $sql="UPDATE users SET profile='$file_name' WHERE username='$activeUsername';";
  mysqli_query($link,$sql);

}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>main</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
    <style>
    #mypp{
    margin-top:5px;
        border-style: solid;
    border-width:2px;
    border-color:white;    
    box-shadow: 1px 1px 5px black;
    }
    

    
    div#identity.col-md-3{

    //box-sizing:border-box;
    //overflow:hidden;
    //display:none;
    
    }
    

    

    
    
    #myPostText{
    border-style:solid;
    border-color:black;
    border-radius:13px;
    }
    #myPostButton{
    float:right;   
     border-radius:13px;
    }
    body{
    margin-top:0px;
    padding-top:0px;
    color:black;
    background-color:#fcfcfc;
    }
        .jumbotron{
        margin-top:10px;
        padding-top:0px;
    color:black;
    background-color:#fcfcfc;
    }
    
    div#newsfeed.col-md-7.column{
        background-color:#f0f0f0;
        box-shadow: 0px 0px 60px 70px #f0f0f0;
    }

    
    #friendReqContainer{
     margin-left:20px;   
    }
    
    #friendReq{
    //min-height:200px;
    //width:100%;
    //background-color:red;


    }
    
    .request{
    display:inline-block;
    border-style:solid;
    border-radius:10px;
    padding:5px;
    background-color:#e4d8e0;
    transition: 0.5s;
    margin-bottom:5px;

    }
    .request:hover{

    background-color:white;
    animation-name: pulse;
    animation-duration:1s;
    animation-iteration-count: 2;
    }
    @keyframes pulse{
    0% {box-shadow:none;}
    50% {box-shadow:0px 0px 20px 3px yellow;}
    //75% {box-shadow:0px 0px 20px -3px blue;}
    100%{box-shadow: none;};
    }
    
    </style>
    
</head>

<body>
    
    <div class="jumbotron hero">
        <div class="row">
            
            <div class="col-md-3 column" id="identity">
                <a href='../main/index.php' style='margin-left:20px; background-color:black; border-radius:20px; color:white; padding:2px;'>Back to the news feed</a></br>
                <img src="../register/<?php  
                $getProfile="SELECT profile FROM users WHERE username='$thisUsername'";
                $resultProfile= mysqli_query($link, $getProfile);
                $rowProfile=mysqli_fetch_assoc($resultProfile);
                $profilepic=$rowProfile['profile'];
                echo $profilepic;
                ?>" id="mypp">
                
                <h2><?php echo $thisUsername; ?></h2><br>
                
                <?php 
                if($thisUsername!=$activeUsername)
                echo "                
                <form method='POST' enctype='multipart/form-data'>
                    <input class='form-control' type='search' name='search' value='$thisUsername' readonly>
                    <button class='btn btn-default' type='submit'>Add </button>
                </form>";
                else
                echo "<div id='changeThings' style='margin-right:150px; text-align:right'>
                    <form method='POST' enctype='multipart/form-data'>
                    <label>New status</label>
                    <input class='form-control' name='newStatus' style='width:50%'></input>
                    <br>
                    <label>New password</label>
                    <input class='form-control' name='newPassword' style='width:50%'></input>
                    <br>
                    <label>New profile picture &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <input  name='newProfile' type='file' style='width:50%; margin-left:30%'></input>
                    <br>
                   <button class='btn btn-default' type='submit' style='width:30%'>Change </button>
                    </form>
                </div>"
                
                ?>

                

                
                <font color="black">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $errormsgadd; echo $status; ?></font>
    <br>
    <div id='friendReqContainer'>
    <div id='friendReq'>
            <?php   // FRIEND REQUESTS
            echo "<small >Friend requests:</small><br>";
            while($friendUnit=mysqli_fetch_assoc($friendList))
            {$usernameFriendAdd=$friendUnit['username'];
                echo "<small class='notsmall x request' onclick={addFriend('$usernameFriendAdd')} >".$friendUnit['username']."</small>";
            }

            ?>
            </div>
            </div>
            </div>
           
            <div class="col-md-7 column" id="newsfeed">
                <h1 class="text-center" id="feed"><?php echo "$thisUsername 's profile";?> </h1>

                <div class="friendpost"   <?php  if($thisUsername!=$activeUsername)    echo  "style='display:none;'";  ?>>      <!---      POST     -->
                    <h2 class="text-center" id="feed">Create a post </h2>
                    <form method="POST" enctype="multipart/form-data">
                    <textarea class="form-control" name="myPostText" id="myPostText"></textarea>
                    <input type="file" name="file" style="float:left;">
                    <button class="btn btn-default" type="submit" id="myPostButton">Post </button>
                    </form>
                </div>     
                <div id="refreshFeed">
                
                <?php 
                //$sql="SELECT * FROM posts ORDER BY date DESC;";
                //$resultPosts = mysqli_query($link,$sql);
                //echo $location;
                $expSql = "SELECT * FROM posts WHERE author='$thisUsername' ORDER BY id DESC;";
                $resultPosts = mysqli_query($link,$expSql);
                echo mysqli_error($link);
                //while($rowfr = mysqli_fetch_assoc($result3))
                
                
                while($rowPost = mysqli_fetch_assoc($resultPosts))
                {//echo $rowPost['author'];
                $authorPost=$rowPost['author'];
                $sql="SELECT profile FROM users WHERE username='$authorPost';";
                $getAvatar = mysqli_query($link,$sql);
                $imgFetch = mysqli_fetch_assoc($getAvatar);
                $imgAvatar= $imgFetch['profile'];
                
                
                if ($rowPost['img'])
                    {$textPost=$rowPost['text'];
                        $imgPost=$rowPost['img'];
                        //$authorPost=$rowPost['author'];
                        $timePost=floor((time()-$rowPost['date'])/60);
                               
                                           
                        if($timePost>60)
                        {$timePost=floor($timePost/60);
                        $timePost=" posted ".$timePost." hours ago";    
                        }
                        else
                        $timePost=" posted ".$timePost." minutes ago";
                        

                       echo "<div class='friendpost'>
                    <div class='friendpost_info'><small class='notsmall'>$authorPost</small><i class='glyphicon glyphicon-time'></i><small class='notsmall'>$timePost</small><img src='../register/$imgAvatar' class='friendpp'></div>
                    <div class='friendpost_actualpost'>
                        <p class='post_text'>$textPost</p>
                    </div><img src='../main/$imgPost' class='post_image'></div>";
                    
                    }
                else
                {$textPost=$rowPost['text'];
                        $authorPost=$rowPost['author'];
                        $timePost=floor((time()-$rowPost['date'])/60);
                        if($timePost>60)
                        {$timePost=floor($timePost/60);
                        $timePost=" posted ".$timePost." hours ago";    
                        }
                        else
                        $timePost=" posted ".$timePost." minutes ago";
                        
                    
                       echo "<div class='friendpost'>
                    <div class='friendpost_info'><small class='notsmall'>$authorPost</small><i class='glyphicon glyphicon-time'></i><small class='notsmall'>$timePost</small><img src='../register/$imgAvatar' class='friendpp'></div>
                    <div class='friendpost_actualpost'>
                        <p class='post_text'>$textPost</p>
                    </div></div>";                    
                }
                
                }
                ?>
                <!--
                <div class="friendpost">
                    <div class="friendpost_info"><small class="notsmall">The name of the person</small><i class="glyphicon glyphicon-time"></i><small class="notsmall">date and time</small><img src="assets/img/screen-content-iphone-6.jpg" class="friendpp"></div>
                    <div class="friendpost_actualpost">
                        <p class="post_text">Lets go bois and feed and get sum money andasdasdasdasdasdasdasdasdsadasdasdasdasdasdadasdasdasdasdasdsadasdasdasdasasdasdasdasdasdasdasdaph</p>
                    </div>
                </div>
                
                <div class="friendpost">
                    <div class="friendpost_info"><small class="notsmall">The name of the person</small><i class="glyphicon glyphicon-time"></i><small class="notsmall">date and time</small><img src="assets/img/screen-content-iphone-6.jpg" class="friendpp"></div>
                    <div class="friendpost_actualpost">
                        <p class="post_text">Lets go bois and feed and get sum money andasdasdasdasdasdasdasdasdsadasdasdasdasdasdadasdasdasdasdasdsadasdasdasdasasdasdasdasdasdasdasdaph</p>
                    </div><img src="assets/img/city_bg.jpg" class="post_image"></div>
                    -->
                    </div>
                
                    
            </div>  
            <div id="friendsSection"></div>         <!--FRIENDS SIDE BAR -->
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script>
        stillonline();
    setInterval(stillonline, 60000);
    
    
    function stillonline() {
        
            //alert("da");
              var xmlhttp = new XMLHttpRequest();
              xmlhttp.onreadystatechange = function() {
                  if (this.readyState == 4 && this.status == 200) {
                  //    this.responseText;
                    console.log("test");

                  }
              };
              xmlhttp.open("GET", "stillonline.php", true);
              xmlhttp.send();
              //setTimeout(stillonline(), 3000);
    };
    
    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

    var $friendsBar = $("#friendsSection");
setInterval(function () {
    $friendsBar.load("../friendsSection.php #friendsContainer");
}, 10000);

    var $scores2 = $("#refreshFeed");
setInterval(function () {
    //alert("index.php?=<?php echo $thisUsername; ?> #refreshFeed");
    $scores2.load("index.php?username=<?php echo $thisUsername; ?> #refreshFeed");
}, 10000);

    var $friendReq = $("#friendReq");
setInterval(function () {
    $friendReq.load("index.php #friendReq");
}, 5000);

function getname(x)
{
  var name=x;
    //alert("deletefile.php?name="+name);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var check=this.responseText;
	//alert(check);
	location.reload();
        }
    };
    xmlhttp.open("GET", "deletefriend.php?name="+name, true	);
    xmlhttp.send();
};

function addFriend(x)
{
  var name=x;
    //alert("deletefile.php?name="+name);
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var check=this.responseText;
	//alert(check);
	location.reload();
        }
    };
    xmlhttp.open("GET", "addfriend.php?name="+name, true	);
    xmlhttp.send();
};

$(function(){
      $("#friendsSection").load("../friendsSection.php"); 
    });

    </script>
</body>

</html>
