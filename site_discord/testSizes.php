<?php

require_once('config.php');
session_start();
// echo $_SESSION["email"];
 $row;
 $email=$_SESSION["email"];


$sql="SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_assoc($result);
$activeUsername=$row['username'];

$sql="SELECT id,username FROM users WHERE verified='1';";
$result = mysqli_query($link,$sql);
$usersFromDB=array();
while($row = mysqli_fetch_assoc($result))
{
	$data['id']=$row['id'];
	$data['value']=$row['username'];
	array_push($usersFromDB,$data);
	
echo $row['username'];
echo "<br>";
}
echo json_encode($usersFromDB);

?>
