<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

//ekhaen connection create korsi
$conn = new mysqli($servername, $username, $password);

//checking connection 
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
} else {
    mysqli_select_db($conn, $dbname);
	//echo "Connection successful";

}

?>