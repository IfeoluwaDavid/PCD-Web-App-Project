<?php

//Connect to Database
include 'init.php';
include 'functions.php';

$currentTab = "defaultOpen";

if (!isset($_SESSION['user_id']))
{
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
}

// REGISTER STUDENT
if (isset($_POST['reg_student'])) 
{
	$currentTab = "defaultOpen";
	
	// receive all input values from the form
	$studentusername = mysqli_real_escape_string($db, $_POST['studentusername']);
	$studentemail = mysqli_real_escape_string($db, $_POST['studentemail']);
	$studentfirstname= mysqli_real_escape_string($db, $_POST['studentfirstname']);
	$studentlastname = mysqli_real_escape_string($db, $_POST['studentlastname']);
	$studentpassword_1 = mysqli_real_escape_string($db, $_POST['studentpassword_1']);
	$studentpassword_2 = mysqli_real_escape_string($db, $_POST['studentpassword_2']);
	
	$trimmedStudentUsername = substr($studentusername, 1, 9);

	if(empty($trimmedStudentUsername) || empty($studentemail) || empty($studentfirstname) || 
	   empty($studentlastname) || empty($studentpassword_1) || empty($studentpassword_2))
	{
		array_push($errors, "Incomplete form. Some fields are still blank.");
	}
	else
	{
		if(!validateStudentUsername($trimmedStudentUsername))
		{
			array_push($errors, "Student number/username format is invalid");
		}
		
		if(!nameValidation($studentfirstname) || !nameValidation($studentlastname))
		{
			array_push($errors, "Only letters are allowed in first/last name. Must range between 2 to 10 characters");
		}
		
		if(!passwordValidation($studentpassword_1))
		{
			array_push($errors, "Password must range between 8 to 15 characters long");
		}
		
		if ($studentpassword_1 != $studentpassword_2)
		{
			array_push($errors, "The two passwords do not match");
		}
	
		// register user if there are no errors in the form
		if (count($errors) == 0) 
		{
			$validationQuery = "SELECT * FROM `user_info` WHERE `email` = '$studentemail' OR `username` = '$studentusername' 
			OR `keynumber` = '$trimmedStudentUsername'";
			
			$result = mysqli_query($db, $validationQuery);
			
			if(mysqli_num_rows($result) > 0)
			{
				array_push($errors, "Existing User Information, Please Change Username, Email or ID used");
			}
			else
			{
				$ENCstudentpassword = md5($studentpassword_1);//encrypt the password before saving in the database
				
				$query = "INSERT INTO user_info (username, first_name, last_name, email, password, keynumber) VALUES
				('$trimmedStudentUsername','$studentfirstname','$studentlastname','$studentemail','$ENCstudentpassword','$trimmedStudentUsername')";
				
				mysqli_query($db, $query);
				
				$cartQuery = "ALTER TABLE `cart_info` ADD `$trimmedStudentUsername` INT NOT NULL DEFAULT '0'";
				mysqli_query($db, $cartQuery);
				 
				$_SESSION['success'] = "Student has been registered successfully!";
				
				unset($studentusername);
				unset($studentfirstname);
				unset($studentlastname);
				unset($studentemail);
			}
		}
	}
}

// REGISTER ADMIN
if (isset($_POST['reg_admin'])) 
{
	$currentTab = "AdminTab";
	
	// receive all input values from the form
	$adminusername = mysqli_real_escape_string($db, $_POST['adminusername']);
	$adminemail = mysqli_real_escape_string($db, $_POST['adminemail']);
	$adminfirstname = mysqli_real_escape_string($db, $_POST['adminfirstname']);
	$adminlastname = mysqli_real_escape_string($db, $_POST['adminlastname']);
	$adminpassword_1 = mysqli_real_escape_string($db, $_POST['adminpassword_1']);
	$adminpassword_2 = mysqli_real_escape_string($db, $_POST['adminpassword_2']);
	
	$adminusertype = 1;
	
	if(empty($adminusername) || empty($adminemail) || empty($adminfirstname) || 
	   empty($adminlastname) || empty($adminpassword_1) || empty($adminpassword_2))
	{
		array_push($errors, "Incomplete form. Some fields are still blank.");
	}
	else
	{
		if(!validateAdminUsername($adminusername))
		{
			array_push($errors, "Admin username format is invalid");
		}
		
		if(!nameValidation($adminfirstname) || !nameValidation($adminlastname))
		{
			array_push($errors, "Only letters are allowed in first/last name. Must range between 2 to 10 characters");
		}
		
		if(!passwordValidation($adminpassword_1))
		{
			array_push($errors, "Password must range between 8 to 15 characters long");
		}
		
		if ($adminpassword_1 != $adminpassword_2)
		{
			array_push($errors, "The two passwords do not match");
		}
		// register user if there are no errors in the form
		if (count($errors) == 0) 
		{
			$validationQuery = "SELECT * FROM `user_info` WHERE `email` = '$adminemail' OR `username` = '$adminusername'";
			$result = mysqli_query($db, $validationQuery);
			
			if(mysqli_num_rows($result) > 0)
			{
				array_push($errors, "Existing User Information, Please Change Username or Email");
			}
			else
			{
				$ENCadminpassword = md5($adminpassword_1);//encrypt the password before saving in the database
				
				$query = "INSERT INTO user_info (username, first_name, last_name, email, password) 
				VALUES('$adminusername', '$adminfirstname', '$adminlastname', '$adminemail', '$ENCadminpassword')";
				
				mysqli_query($db, $query);
				
				$updateQuery = "UPDATE `user_info` SET `admin_status` = '1' WHERE `username`= '$adminusername'";
				mysqli_query($db, $updateQuery);
				
				$_SESSION['success'] = "New Admin has been registered successfully!";
				
				unset($adminusername);
				unset($adminfirstname);
				unset($adminlastname);
				unset($adminemail);
			}
		}
	}
}

// REGISTER ADMIN
if (isset($_POST['reg_item'])) 
{
	$currentTab = "itemTab";
	
	// receive all input values from the form
	$itemname = mysqli_real_escape_string($db, $_POST['itemname']);
	$itemserialno = mysqli_real_escape_string($db, $_POST['itemserialno']);
	$itemtotalqty = mysqli_real_escape_string($db, $_POST['itemtotalqty']);
	$itemcategory = mysqli_real_escape_string($db, $_POST['itemcategory']);
	$yourpassword = mysqli_real_escape_string($db, $_POST['yourpassword']);
	$confirmyourpassword = mysqli_real_escape_string($db, $_POST['confirmyourpassword']);
	
	if(empty($itemname) || empty($itemserialno) || empty($itemtotalqty) || empty($itemcategory) || empty($yourpassword) || empty($confirmyourpassword))
	{
		array_push($errors, "Incomplete form. Some fields are still blank.");
	}
	else
	{
		if(!validateItemName($itemname))
		{
			array_push($errors, "Item name must be between 3 to 15 characters.");
		}
		
		if(!validateSerialNo($itemserialno))
		{
			array_push($errors, "Item serial number must be exactly 6 digits.");
		}
		
		if(!validateItemName($itemcategory))
		{
			array_push($errors, "Item category must be 3 to 15 characters.");
		}
		
		if($yourpassword != $confirmyourpassword)
		{
			array_push($errors, "The two passwords do not match");
		}
		
		// register user if there are no errors in the form
		if (count($errors) == 0) 
		{	
			$ENCyourpassword = md5($yourpassword); //encrypt the password before saving in the database
			
			$mysql_query = "SELECT * FROM `user_info` WHERE `user_id` = '".$_SESSION['user_id']."' AND `password` = '$ENCyourpassword';";
			$result = mysqli_query($db, $mysql_query);
			
			if (mysqli_num_rows($result) == 1)
			{
				$validationQuery = "SELECT * FROM `item_info` WHERE `item_name` = '$itemname' OR `serial_no` = '$itemserialno'";
				$result = mysqli_query($db, $validationQuery);
				
				if(mysqli_num_rows($result) > 0)
				{
					array_push($errors, "Existing Item Information, Please Change Item Name or Serial Number");
				}
				else
				{	
					$updateQuery = "INSERT INTO `item_info` (`item_name`, `serial_no`, `available_qty`, `total_qty`,`category`) 
					VALUES ('$itemname', '$itemserialno', '$itemtotalqty', '$itemtotalqty', '$itemcategory')";
					
					$cartQuery = "INSERT INTO `cart_info` (`item_name`) VALUES ('$itemname')";
					mysqli_query($db, $cartQuery);
					
					$updateCartInfoQuantity = "UPDATE `cart_info` SET `available_qty` = '$itemtotalqty' WHERE `item_name` = '$itemname'";
					mysqli_query($db, $updateCartInfoQuantity);
						
					$rentalQuery = "INSERT INTO `rental_info` (`item_name`) VALUES ('$itemname')";
					mysqli_query($db, $rentalQuery);
					
					$updateRentalInfoQuantity = "UPDATE `rental_info` SET `available_qty` = '$itemtotalqty' WHERE `item_name` = '$itemname'";
					mysqli_query($db, $updateRentalInfoQuantity);
					
					mysqli_query($db, $updateQuery);	 
				    	$_SESSION['success'] = "Item has been successfully added to inventory";
				    	
				    	unset($itemname);
					unset($itemserialno);
					unset($itemtotalqty);
					unset($itemcategory);
				}
			}
			else
			{
				array_push($errors, "Incorrect current password.");
			}
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/registeruser.css">
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
	<li><a class="active" href="registeruser.php">Registration</a></li>
	<li><a href="#about">Removals</a></li>
	<li><a href="#about">Update Inventory</a></li>
	<li><a href="vieweditprofile.php">Profile Settings</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	<div id="rightcolumn">
	
	<form method="post" action="registeruser.php">
	
		<div class="title">
		<h3>
		<?php echo "Registration"; ?>
		</h3>
		</div>
		
		<div class="tab">
		<button class="tablinks" onclick="openCity(event, 'Student'); return false;" id="defaultOpen">Register Student</button>
		<button class="tablinks" onclick="openCity(event, 'Admin'); return false;" id="AdminTab">Register Admin</button>
		<button class="tablinks" onclick="openCity(event, 'item'); return false;" id="itemTab">Register Item</button>
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
		
		<div id="Student" class="tabcontent">
		
		<div class="input-group">
		<label>Student Number/Username:</label>
		<input type="password" id="keynumber" name="studentusername" placeholder="Swipe ID Card Here">
		</div>
		
		<div class="input-group">
		<label>Student Firstname:</label>
		<input type="text" name="studentfirstname" value="<?php echo $studentfirstname; ?>">
		</div>
		
		<div class="input-group">
		<label>Student Lastname:</label>
		<input type="text" name="studentlastname" value="<?php echo $studentlastname; ?>">
		</div>
		
		<div class="input-group">
		<label>Student Email:</label>
		<input type="email" name="studentemail" value="<?php echo $studentemail; ?>">
		</div>
		
		<div class="input-group">
		<label>Student Password:</label>
		<input type="password" name="studentpassword_1">
		</div>
		
		<div class="input-group">
		<label>Confirm Student Password:</label>
		<input type="password" name="studentpassword_2">
		</div>
		
		<div class="input-group">
		<button type="submit" class="btn" name="reg_student">Register Student</button>
		</div>
		
		</div>
		
		<div id="Admin" class="tabcontent">
		
		<div class="input-group">
		<label>Admin Username:</label>
		<input type="text" name="adminusername" value="<?php echo $adminusername; ?>">
		</div>
		
		<div class="input-group">
		<label>Admin Firstname:</label>
		<input type="text" name="adminfirstname" value="<?php echo $adminfirstname; ?>">
		</div>
		
		<div class="input-group">
		<label>Admin Lastname:</label>
		<input type="text" name="adminlastname" value="<?php echo $adminlastname; ?>">
		</div>
		
		<div class="input-group">
		<label>Admin Email:</label>
		<input type="email" name="adminemail" value="<?php echo $adminemail; ?>">
		</div>
		
		<div class="input-group">
		<label>Admin Password:</label>
		<input type="password" name="adminpassword_1">
		</div>
		
		<div class="input-group">
		<label>Confirm Admin Password:</label>
		<input type="password" name="adminpassword_2">
		</div>
		
		<div class="input-group">
		<button type="submit" class="btn" name="reg_admin">Register Admin</button>
		</div>
		
		</div>
		
		<div id="item" class="tabcontent">
		
		<div class="input-group">
		<label>Item Name:</label>
		<input type="text" name="itemname" value="<?php echo $itemname; ?>">
		</div>
		
		<div class="input-group">
		<label>Item Serial Number:</label>
		<input type="number" name="itemserialno" value="<?php echo $itemserialno; ?>">
		</div>
		
		<div class="input-group">
		<label>Item Total Quantity:</label>
		<input type="number" name="itemtotalqty" min="1" value="<?php echo $itemtotalqty; ?>">
		</div>
		
		<div class="input-group">
		<label>Item Category:</label>
		<input type="text" name="itemcategory" value="<?php echo $itemcategory; ?>">
		</div>
		
		<div class="input-group">
		<label>Your Admin Password:</label>
		<input type="password" name="yourpassword">
		</div>
		
		<div class="input-group">
		<label>Confirm Your Admin Password:</label>
		<input type="password" name="confirmyourpassword">
		</div>
		
		<div class="input-group">
		<button type="submit" class="btn" name="reg_item">Register Item</button>
		</div>		
		
		</div>
		
	</form>
	
	
	</div>
	<div style="clear: both;"></div>
	</div>
	
		
	<script>
	function openCity(evt, cityName)
	{
	    var i, tabcontent, tablinks;
	    tabcontent = document.getElementsByClassName("tabcontent");
	    for (i = 0; i < tabcontent.length; i++)
	    {
	        tabcontent[i].style.display = "none";
	    }
	    tablinks = document.getElementsByClassName("tablinks");
	    for (i = 0; i < tablinks.length; i++)
	    {
	        tablinks[i].className = tablinks[i].className.replace(" active", "");
	    }
	    document.getElementById(cityName).style.display = "block";
	    evt.currentTarget.className += " active";
	}
	
	// Get the element with id="defaultOpen" and click on it
	document.getElementById("<?php echo $currentTab; ?>").click();
	</script>
		
</body>
</html>

