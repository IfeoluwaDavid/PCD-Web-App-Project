<?php

	session_start();
	error_reporting(0);
 
	$db_name = "ifedavid_partscrib";
	$mysql_user = "ifedavid";
	$mysql_pass = "Rapperholikah1997";
	$server_name = "gator4235.hostgator.com";

	//Connect to database
	$db = mysqli_connect($server_name, $mysql_user, $mysql_pass, $db_name);
	
	// Check connection
	if ($db->connect_error)
	{
	    	die("Connection failed: " . $db->connect_error);
	}
		
	$errors = array();
	//$conn->close();
	
?>