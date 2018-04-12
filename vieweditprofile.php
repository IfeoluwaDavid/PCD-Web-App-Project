<?php

//Connect to Database
include 'init.php';
include 'functions.php';

if (!isset($_SESSION['user_id']))
{
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
}

else
{
	$query = "SELECT * FROM user_info WHERE user_id = ".$_SESSION['user_id']."";
	$results = mysqli_query($db, $query);
	
	if (mysqli_num_rows($results) == 1)
	{
		$row = mysqli_fetch_array($results);
		
		$username = $row[1];
		$firstname = $row[3];
		$lastname = $row[4];
		$email = $row[5];
		
		if($row[7] == true)
		{
			$usertype = "Admin";
		}
		else
		{
			$usertype = "Student";
		}
	}
	else
	{
		//Log the user out. Something must have happened for the userid to be empty.
		session_start();
		session_destroy();
		unset($_SESSION['username']);
		header("location: login.php");
	}
}

// UPDATE USER
if (isset($_POST['logout'])) 
{
	session_start();
	session_destroy();
	unset($_SESSION['username']);
	header("location: login.php");
}

// UPDATE USER
if (isset($_POST['update_user'])) 
{
	// receive all input values from the form
	$username = mysqli_real_escape_string($db, $_POST['username']);
	$firstname = mysqli_real_escape_string($db, $_POST['firstname']);
	$lastname = mysqli_real_escape_string($db, $_POST['lastname']);
	$email = mysqli_real_escape_string($db, $_POST['email']);

	if(empty($username) || empty($email) || empty($firstname) || empty($lastname))
	{
		array_push($errors, "Incomplete form. Some fields are still blank.");
	}
	else
	{
		if(!validateAdminUsername($username))
		{
			array_push($errors, "Student number/username format is invalid");
		}
		
		if(!nameValidation($firstname) || !nameValidation($lastname))
		{
			array_push($errors, "Only letters are allowed in first/last name. Must range between 2 to 10 characters");
		}
		
		// register user if there are no errors in the form
		if (count($errors) == 0) 
		{
			$validationQuery = "SELECT * FROM `user_info` WHERE `email` = '$email' OR `username` = '$username'";
			$result = mysqli_query($db, $validationQuery);
			
			if(mysqli_num_rows($result) > 1)
			{
				array_push($errors, "Existing User Information, Please Change Username or Email");
			}
			else
			{
				$query =  "UPDATE `user_info` SET `username` = '$username', `first_name` = '$firstname', 
				`last_name` = '$lastname', `email` = '$email' WHERE `user_id` = '".$_SESSION['user_id']."'";
				
				mysqli_query($db, $query);
		
				$_SESSION['username'] = $username;
				$_SESSION['success'] = "Your profile has been updated successfully.";
			}
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/vieweditprofile.css">
</head>
<body>
	<div class="header">
	</div>
	
	<div id="container">
	<div id="leftcolumn">	
	<ul>
	<h1>PartsCribber</h1>
	<li><a href="mainoperationsA.php">Item Check-Out</a></li>
	<li><a href="studentaccount.php">Student Account</a></li>
	<li><a href="registeruser.php">Registration</a></li>
	<li><a href="removals.php">Removals</a></li>
	<li><a href="inventory.php">Update Inventory</a></li>
	<li><a href="reports.php">Daily Reports</a></li>
	<li><a class="active" href="vieweditprofile.php">Profile Settings</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	
	<div id="rightcolumn">
	<form method="post" action="vieweditprofile.php">
	
		<div class="title">
		<h3>
		<?php
		echo "View/Edit Profile"; 
		?>
		</h3>
		</div>
		
		<!-- notification message -->
		<?php if (isset($_SESSION['success'])) : ?>
		<div class="error success">
		<h3>
		<?php
		echo $_SESSION['success'];
		unset($_SESSION['success']);
		?>
		</h3>
		</div>
		<?php endif ?>
	
		<?php include('errors.php'); ?>
	
		<div class="input-group">
		<label>Username:</label>
		<input type="text" name="username" value="<?php echo $username; ?>" required>
		</div>
		
		<div class="input-group">
		<label>First Name:</label>
		<input type="text" name="firstname" value="<?php echo $firstname; ?>" required>
		</div>
		
		<div class="input-group">
		<label>Last Name:</label>
		<input type="text" name="lastname" value="<?php echo $lastname; ?>" required>
		</div>
		
		<div class="input-group">
		<label>Email Address:</label>
		<input type="email" name="email" value="<?php echo $email; ?>" required>
		</div>
		
		<div class="input-group">
		<label>User Status:</label>
		<input type="text" name="usertype" value="<?php echo $usertype; ?>" disabled>
		</div>
		
		<div class="input-group">
		<button type="submit" class="btn" name="update_user">Update Profile</button>
		</div>
		
		<div class="input-group">
		<button type="submit" class="btn" name="logout">Log Out</button>
		</div>
		
	</form>
	</div>
	<div style="clear: both;" > </div>
	</div>
	
</body>
</html>