<?php

//Connect to Database
include 'init.php';
//unset($_SESSION['scanneditemlist']);

if (!isset($_SESSION['user_id']))
{
	array_push($errors, "Please log in first");
	header('location: login.php');
}

if (isset($_POST['validate']))
{
	$keynumber = mysqli_real_escape_string($db, $_POST['keynumber']);
	$trimmedKeyNumber = substr($keynumber, 1, 9);
	
	$query = "SELECT * FROM user_info WHERE keynumber = ".$trimmedKeyNumber."";
	$results = mysqli_query($db, $query);
	
	if (mysqli_num_rows($results) == 1)
	{
		$row = mysqli_fetch_array($results);
		
		$studentuserid = $row[0];
		$username = $row[1];
		$_SESSION['studentusername'] = $username;
		$firstname = $row[3];
		$lastname = $row[4];
		$email = $row[5];
		$possessionQty = $row[8];
		$serverkeynumber = $row[9];
		$nameandID = $firstname . " " . $lastname . " (" . $username . ") ";
	}
	else
	{
		array_push($errors, "Unable to find student");
	}
}

if (isset($_POST['next']))
{
	$_SESSION['studentuserid'] = mysqli_real_escape_string($db, $_POST['keynumber']);
	$_SESSION['studentfullname'] = mysqli_real_escape_string($db, $_POST['fullname']);
	$checkEmail = mysqli_real_escape_string($db, $_POST['email']);

	if (!empty($checkEmail))  //??
	{
		header("Location: cartandpossessions.php");
	}
	else
	{
		array_push($errors, "A student must be verified first!");
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/studentaccount.css">
</head>
<body>
	<div class="header">
	</div>
	
	<div id="container">
	<div id="leftcolumn">	
	<ul>
	<h1>PartsCribber</h1>
	<li><a href="mainoperationsA.php">Item Check-Out</a></li>
	<li><a class="active" href="studentaccount.php">Student Account</a></li>
	<li><a href="registeruser.php">Registration</a></li>
	<li><a href="#about">Removals</a></li>
	<li><a href="#about">Update Inventory</a></li>
	<li><a href="vieweditprofile.php">Profile Settings</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	<div id="rightcolumn">
	<form method="post" action="studentaccount.php">
	
		<div class="title">
		<h3>
		<?php echo "Student Account - Student Authentication (1/3)"; ?>
		</h3>
		</div>
		
		<!-- success message -->
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
		<input type="password" id="keynumber" name="keynumber" placeholder="Swipe ID or Enter Key Number here">
		</div>
		
		<div class="input-group">
		<button class="btn" name="validate">Validate Student</button>
		</div>
		
	</form>
	
	<form method="post" action="studentaccount.php">
		
		<div class="input-group">
		<label>Full Name & Student Number:</label>
		<input type="text" value="<?php echo $nameandID ?>" name="fullname">
		</div>
		
		<div class="input-group">
		<label>Email:</label>
		<input type="text" name="email" id="email" value="<?php echo $email; ?>">
		</div>
		
		<div class="input-group">
		<label>Key Number:</label>
		<input type="text" value="<?php echo $serverkeynumber  ?>" name="keynumber">
		</div>
		
		<div class="input-group">
		<label>Possession Quantity:</label>
		<input type="text" value="<?php echo $possessionQty; ?>" name="possessionqty">
		</div>
		
		<div class="input-group">
		<button class="btn" name="next" id="next">CONFIRM</button>
		</div>
		
		<div class="input-group">
		<button class="btn" id="cancel" name="cancel">CANCEL</button>
		</div>
	</form>
	
	</div>
	<div style="clear: both;"></div>
	</div>
		
</body>
</html>