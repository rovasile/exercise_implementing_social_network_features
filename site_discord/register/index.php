<?php 
require_once('../config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '/usr/share/php/libphp-phpmailer/src/SMTP.php';
require '/usr/share/php/libphp-phpmailer/src/PHPMailer.php';
require '/usr/share/php/libphp-phpmailer/src/OAuth.php';
require '/usr/share/php/libphp-phpmailer/src/Exception.php';
require '/usr/share/php/libphp-phpmailer/src/POP3.php';
$errormsg="";
$file_store="";
$file_size=0;
$profile="";
if ($_FILES['file']['name']) {
    
  $file_name= $_FILES['file']['name'];
  $file_type= $_FILES['file']['type'];
  $file_size = $_FILES['file']['size'];
  //echo "file size = ".$file_size;
  if ($file_size>1000000)
    $errormsg.="Please use an image that's smaller than 1MB. ";
  $file_tem_loc = $_FILES['file']['tmp_name'];
  $file_name= preg_replace('/[^A-Za-z0-9-.]/', '', $file_name);
  $file_store=$file_name;

  move_uploaded_file($file_tem_loc, $file_store);
  chmod($file_store, 0777);
  $profile=$file_store;
  //echo "profile location = ".$profile;
}



if ($_POST['username'] && $_POST['password'] && $_POST['email'])
$fill=1;
else
$errormsg.="Please fill in the username, password and email. ";

$username=$_POST['username'];
$password=$_POST['password'];
$username = mysqli_real_escape_string($link,$username);

$password = mysqli_real_escape_string($link,$password);
$password = md5($password);
$email=$_POST['email'];
$email=mysqli_real_escape_string($link,$email);
$status=$_POST['status'];
$status=mysqli_real_escape_string($link,$status);
$profile=mysqli_real_escape_string($link,$profile);
if ($profile=="")
$profile="basic.jpg";

$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
//echo "count=".$count;
if ($count>0)
$errormsg.="Email already in use. ";


$sql = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($link,$sql);
$count2 = mysqli_num_rows($result);
if ($count2>0)
$errormsg.="Username already in use. Please choose another one. ";


$code= rand(0,99999);
if ($fill==1 && $count==0 && $count2==0 && $file_size<1000000)
{   //echo "code=".$code;
    $sql="INSERT INTO users (username, password, email, status, profile,code) VALUES ('$username','$password','$email','$status','$profile',$code);";
    $result = mysqli_query($link,$sql);
    //echo "result insert = ".$result;
    $sql="SELECT id FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    $pos=$row["id"];
    //echo "pos=".$pos;


$subject="Please verify your account!";

$message="";

$message.="Validate your account by accessing the following link: http://109.103.170.217/site_discord/verify/verify.php?code=$code&p=$pos .";






$mail = new PHPMailer(TRUE);

$mail->setFrom($email);
$mail->addAddress($email);
$mail->Subject = $subject;
$mail->Body = $message;
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->Host = 'ssl://smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Port = 465;

//Set your existing gmail address as user name
$mail->Username = 'botfortesting2020@gmail.com';

//Set the password of your gmail address here
$mail->Password = 'Botfortesting2020*';

if(!$mail->send()) {
  //echo 'Email is not sent.';
  //echo 'Email error: ' . $mail->ErrorInfo;
  $errormsg.="Could not send the verification email. Please report this issue to the website administrator.";
  $emailerror=$mail->ErrorInfo;
} else {
  $errormsg="The verification email has been sent. Please check your inbox. The email may end up in Spam, so please check there too.";
}

}
    




?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
    <style>
    input{
    margin-top:10px;
    margin-bottom:20px;
    }
    
    </style>
</head>

<body>
    <div class="jumbotron hero">
        <div class="container">
            <div>
                <form id="allform" method="POST" action="index.php" enctype="multipart/form-data"><span class="label label-default">Username: </span>
                    <input class="form-control" type="text" placeholder="Username" name="username"><span class="label label-default">Password: </span>
                    <input class="form-control" type="password" placeholder="Password" name="password"><span class="label label-default">Email: </span>
                    <input class="form-control" type="email" placeholder="Email" name="email">
                    <span class="label label-default">How are you feeling? </span>
                    <input class="form-control" type="text" placeholder="Status" name="status">
                    
                    <span class="label label-default" >Profile picture (1MB max)</span>
                    <input type="file" name="file">
                    <button class="btn btn-default" type="submit" name="check">Register </button>
                    <?php echo "<br>".$errormsg; ?>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
