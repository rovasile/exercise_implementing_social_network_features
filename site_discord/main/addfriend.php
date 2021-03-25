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

$sql=" UPDATE `" .$location."` SET friend = '1' WHERE person = '$friendName';";
$resultAdd1 = mysqli_query($link,$sql);
$sql=" UPDATE `" .$location."` SET notif = '0' WHERE person = '$friendName';";
$resultAdd1 = mysqli_query($link,$sql);

$sql=" UPDATE `" .$locationFriend."` SET friend = '1' WHERE person = '$currentName';";
$resultAdd2 = mysqli_query($link,$sql);

?>
