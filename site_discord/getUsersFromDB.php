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


$searchWord=mysqli_real_escape_string($link,$_GET['search']);
$searchWord=strtolower($searchWord);
//echo "SE=".$searchWord."<br>";
$sql="SELECT id,username,profile FROM users WHERE verified='1' AND LOWER(username) LIKE '%$searchWord%';";
$result = mysqli_query($link,$sql);
$usersFromDB=array();
while($row = mysqli_fetch_assoc($result))
{
	$data['id']=$row['id'];
	$data['value']=$row['username'];
	$data['profile']=$row['profile'];
	array_push($usersFromDB,$data);
	
//echo $row['username'];
//echo "<br>";
}
//echo $searchWord;
echo json_encode($usersFromDB);

?>
