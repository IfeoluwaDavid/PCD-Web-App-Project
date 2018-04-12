<?php

//Connect to Database
include 'init.php';
include 'functions.php';

if (!isset($_SESSION['user_id']))
{
	array_push($errors, "Please log in first");
	header('location: login.php');
}

if (isset($_POST['update_item']))
{
	$currentTab = "itemTab";
	
	// receive all input values from the form
	$itemname = mysqli_real_escape_string($db, $_POST['itemname']);
	$itemserialno = mysqli_real_escape_string($db, $_POST['serialno']);
	$itemavailableqty = mysqli_real_escape_string($db, $_POST['availableQty']);
	$itemtotalqty = mysqli_real_escape_string($db, $_POST['totalQty']);
	$itemcategory = mysqli_real_escape_string($db, $_POST['itemcategory']);
	
	if(empty($itemname) || empty($itemserialno) || empty($itemavailableqty) || empty($itemtotalqty) || empty($itemcategory))
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
		
		if($itemavailableqty > $itemtotalqty)
		{
			array_push($errors, "Available quantity cannot be more than total stock quantity.");
		}
		
		// register user if there are no errors in the form
		if (count($errors) == 0)
		{
			$validationQuery = "SELECT * FROM `item_info` WHERE `item_name` = '$itemname' OR `serial_no` = '$itemserialno'";
			$result = mysqli_query($db, $validationQuery);
			
			if(mysqli_num_rows($result) > 1)
			{
				array_push($errors, "Existing Item Information, Please Change Item Name or Serial Number");
			}
			else
			{	
				$updateItemInfoQuery = "UPDATE `item_info` SET  
				`item_name` = '$itemname', 
				`serial_no` = '$itemserialno',
				`available_qty` = '$itemavailableqty',
				`total_qty` = '$itemtotalqty',
				`category` = '$itemcategory'
				WHERE `item_id` = '".$_SESSION['itemid']."'";
				$result1 = mysqli_query($db, $updateItemInfoQuery);
				
				$updateCartInfoQuery = "UPDATE `cart_info` SET  `item_name` = '$itemname', `available_qty` = '$itemavailableqty' WHERE `id` = '".$_SESSION['itemid']."'";
				$result2 = mysqli_query($db, $updateCartInfoQuery);
				
				$updateRentalInfoQuery = "UPDATE `rental_info` SET  `item_name` = '$itemname', `available_qty` = '$itemavailableqty' WHERE `id` = '".$_SESSION['itemid']."'";
				$result3 = mysqli_query($db, $updateRentalInfoQuery);
				
				if(!$result1 || !$result2 || !$result3)
				{
					array_push($errors, "Server side error!  Please Try Again.");
				}
				else
				{
				    	$_SESSION['success'] = "Item information has been updated successfully.";
				}
			}
		}
	}
}

if (isset($_POST['validate']))
{
	$itementry = mysqli_real_escape_string($db, $_POST['itementry']);
	
	$query = "SELECT * FROM item_info WHERE `serial_no`= '$itementry'";
	$results = mysqli_query($db, $query);
	//echo $query;
	
	if (mysqli_num_rows($results) == 1)
	{
		$row = mysqli_fetch_array($results);
		
		$_SESSION['itemid'] = $row[0];
		$itemname = $row[1];
		$itemserialno = $row[2];
		$itemavailableqty = $row[3];
		$itemtotalqty = $row[5];
		$itemcategory = $row[6];
	}
	else
	{
		array_push($errors, "Unable to find item");
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/inventory.css">
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
	<li><a class="active" href="inventory.php">Update Inventory</a></li>
	<li><a href="reports.php">Daily Reports</a></li>
	<li><a href="vieweditprofile.php">Profile Settings</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	<div id="rightcolumn">
	<form method="post" action="inventory.php">
	
		<div class="title">
		<h3>
		<?php echo "Inventory - Update Item Information"; ?>
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
		<input type="text"  name="itementry" placeholder="Scan or Type Item Barcode Here">
		</div>
		
		<div class="input-group">
		<button class="btn" name="validate">Fetch Item</button>
		</div>
		
	</form>
	
	<form method="post" action="inventory.php">
		
		<div class="input-group">
		<label>Item Name:</label>
		<input type="text" value="<?php echo $itemname?>" name="itemname">
		</div>
		
		<div class="input-group">
		<label>Serial Number:</label>
		<input type="text" value="<?php echo $itemserialno; ?>" name="serialno">
		</div>
		
		<div class="input-group">
		<label>Available Quantity:</label>
		<input type="text" value="<?php echo $itemavailableqty?>" name="availableQty">
		</div>
		
		<div class="input-group">
		<label>Total Stock Quantity:</label>
		<input type="text" value="<?php echo $itemtotalqty; ?>" name="totalQty">
		</div>
		
		<div class="input-group">
		<label>Item Category:</label>
		<input type="text" value="<?php echo $itemcategory; ?>" name="itemcategory">
		</div>
		
		<div class="input-group">
		<button class="btn" name="update_item" id="update">Update Item Information</button>
		</div>
	</form>
	
	</div>
	<div style="clear: both;"></div>
	</div>
		
</body>
</html>