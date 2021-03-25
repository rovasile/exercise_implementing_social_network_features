<?php 
require_once('../config.php');

//echo $_POST['email'].$_POST['password'];
if ($_POST['email'] && $_POST['password'])
{$email=$_POST['email'];
$password=$_POST['password'];
$email = mysqli_real_escape_string($link,$email);
$password = mysqli_real_escape_string($link,$password);
$password=md5($password);
$sql="SELECT verified FROM users WHERE email='$email' AND password='$password'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_assoc($result);
if($count==1)
{
    if($row['verified']==1)
{session_start();
$_SESSION["email"]=$email;
//$date=date("Y-m-d H:i:s");
$date=time();
$sql="UPDATE users SET last_activity = '$date' WHERE email = '$email';";
mysqli_query($link,$sql);
//logged in
header('Location: ../main/index.php');
}
else
{$errormsg.="Please verify your account with the mail provided on your email.";}   
    

}
else
$errormsg.="The credentials do not match. Please try again. ";
}
else
{$errormsg.="Please fill in the email and the password. ";}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
</head>

<body>
    <div class="jumbotron hero">
        <div class="container">
            <div>
                <form id="allform" method="POST" >
                    <h2 class="text-center">Welcome </h2><span class="label label-default">Email: </span>
                    <input class="form-control" type="email" placeholder="Email" name="email"><span class="label label-default">Password: </span>
                    <input class="form-control" type="password" placeholder="Password" name="password">
                    <button class="btn btn-default" type="submit">Log in</button><br>
                    <a href="../register/index.php">or click here to register an account.</a>
                    <?php echo "<br>".$errormsg; ?>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
