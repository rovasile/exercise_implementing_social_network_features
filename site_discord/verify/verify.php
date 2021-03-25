<?php
require_once('../config.php');

$id=$_GET['p'];
//echo "id=".$id;
$code=$_GET['code'];
//echo "code=".$code;
$sql = "SELECT * FROM users WHERE code = '$code' AND id = '$id'";
$result = mysqli_query($link,$sql);
$rows = mysqli_fetch_assoc($result);
$count = mysqli_num_rows($result);
if ($count>0)
 {$sql="UPDATE users SET verified = '1' WHERE id='$id'";
	 $result = mysqli_query($link,$sql);
	 
	$username=$rows['username'];
	 $tablename=$rows['username']."_friends";
	 $sql=" CREATE TABLE `" .$tablename."` (id int unsigned not null auto_increment primary key, person varchar(200),request int, friend int, notif int);";
	 $result = mysqli_query($link,$sql);
	 $sql="INSERT INTO `" .$tablename."` (person, request, friend) VALUES ('$username','1','1');";
	 mysqli_query($link,$sql);
	 

	 echo "Verification succesful. \nRedirecting to login...";
	 sleep(1);
	 header('Location: ../login/index.php');
	 }
else
echo "Verification failed.";


?>
