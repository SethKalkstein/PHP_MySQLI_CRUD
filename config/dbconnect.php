<?php 
	//connect to the database
	$conn = mysqli_connect("localhost", "MrSeth", "DataYay", "php_tester");

	//test to see if connection was successful
	if($conn){
//		echo "connection successful... ";
	}
	else {
		echo "connection problems as follows: " . mysqli_connect_error();
	}
 ?>