<?php

error_reporting(0);
//Connect to Database
//include 'init.php';

if (!isset($_SESSION['username']))
{
	
}

// UPDATE USER
if (isset($_POST['update_user'])) 
{
	
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="maininterface.css">
</head>
<body>
	<div class="header">
	</div>

	<div id="container">
	<div id="leftcolumn">	
	<ul>
	<h1>PartsCribber</h1>
	<li><a class="active" href="#home">Main Menu</a></li>
	<li><a href="#news">Profile Settings</a></li>
	<li><a href="#contact">Change Password</a></li>
	<li><a href="#about">Student Cart</a></li>
	<li><a href="#about">Student Possession</a></li>
	<li><a href="#about">Update Inventory</a></li>
	<li><a href="#about">Log Out</a></li>
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
		<input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" required>
		</div>
		
		<div class="input-group">
		<label>First Name:</label>
		<input type="text" name="firstname" value="<?php echo $_SESSION['firstname']; ?>" required>
		</div>
		
		<div class="input-group">
		<label>Last Name:</label>
		<input type="text" name="lastname" value="<?php echo $_SESSION['lastname']; ?>" required>
		</div>
		
		<div class="input-group">
		<label>Email Address:</label>
		<input type="email" name="email" value="<?php echo $_SESSION['email']; ?>" required>
		</div>
		
		<div class="input-group">
		<label>User Status:</label>
		<input type="text" name="usertype" value="<?php echo $_SESSION['usertype']; ?>" required>
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