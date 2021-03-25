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
//echo $count;
$thisUsername=mysqli_real_escape_string($link,$_GET['username']);
$activeUsername=$row['username'];

$sqlVerif="SELECT * FROM users WHERE username='$thisUsername';";
$queryVerif=mysqli_query($link,$sqlVerif);
$count = mysqli_num_rows($queryVerif);
if ($count==0)
header('Location: ../main/index.php');
}
//print_r($row);
$status="";
$errormsgadd="";
$currentUsername=$row["username"];
$location=$row['username']."_friends";
                                        
                                        
                                                    // GETTING MESSAGES

$tableName="";
if (strcmp($activeUsername,$thisUsername)<0)
$tableName=$activeUsername.$thisUsername;
else
$tableName=$thisUsername.$activeUsername;


$sqlTableCheck="CREATE TABLE IF NOT EXISTS `$tableName` (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, messageContent text, messageSender varchar(100),messageTime int);";
mysqli_query($link,$sqlTableCheck);



                                                //TRYING TO SEND MESSAGES




                                    //ADD POST*
//print_r($_FILES);

$postType=0;    //NO POST
if ($_FILES['myMessageFile']['name'])
 $postType=1;   //IMG ONLY
 if ($_POST['myMessageText'])
 $postType=2;   //TEXT ONLY
 if ($_FILES['myMessageFile']['name'] && $_POST['myMessageText']) 
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
    global $tableName;
    global $activeUsername;
    
  //  echo $_FILES['file']['name'];
    
  $file_name= $_FILES['myMessageFile']['name'];
  $file_type= $_FILES['myMessageFile']['type'];
  $file_size = $_FILES['myMessageFile']['size'];
 // echo "file size = ".$file_size;
  if ($file_size>1000000)
    $errormsg.="Please use an image that's smaller than 1MB. ";
  $file_tem_loc = $_FILES['myMessageFile']['tmp_name'];
  $file_name= preg_replace('/[^A-Za-z0-9-.]/', '', $file_name);
  $file_store=$file_name;

  move_uploaded_file($file_tem_loc, $file_store);
  chmod($file_store, 0777);
  $imgLocation=$file_store;
  
    //$temp=$row['username'];
  $date=time();
  $sql="INSERT INTO `$tableName` (messageSender, messageImage, messageTime) VALUES ('$activeUsername','$file_store','$date');";
  mysqli_query($link,$sql);
    
    
    }
    
function postText(){
    global $row;
    global $_POST;
    global $link;
    global $tableName;
    global $activeUsername;
    
    //echo $row['username'];
    //echo $_POST['myPostText'];
    $text=mysqli_real_escape_string($link,$_POST['myMessageText']);
      $date=time();
      //$temp=$row["username"];
  $sql="INSERT INTO `$tableName` (messageSender, messageContent, messageTime) VALUES ('$activeUsername','$text','$date');";
   // echo $sql;
    mysqli_query($link,$sql);
    //echo mysqli_error($link);
    
    
    
    }
    
function postTextImg(){
        global $row;
    global $_POST;
    global $link;
    global $_FILES;
    global $tableName;
    global $activeUsername;
    
  $file_name= $_FILES['myMessageFile']['name'];
  $file_type= $_FILES['myMessageFile']['type'];
  $file_size = $_FILES['myMessageFile']['size'];
  //echo "file size = ".$file_size;
  if ($file_size>1000000)
    $errormsg.="Please use an image that's smaller than 1MB. ";
  $file_tem_loc = $_FILES['myMessageFile']['tmp_name'];
  $file_name= preg_replace('/[^A-Za-z0-9-.]/', '', $file_name);
  $file_store=$file_name;
  


  move_uploaded_file($file_tem_loc, $file_store);
  chmod($file_store, 0777);
  $imgLocation=$file_store;
    $text=mysqli_real_escape_string($link,$_POST['myMessageText']);
    
    //$temp=$row['username'];
      $date=time();
  $sql="INSERT INTO `$tableName` (messageSender, messageImage, messageContent, messageTime) VALUES ('$activeUsername','$file_store','$text','$date');";
  mysqli_query($link,$sql);
    
    
    
    
    }   //ADD POST
















if($_POST["search"])                                    // ADD FRIEND**
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

//                                              check friend requests
$sql="SELECT * FROM users WHERE username IN (SELECT person FROM `" .$location."` WHERE notif='1' ORDER BY id DESC);";
$friendList=mysqli_query($link,$sql);


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>main</title>

    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
<link rel="shortcut icon" href="#" />



    <style>
    #mypp{
    margin-top:5px;
        border-style: solid;
    border-width:2px;
    border-color:white;    
    box-shadow: 1px 1px 5px black;
    float:left;
    margin-left:40px;
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
    margin-bottom:0px;
    padding-bottom:0px;
    }
        .jumbotron{
        margin-top:10px;
        padding-top:0px;
    color:black;
    background-color:#fcfcfc;
        margin-bottom:0px;
    padding-bottom:0px;
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

#searchResults{
//border-style:solid;
border-color:black;
border-width:2px;
border-radius:5px;
margin-left:45px;
margin-top:0;
padding-top:0;
//box-shadow: 0px 0px 10px gray;
width:60%;
text-align:center;
}

.searchResultLine
{   //background-color:yellow;
    margin-bottom:5px;
    margin-top:3px;
    padding:2px;
    border-style:solid;
    box-shadow: 0px 0px 10px -2px gray;
border-color:black;
border-width:2px;
border-radius:10px;
display:inline-block;
transition:0.3s;
font-size:20px;
}

.searchResultLine:hover
{
background-color:black;
color:white;
transition:0.3s;
}

.searchFriendsPp
{
width:30px;
height:30px;
margin-left:0px;
}


#chat{
    height:99vh;
    //background-color:green;
    //border-style:solid;
    //border-radius:20px;
    //border-color:purple;
    //padding-left:10px;
    //padding-right:10px;
    //margin-left:25px;
    //box-shadow: 0px 0px 20px -5px black;
}
.message{
    //border-style:solid;
    border-radius:10px;
    border-color:black;
    border-width:2px;
    margin-top:10px;
    padding:5px;
    background-color:white;
    box-shadow:3px 3px 15px -5px black, -3px -3px 15px -5px gray;
    //height:20vh;
}

.messageSender{
    //background-color:yellow;
}

.messageContent{
    
}
    
#messageText{
background-color:green;
border-radius:20px;
}
#sendForm{
    height:13vh;
    margin-left:25px;
    border-style:solid;
    border-radius:10px;
    border-color:black;
    border-width:2px;
    margin-top:10px;
    padding:5px;
}
    
    </style>
</head>

<body>
    <div class="jumbotron hero">
        <div class="row">
            <div class="col-md-3 column" id="identity">
                <a href='../main/index.php' style='margin-left:20px; background-color:black; border-radius:20px; color:white; padding:2px;'>Back to the news feed</a></br>
                <img src="../register/<?php  
                $profilepic=$row['profile'];
                echo $profilepic;
                ?>" id="mypp">
                
                <h2><?php echo $row['username']; ?></h2><small style="float:right; margin-right:30px;"><?php echo $row['status'];?></small><br>
                <form method="POST" enctype="multipart/form-data">      <!--  SEARCH FRIENDS    -->
                    <input placeholder="Search here" class="form-control"  name="search" id="searchFriends" autocomplete="off">
                    <button class="btn btn-default" type="submit" >Add </button>
                    <div id="searchResults">
                    <!-- <a href="" class="searchResultLine">asdasdasdas</a> -->
                    </div>
                </form>
                <font color="black">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $errormsgadd; echo $status; ?></font>
    
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
                <!-- <h1 class="text-center" id="feed">News </h1> -->    <!--       CHAT STARTS HERE        -->
                <div >
                    
                    <h1>Your conversation with <?php echo $thisUsername;?></h1>
                    <form method="POST" enctype="multipart/form-data" id="sendForm">
                        <textarea class="form-control" name="myMessageText" id="myPostText"></textarea>
                        <input type="file" name="myMessageFile" style="float:left;">
                        <button class="btn btn-default" type="submit" id="myPostButton">Send </button>
                    </form>
                    <div style='margin-left:25px;'>
                        <div id='chat'>
                        <?php 
                        $sqlMessages="SELECT * FROM `$tableName` ORDER BY id DESC;";
                        $queryMessages=mysqli_query($link,$sqlMessages);


                        while($row=mysqli_fetch_assoc($queryMessages))
                        {
                        
                        if($row['messageSender']==$activeUsername)
                            $col="0e6e49";
                        else
                            $col="590e6e";
                        
                        $numlines=floor(strlen($row["messageContent"])/150) +1;
                        $time=floor((time()-$row["messageTime"])/60);
                        if($time<60)
                            $time="$time minutes ago";
                        else
                            $time=floor($time/60)." hours ago";
                        $msgImg=$row['messageImage'];
                        echo "
                            <div class='message'>
                                <div class='messageSender'>
                                        <label class='control-label' for='text-input'> &nbsp; <font color='#".$col."'>".$row["messageSender"].":</font> </label>
                                        <label class='control-label' style='float:right;'>".$time."</label>
                                </div><div class='messageContent'>";
                                if($row['messageContent'])
                                echo "
                                
                                      <textarea class='form-control messageText' rows='".$numlines."' cols='60' disabled>".htmlspecialchars($row["messageContent"])."</textarea>";
                              if($row['messageImage'])
                              echo "<img src='$msgImg' class='post_image'>";
                              
                              echo "  
                                </div>
                            </div>";
                        
                        }
                        ?>
                        </div>
                    </div>
                    <!--                    
                    <div id='chat'>
                        <div class='message'>
                            <div class='messageSender'>
                                    <label class='control-label' for='text-input'> &nbsp; <font color='#".$col."'>".$row["sender"].":</font> </label>
                                    <label class='control-label' style='float:right;'>".$row["time"]."</label>
                            </div>
                            <div class='messageContent'>
                                  <textarea class='form-control messageText' rows='".$numlines."' cols='60' disabled>".htmlspecialchars($row["content"])."</textarea>
                            </div>
                        </div>
                    </div>
                    -->
                </div>
                
                    
            </div>  
            <div id="friendsSection"></div>    <!-- friends bar here -->
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

/*    var $scores2 = $("#refreshFeed");
setInterval(function () {
    $scores2.load("index.php #refreshFeed");
}, 10000);*/



    var $friendReq = $("#friendReq");
setInterval(function () {
    $friendReq.load("index.php?username=<?php echo $thisUsername; ?> #friendReq");
}, 5000);

    var $chat = $("#chat");
setInterval(function () {
    $chat.load("index.php?username=<?php echo $thisUsername; ?> #chat");
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
    


var searchFriends = $("#searchFriends");
  searchFriends.keyup(function(){
      var searchBar = document.getElementById("searchFriends");
      var name=searchBar.value;
      if(name=="")
      {document.getElementById("searchResults").innerHTML="";
      //searchFriends.hide();
      }
      else
       { var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var theList=JSON.parse(this.responseText);
        renderHTML(theList);
        //console.log(theList);
        }};
    xmlhttp.open("GET", "../getUsersFromDB.php?search="+name, true);
    xmlhttp.send();
   // searchFriends.css("background-color", "yellow");
        }
});


var searchResults=document.getElementById("searchResults");
function renderHTML(data)
{searchResults.innerHTML="";
        var i;
    var htmlString="";
    for (i=0; i<data.length && i<5; i++)
    {
        htmlString+= "<a href='../profile/index.php?username="+data[i].value+"' class='searchResultLine'><img class='searchFriendsPp' src='../register/"+data[i].profile+"'></img>"+ data[i].value+"</a></br>";
        
    }
    searchResults.insertAdjacentHTML('beforeend',htmlString);
    //console.log(data);
}

    </script>
</body>

</html>
