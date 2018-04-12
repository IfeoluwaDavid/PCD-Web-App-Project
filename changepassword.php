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
if (isset($_POST['update_pswd'])) 
{
	// receive all input values from the form
	$currentpswd = mysqli_real_escape_string($db, $_POST['currentpswd']);
	$confirmcurrent = mysqli_real_escape_string($db, $_POST['confirmcurrent']);
	$newpswd = mysqli_real_escape_string($db, $_POST['newpswd']);
	$confirmnew = mysqli_real_escape_string($db, $_POST['confirmnew']);
	
	if ($currentpswd != $confirmcurrent)
	{
		array_push($errors, "The current passwords do not match");
	}
	else
	{	
		$ENCcurrentpswd = md5($currentpswd); //encrypt the password before saving in the database
		
		$mysql_query = "SELECT * FROM `user_info` WHERE `user_id` = '".$_SESSION['user_id']."' AND `password` = '$ENCcurrentpswd';";
		$result = mysqli_query($db, $mysql_query);
		
		if (mysqli_num_rows($result) == 1)
		{
			if ($newpswd != $confirmnew)
			{
				array_push($errors, "The new passwords do not match!");
			}
			else
			{
				if(!passwordValidation($newpswd))
				{
					array_push($errors, "Password must range between 8 to 15 characters long");
				}
				else
				{	
					$ENCnewpswd = md5($newpswd);
					$updateQuery = "UPDATE `user_info` SET `password` = '$ENCnewpswd' WHERE `user_id`= '".$_SESSION['user_id']."'";
					mysqli_query($db, $updateQuery);
					$_SESSION['success'] = "Your password has been updated successfully.";
					
					unset($currentpswd);
					unset($confirmcurrent);
					unset($newpswd);
					unset($confirmnew);
				}
			}
		}
		else
		{
			array_push($errors, "Incorrect current password.");
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/changepassword.css">
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
	<li><a href="vieweditprofile.php">Profile Settings</a></li>
	<li><a class="active" href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	
	<div id="rightcolumn">
	<form method="post" action="changepassword.php">
	
		<div class="title">
		<h3>
		<?php echo "Change Password"; ?>
		</h3>
		</div>
		
		<!-- notification message -->
		<?php if (isset($_SESSION['success'])) : ?>
		<div class="error success">
		<h3>
		<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
		</h3>
		</div>
		<?php endif ?>
	
		<?php include('errors.php'); ?>
	
		<div class="input-group">
		<label>Enter Current Password:</label>
		<input type="password" name="currentpswd" value="<?php echo $currentpswd; ?>" required>
		</div>
		
		<div class="input-group">
		<label>Confirm Current Password:</label>
		<input type="password" name="confirmcurrent" value="<?php echo $confirmcurrent; ?>" required>
		</div>
		
		<div class="input-group">
		<label>Enter New Password:</label>
		<input type="password" name="newpswd" value="<?php echo $newpswd; ?>" required>
		</div>
		
		<div class="input-group">
		<label>Confirm New Password:</label>
		<input type="password" name="confirmnew" value="<?php echo $confirmnew; ?>" required>
		</div>
		
		<div class="input-group">
		<button type="submit" class="btn" name="update_pswd">Update Password</button>
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