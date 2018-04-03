<?php

session_start();
session_destroy();
unset($_SESSION['username']);
unset($_SESSION['user_id']);
header("location: login.php");
	
?>