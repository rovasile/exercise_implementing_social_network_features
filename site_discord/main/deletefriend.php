<?php 
require_once('../config.php');
session_start();
// echo $_SESSION["email"];
$friendName = $_REQUEST['name'];
 $row;
 $email=$_SESSION["email"];

$sql="SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_assoc($result);
$currentName=$row['username'];
$location=$row['username']."_friends";
$locationFriend=$friendName."_friends";

$sql="DELETE FROM `".$location."` WHERE person='$friendName'";
mysqli_query($link,$sql);

$sql="DELETE FROM `".$locationFriend."` WHERE person='$currentName'";
mysqli_query($link,$sql);



?>
