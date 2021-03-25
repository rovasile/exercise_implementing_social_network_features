<?php 
require_once('../config.php');
session_start();
// echo $_SESSION["email"];
 $row;
 $email=$_SESSION["email"];
 
 $sql="SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($link,$sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_assoc($result);
$location=$row['username']."_friends";




//echo $location."  ";
                $expSql = "SELECT * FROM posts WHERE author IN (SELECT person FROM `".$location."` WHERE friend = '1') ORDER BY date DESC;";
                
                $resultPosts = mysqli_query($link,$expSql);
                print_r($resultPosts);
                echo "<br>";
                while($rowPost = mysqli_fetch_assoc($resultPosts))
                {
				echo $rowPost['author']." ".$rowPost['text']." ".$rowPost['person'];
				echo "<br>";
				}

?>
