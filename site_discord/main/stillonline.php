
<?php 
require_once('../config.php');
session_start();
// echo $_SESSION["email"];
 $row;
 $email=$_SESSION["email"];

//echo $count;
$date=time();
$sql="UPDATE users SET last_activity = '$date' WHERE email = '$email';";
mysqli_query($link,$sql);



?>
