<?php

//Connect to Database
include 'init.php';
$_SESSION['holdItemQuantity'] = array();

if (!isset($_SESSION['user_id']))
{
	array_push($errors, "Please log in first");
	header('location: login.php');
}

if (isset($_POST["back"]))
{
	header('location: mainoperationsB.php');	
}

if (isset($_POST["approve"]))
{
	if(empty($_SESSION['scanneditemlist']))
	{
		//Do nothing
	}
	else
	{
		$listLength = sizeof($_SESSION['scanneditemlist']);
		$checks = 0;
		
		for ($x = 0; $x < $listLength; $x++)
		{
			$wantedItem = $_SESSION['scanneditemlist'][$x];
			$requestedQuantity = $_POST['items'][$x];
			
			array_push($_SESSION['holdItemQuantity'], $requestedQuantity);
		
			$fetchQuery = "SELECT `available_qty` FROM `rental_info` WHERE `item_name` = '$wantedItem';";
			$result = mysqli_query($db, $fetchQuery);
			
			while($row = mysqli_fetch_array($result))
			{
				$AvailableQuantity = $row[0];
				
				if ($requestedQuantity > $AvailableQuantity)
				{
					array_push($errors, "Only ".$AvailableQuantity." ".$wantedItem."s available at the moment.");
					break;
				}
				else
				{	
					$checks++;
				}
			}
		}
		
		if ($checks == $listLength)
		{
			$username = $_SESSION['studentusername'];
			$checkForColumn = "SELECT `$username` FROM `rental_info`;";
			$col = mysqli_query($db, $checkForColumn);
			
			if (!$col)
			{
				$createColumnQuery = "ALTER TABLE `rental_info` ADD `$username` INT NOT NULL DEFAULT '0';";
				mysqli_query($db, $createColumnQuery );
			}
		
			for ($x = 0; $x < $listLength; $x++)
			{
				$wantedItem = $_SESSION['scanneditemlist'][$x];
				$requestedQuantity = $_POST['items'][$x];
			
				$updateQuery = "UPDATE `rental_info` SET `$username` = `$username` + '$requestedQuantity' WHERE `item_name` = '$wantedItem';";
				mysqli_query($db, $updateQuery);
				
				$updateItemInfoQuantity = "UPDATE `item_info` SET `available_qty` = `available_qty` - '$requestedQuantity' WHERE `item_name` = '$wantedItem';";
				mysqli_query($db, $updateItemInfoQuantity);
				
				$updateRentedQty = "UPDATE `item_info` SET `rented_qty` = `rented_qty` + '$requestedQuantity' WHERE `item_name` = '$wantedItem';";
				mysqli_query($db, $updateRentedQty);
				
				$updateCartInfoQuantity = "UPDATE `cart_info` SET `available_qty` = `available_qty` - '$requestedQuantity' WHERE `item_name` = '$wantedItem';";
				mysqli_query($db, $updateCartInfoQuantity);
				
				$updateRentalInfoQuantity = "UPDATE `rental_info` SET `available_qty` = `available_qty` - '$requestedQuantity' WHERE `item_name` = '$wantedItem';";
				mysqli_query($db, $updateRentalInfoQuantity);
				
				$updatePossessionQuantity = "UPDATE `user_info` SET `possession_qty` = `possession_qty` + '$requestedQuantity' WHERE `username` = '$username';";
				mysqli_query($db, $updatePossessionQuantity);
				
				if ($x == $listLength - 1)
				{
				
				$_SESSION['success'] = "Items successfully approved for ".$_SESSION['studentfullname']."";
				unset($_SESSION['scanneditemlist']);
				unset($_SESSION['holdItemQuantity']);
				unset($_SESSION['studentuserid']);
				unset($_SESSION['studentfullname']);
				unset($_SESSION['studentusername']);
				$checks = 0;
				
				break;
				
				}
			}
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/reports.css">
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
	<li><a class="active" href="reports.php">Daily Reports</a></li>
	<li><a href="vieweditprofile.php">Profile Settings</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	<div id="rightcolumn">
	<form method="post" action="reports.php">
	
		<div class="title">
		<h3>
		<?php echo "Daily Reports - Pending Student Returns"; ?>
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
	
		<select id="selectholder" name="color" size="20" class="select">
		</select>
		
		<div class="input-group">
		<button class="approveBtn" name="">View Student</button>
		</div>
		
	</form>
	
	</div>
	<div style="clear: both;"></div>
	</div>
		
</body>
</html>

<?php

if (isset($_POST["delete"]))
{	
	$itemtobeDeleted = $_POST["delete"];
	
	for ($x = 0; $x < sizeof($_SESSION['scanneditemlist']); $x++)
	{
		$requestedQuantity = $_POST['items'][$x];
		array_push($_SESSION['holdItemQuantity'], $requestedQuantity);
	}
	
	//echo sizeof($_SESSION['scanneditemlist']);
	//echo sizeof($_SESSION['holdItemQuantity']);
		
	if (in_array($itemtobeDeleted, $_SESSION['scanneditemlist']))
	{
		$sessionIndex = array_search($itemtobeDeleted, $_SESSION['scanneditemlist']);
		array_splice($_SESSION['scanneditemlist'], $sessionIndex, 1);
		array_splice($_SESSION['holdItemQuantity'], $sessionIndex, 1);
	}
	else
	{
		//echo "Match not found";
	}
}

function showItems()
{
	$itemindex = 0;
	
	?>
	<script type="text/javascript">
	var itemIndex = "<?php echo $itemindex; ?>";
	</script>
	<?php

	if(empty($_SESSION['holdItemQuantity']))
	{
		foreach ($_SESSION['scanneditemlist'] as $value)
		{
			$itemValue = $value;
			?>
			<script type="text/javascript">
			
			var f = document.getElementById("itemlist");
			
			var item = document.createElement("div");
			item.className = ('input-group');
			
			var label = document.createElement("label");
			var itemValue = "<?php echo $itemValue; ?>";
			label.innerHTML = itemValue;
			
			var qty = document.createElement("input");
			qty.setAttribute('type',"number");
			qty.setAttribute('min',"1");
			qty.setAttribute('max',"20");
			qty.setAttribute('name',"items[]");
			qty.setAttribute('value',"1");
	
			var delBtn = document.createElement("button");
			delBtn.setAttribute('name',"delete");
			delBtn.setAttribute('value',itemValue);
			delBtn.className = ('delbtn');
			delBtn.innerHTML = "x";
			
			item.appendChild(label);
			item.appendChild(qty);
			item.appendChild(delBtn);
			
			document.getElementById("itemlist").appendChild(item);
			
			itemIndex++;
		
			</script>
			<?php
		}
	}
	else
	{	
		$myLength = sizeof($_SESSION['holdItemQuantity']);
		for ($x = 0; $x < $myLength; $x++)
		{
			$itemValue = $_SESSION['scanneditemlist'][$x];
			//$itemQty = $_SESSION['holdItemQuantity'][$x];
				
			?>
			<script type="text/javascript">
			
			var f = document.getElementById("itemlist");
			
			var item = document.createElement("div");
			item.className = ('input-group');
			
			var label = document.createElement("label");
			var itemValue = "<?php echo $itemValue; ?>";
			label.innerHTML = itemValue;
			
			var qty = document.createElement("input");
			qty.setAttribute('type',"number");
			qty.setAttribute('min',"1");
			qty.setAttribute('max',"20");
			qty.setAttribute('name',"items[]");
			qty.setAttribute('value',"<?php echo $_SESSION['holdItemQuantity'][$x] ?>");
	
			var delBtn = document.createElement("button");
			delBtn.setAttribute('name',"delete");
			delBtn.setAttribute('value',itemValue);
			delBtn.className = ('delbtn');
			delBtn.innerHTML = "x";
			
			item.appendChild(label);
			item.appendChild(qty);
			item.appendChild(delBtn);
			
			document.getElementById("itemlist").appendChild(item);
			
			itemIndex++;
		
			</script>
			<?php
		}
	}
}

function clearForm()
{
	?>
	<script type="text/javascript">
	document.getElementById("itemlist").innerHTML = "";
	</script>
	<?php
}

clearForm();
showItems();

?>