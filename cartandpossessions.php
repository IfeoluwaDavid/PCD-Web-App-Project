<?php

//dbnect to Database
include 'init.php';
include 'functions.php';

$currentTab = "defaultOpen";

unset($_SESSION['cartlist']);
unset($_SESSION['holdCartItemQuantity']);
unset($_SESSION['possessionlist']);
unset($_SESSION['holdRentedItemQuantity']);

if (!isset($_SESSION['user_id']))
{
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
}

// REGISTER STUDENT
if (isset($_POST['reg_student'])) 
{
	$currentTab = "defaultOpen";
}

if(!isset($_SESSION['cartlist']))
{
	//If it doesn't, create an empty array.
	$_SESSION['cartlist'] = array();
}
else
{
	//Do Nothing
}
if(!isset($_SESSION['holdCartItemQuantity']))
{
	//If it doesn't, create an empty array.
	$_SESSION['holdCartItemQuantity'] = array();
}
else
{
	//Do Nothing
}

if(!isset($_SESSION['holdRentedItemQuantity']))
{
	//If it doesn't, create an empty array.
	$_SESSION['holdRentedItemQuantity'] = array();
}
else
{
	//Do Nothing
}

if(!isset($_SESSION['possessionlist']))
{
	//If it doesn't, create an empty array.
	$_SESSION['possessionlist'] = array();
}
else
{
	//Do Nothing
}

if(isset($_POST["exit"]))
{
	header('location: studentaccount.php');
}
	
$fetchQueryA = "SELECT `item_name`, `".$_SESSION['studentuserid']."` FROM `cart_info` WHERE `".$_SESSION['studentuserid']."` > '0';";
$result = mysqli_query($db, $fetchQueryA);

while($row = mysqli_fetch_array($result))
{
	if (in_array($row[0], $_SESSION['cartlist']))
	{
		//Do Nothing
	}
	else
	{
		//Add a product ID to our cart session variable.
		$_SESSION['cartlist'][] = $row[0];
	}
}

if (isset($_POST["delete"]))
{	
	$itemtobeDeleted = $_POST["delete"];
	
	$updateQuery = "UPDATE `cart_info` SET `".$_SESSION['studentuserid']."` = '0' WHERE `item_name` = '$itemtobeDeleted';";
	mysqli_query($db, $updateQuery);
	
	for ($x = 0; $x < sizeof($_SESSION['cartlist']); $x++)
	{
		$requestedQuantity = $_POST['cartitems'][$x];
		array_push($_SESSION['holdCartItemQuantity'], $requestedQuantity);
	}
		
	if (in_array($itemtobeDeleted, $_SESSION['cartlist']))
	{
		$sessionIndex = array_search($itemtobeDeleted, $_SESSION['cartlist']);
		array_splice($_SESSION['cartlist'], $sessionIndex, 1);	
		array_splice($_SESSION['holdCartItemQuantity'], $sessionIndex, 1);
	}
	else
	{
		//echo "Match not found";
	}
}

if (isset($_POST["singleApprove"]))
{
	$wantedItemSA = $_POST["singleApprove"];
	$wantedItemIndex = array_search($wantedItemSA, $_SESSION['cartlist']);
	$requestedQuantitySA = $_POST['cartitems'][$wantedItemIndex];
	
	//unset($_SESSION['holdCartItemQuantity'])
		
	$checkForColumn = "SELECT `".$_SESSION['studentuserid']."` FROM `rental_info`;";
	$col = mysqli_query($db, $checkForColumn);
	
	if (!$col)
	{
		$createColumnQuery = "ALTER TABLE `rental_info` ADD `".$_SESSION['studentuserid']."` INT NOT NULL DEFAULT '0';";
		mysqli_query($db, $createColumnQuery);
	} 
	
	$fetchQuery = "SELECT `item_name`, `".$_SESSION['studentuserid']."`, `available_qty` FROM `cart_info` WHERE `item_name` = '$wantedItemSA';";
	$result = mysqli_query($db, $fetchQuery);
	
	while($row = mysqli_fetch_array($result))
	{
		$TheItemName = $row[0];
		$RequestedQuantity = $requestedQuantitySA;
		$AvailableQuantity = $row[2];
		
		if ($RequestedQuantity > $AvailableQuantity)
		{
			$problemfound = true;
			break;
		}
	}
	
	for ($x = 0; $x < sizeof($_SESSION['cartlist']); $x++)
	{
		$requestedQuantity = $_POST['cartitems'][$x];
		array_push($_SESSION['holdCartItemQuantity'], $requestedQuantity);
	}
		
	if($problemfound == true)
	{
		if($AvailableQuantity > 1)
		{
			array_push($errors, "Only ".$AvailableQuantity." ".$TheItemName."s available at the moment.");
		}
		else if($AvailableQuantity == 1)
		{
			array_push($errors, "Only ".$AvailableQuantity." ".$TheItemName." available at the moment.");
		}
		else
		{
			array_push($errors, "No ".$TheItemName."s available at the moment.");
		}
	}
	else
	{	
		$fetchQuery = "SELECT `item_name`, `".$_SESSION['studentuserid']."`, `available_qty` FROM `cart_info` WHERE `item_name` = '$wantedItemSA';";
		$result = mysqli_query($db, $fetchQuery);
	
		while($row = mysqli_fetch_array($result))
		{
			$TheItemName = $row[0];
			$RequestedQuantity = $requestedQuantitySA;
			$AvailableQuantity = $row[2];
			
			$updateQuery = "UPDATE `rental_info` SET `".$_SESSION['studentuserid']."` = `".$_SESSION['studentuserid']."` + '$RequestedQuantity ' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateQuery);
			
			$updateItemInfoQuantity = "UPDATE `item_info` SET `available_qty` = `available_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateItemInfoQuantity);
			
			$updateRentedQty = "UPDATE `item_info` SET `rented_qty` = `rented_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateRentedQty);
			
			$updateCartInfoQuantity = "UPDATE `cart_info` SET `available_qty` = `available_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateCartInfoQuantity);
			
			$updateRentalInfoQuantity = "UPDATE `rental_info` SET `available_qty` = `available_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateRentalInfoQuantity);
			
			$updatePossessionQuantity = "UPDATE `user_info` SET `possession_qty` = `possession_qty` + '$RequestedQuantity' WHERE `username` = '".$_SESSION['studentuserid']."';";
			mysqli_query($db, $updatePossessionQuantity);
		}
	
		$clearCartQuery = "UPDATE `cart_info` SET `".$_SESSION['studentuserid']."` = '0' WHERE `item_name` = '$TheItemName';";
		mysqli_query($db, $clearCartQuery);

	    	$_SESSION['success'] = "Successful Rental. Check Student Possessions to Verify.";
	    	
		if (in_array($wantedItemSA, $_SESSION['cartlist']))
		{
			$sessionIndex = array_search($wantedItemSA, $_SESSION['cartlist']);
			array_splice($_SESSION['cartlist'], $sessionIndex, 1);	
			array_splice($_SESSION['holdCartItemQuantity'], $sessionIndex, 1);
		}
		else
		{
			//echo "Match not found";
		}
	}
}

if (isset($_POST["approveall"]))
{
	$checkForColumn = "SELECT `".$_SESSION['studentuserid']."` FROM `rental_info`;";
	$col = mysqli_query($db, $checkForColumn);
	
	if (!$col)
	{
		$createColumnQuery = "ALTER TABLE `rental_info` ADD `".$_SESSION['studentuserid']."` INT NOT NULL DEFAULT '0';";
		mysqli_query($db, $createColumnQuery );
		//echo ".$_SESSION['studentuserid'].".' has been added to the database';
	} 
	
	$fetchQuery = "SELECT `item_name`, `".$_SESSION['studentuserid']."`, `available_qty` FROM `cart_info` WHERE `".$_SESSION['studentuserid']."` > '0';";
	$result = mysqli_query($db, $fetchQuery);
	
	$loopCounterAsIndex = 0;
	
	while($row = mysqli_fetch_array($result))
	{
		$TheItemName = $row[0];
		$RequestedQuantity = $_POST['cartitems'][$loopCounterAsIndex];
		$AvailableQuantity = $row[2];
		
		if ($RequestedQuantity > $AvailableQuantity)
		{
			$problemfound = true;
			break;
		}
		
		$loopCounterAsIndex++;
	}
		
	for ($x = 0; $x < sizeof($_SESSION['cartlist']); $x++)
	{
		$requestedQuantity = $_POST['cartitems'][$x];
		array_push($_SESSION['holdCartItemQuantity'], $requestedQuantity);
	}
	
	if($problemfound == true)
	{
		if($AvailableQuantity > 1)
		{
			array_push($errors, "Only ".$AvailableQuantity." ".$TheItemName."s available at the moment.");
		}
		else if($AvailableQuantity == 1)
		{
			array_push($errors, "Only ".$AvailableQuantity." ".$TheItemName." available at the moment.");
		}
		else
		{
			array_push($errors, "No ".$TheItemName."s available at the moment.");
		}
	}
	else
	{	
		$fetchQuery = "SELECT `item_name`, `".$_SESSION['studentuserid']."`, `available_qty` FROM `cart_info` WHERE `".$_SESSION['studentuserid']."` > '0';";
		$result = mysqli_query($db, $fetchQuery);
	
		$loopCounterAsIndex = 0;
		
		while($row = mysqli_fetch_array($result))
		{
			$TheItemName = $row[0];
			$RequestedQuantity = $_POST['cartitems'][$loopCounterAsIndex];
			$AvailableQuantity = $row[2];
			
			$updateQuery = "UPDATE `rental_info` SET `".$_SESSION['studentuserid']."` = `".$_SESSION['studentuserid']."` + '$RequestedQuantity ' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateQuery);
			
			$updateItemInfoQuantity = "UPDATE `item_info` SET `available_qty` = `available_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateItemInfoQuantity);
			
			$updateRentedQty = "UPDATE `item_info` SET `rented_qty` = `rented_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateRentedQty);
			
			$updateCartInfoQuantity = "UPDATE `cart_info` SET `available_qty` = `available_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateCartInfoQuantity);
			
			$updateRentalInfoQuantity = "UPDATE `rental_info` SET `available_qty` = `available_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
			mysqli_query($db, $updateRentalInfoQuantity);
			
			$updatePossessionQuantity = "UPDATE `user_info` SET `possession_qty` = `possession_qty` + '$RequestedQuantity' WHERE `username` = '".$_SESSION['studentuserid']."';";
			mysqli_query($db, $updatePossessionQuantity);
			
			$loopCounterAsIndex++;
		}
	
		$clearCartQuery = "UPDATE `cart_info` SET `".$_SESSION['studentuserid']."` = '0';";
		mysqli_query($db, $clearCartQuery);

	    	$_SESSION['success'] = "Successful Rental. Cart Cleared";
	    	unset($_SESSION['cartlist']);
		unset($_SESSION['holdCartItemQuantity']);
	}
}

if(isset($_POST["singleReturn"]))
{
	$TheItemName = $_POST["singleReturn"];
	$returnItemIndex = array_search($TheItemName, $_SESSION['possessionlist']);
	$RequestedQuantity = $_POST['rentalitems'][$returnItemIndex];
	
	$updateQuery = "UPDATE `rental_info` SET `".$_SESSION['studentuserid']."` = `".$_SESSION['studentuserid']."` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName'";
	$result = mysqli_query($db, $updateQuery);
	
	$updateItemInfoQuantity = "UPDATE `item_info` SET `available_qty` = `available_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
	$itemInfoUpdate = mysqli_query($db, $updateItemInfoQuantity);
	
	$updateRentedQty = "UPDATE `item_info` SET `rented_qty` = `rented_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
	$rentedQtyUpdate = mysqli_query($db, $updateRentedQty);
	
	$updateCartInfoQuantity = "UPDATE `cart_info` SET `available_qty` = `available_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
	$cartInfoUpdate = mysqli_query($db, $updateCartInfoQuantity);
	
	$updateRentalInfoQuantity = "UPDATE `rental_info` SET `available_qty` = `available_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
	$rentalInfoUpdate = mysqli_query($db, $updateRentalInfoQuantity);
	
	$updatePossessionQuantity = "UPDATE `user_info` SET `possession_qty` = `possession_qty` - '$RequestedQuantity' WHERE `username` = '".$_SESSION['studentuserid']."';";
	$possessionQtyUpdate = mysqli_query($db, $updatePossessionQuantity);
	
	if(!$result || !$itemInfoUpdate || !$rentedQtyUpdate || !$cartInfoUpdate || !$rentalInfoUpdate || !$possessionQtyUpdate)
	{
		array_push($errors, "Something went wrong. Please Try Again.");
	}
	else
	{	
		$dropColumnEligibility = "SELECT `".$_SESSION['studentuserid']."` FROM `rental_info` WHERE `".$_SESSION['studentuserid']."` > 0;";
		$dropColumnCheck = mysqli_query($db, $dropColumnEligibility);
		
		if(mysqli_num_rows($dropColumnCheck) < 1)
		{
			$dropColumn = "ALTER TABLE `rental_info` DROP `".$_SESSION['studentuserid']."`;";
			mysqli_query($db, $dropColumn);
		}
		
		if($RequestedQuantity > 1)
		{
			$_SESSION['success'] = "Successfully Returned ".$RequestedQuantity." ".$TheItemName."s.";
		}
		else
		{
			$_SESSION['success'] = "Successfully Returned ".$RequestedQuantity." ".$TheItemName.".";
		}	    	
	}
}

if(isset($_POST["returnall"]))
{
	$checkForColumn = "SELECT `".$_SESSION['studentuserid']."` FROM `rental_info`;";
	mysqli_query($db, $checkForColumn);
	
	$fetchQuery = "SELECT `item_name`, `".$_SESSION['studentuserid']."`, `available_qty` FROM `rental_info` WHERE `".$_SESSION['studentuserid']."` > '0';";
	$result = mysqli_query($db, $fetchQuery);
	
	while($row = mysqli_fetch_array($result))
	{
		$TheItemName = $row[0];
		$RequestedQuantity = $row[1];
		$AvailableQuantity = $row[2];
	
		$updateItemInfoQuantity = "UPDATE `item_info` SET `available_qty` = `available_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
		$itemInfoUpdate = mysqli_query($db, $updateItemInfoQuantity);
		
		if (!$itemInfoUpdate)
		{
			$problemfound = true;
			break;
		}
		
		$updateRentedQty = "UPDATE `item_info` SET `rented_qty` = `rented_qty` - '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
		$rentedQtyUpdate = mysqli_query($db, $updateRentedQty);
		
		if (!$rentedQtyUpdate)
		{
			$problemfound = true;
			break;
		}
		
		$updateCartInfoQuantity = "UPDATE `cart_info` SET `available_qty` = `available_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
		$cartInfoUpdate = mysqli_query($db, $updateCartInfoQuantity);
		
		if (!$cartInfoUpdate)
		{
			$problemfound = true;
			break;
		}
		
		$updateRentalInfoQuantity = "UPDATE `rental_info` SET `available_qty` = `available_qty` + '$RequestedQuantity' WHERE `item_name` = '$TheItemName';";
		$rentalInfoUpdate = mysqli_query($db, $updateRentalInfoQuantity);
		
		if (!$rentalInfoUpdate)
		{
			$problemfound = true;
			break;
		}
		
	}
	
	if($problemfound == true)
	{
		array_push($errors, "Server side error. Please Try Again.");
	}
	else
	{	
		$updatePossessionQuantity = "UPDATE `user_info` SET `possession_qty` = '0' WHERE `username` = '".$_SESSION['studentuserid']."';";
		mysqli_query($db, $updatePossessionQuantity);
	
		$dropColumn = "ALTER TABLE `rental_info` DROP `".$_SESSION['studentuserid']."`;";
		mysqli_query($db, $dropColumn);
		
	    	$_SESSION['success'] = "All Items Successfully Returned and Updated.";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/cartandpossessions.css">
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
	<li><a href="removals.php">Removals</a></li>
	<li><a href="inventory.php">Update Inventory</a></li>
	<li><a href="reports.php">Daily Reports</a></li>
	<li><a href="vieweditprofile.php">Profile Settings</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	<div id="rightcolumn">
	
	<form method="post" action="cartandpossessions.php">
	
		<div class="title">
		<h3>
		<?php echo "Student Account - View Cart & Possessions (2/3)"; ?>
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
		
		<div class="ex2" id="identity">
		<?php echo "".$_SESSION['studentfullname']."";?>
		</div>
		
		<div class="tab">
		<button class="tablinks" onclick="openCity(event, 'cart'); return false;" id="defaultOpen">Student Cart</button>
		<button class="tablinks" onclick="openCity(event, 'possessions'); return false;" id="studentPossessions">Student Possessions</button>
		</div>
		
		<div id="cart" class="tabdbtent">
		
			<div class="ex1" id="itemlist1">
			</div>
			
			<div class="input-group">
			<button class="approveBtn" name="approveall">Approve All Items</button>
			</div>
			
		</div>
		
		<div id="possessions" class="tabdbtent">
		
			<div class="ex1" id="itemlist2">
			</div>
			
			<div class="input-group">
			<button class="approveBtn" name="returnall">Return All Items (Completely)</button>
			</div>

		</div>
		
		<div class="input-group">
		<button class="approveBtn" name="exit">EXIT</button>
		</div>
		
		<?php
			$fetchQueryA = "SELECT `item_name`, `".$_SESSION['studentuserid']."` FROM `cart_info` WHERE `".$_SESSION['studentuserid']."` > '0';";
			$result = mysqli_query($db, $fetchQueryA);
			
			$itemindex = 0;
	
			?>
			<script type="text/javascript">
			var itemIndex = "<?php echo $itemindex; ?>";
			</script>
			<?php
			
			if(empty($_SESSION['holdCartItemQuantity']))
			{
				while($row = mysqli_fetch_array($result))
				{
					$itemname = $row[0];
					$quantity = $row[1];
					
					/*************************************************/
					
					if (in_array($itemname, $_SESSION['cartlist']))
					{
						//Do Nothing
					}
					else
					{
						//Add a product ID to our cart session variable.
						$_SESSION['cartlist'][] = $itemname;
					}
					
					?>
					<script type="text/javascript">
					
					var f = document.getElementById("itemlist1");
					
					var item = document.createElement("div");
					item.className = ('input-group');
					
					var label = document.createElement("label");
					var itemValue = "<?php echo $itemname; ?>";
					
					label.innerHTML = itemValue;
					
					var qty = document.createElement("input");
					qty.setAttribute('type',"number");
					qty.setAttribute('min',"1");
					qty.setAttribute('max',"20");
					qty.setAttribute('name',"cartitems[]");
					qty.setAttribute('value',"<?php echo $quantity ?>");
				
					var delBtn = document.createElement("button");
					delBtn.setAttribute('name',"delete");
					delBtn.setAttribute('value',itemValue);
					delBtn.className = ('delbtn');
					delBtn.innerHTML = "x";
					
					var singleapprovebtn = document.createElement("button");
					singleapprovebtn.setAttribute('name',"singleApprove");
					singleapprovebtn.setAttribute('value',itemValue);
					singleapprovebtn.className = ('singleapprovebtn');
					singleapprovebtn.innerHTML = "Approve";
					
					item.appendChild(label);
					item.appendChild(qty);
					item.appendChild(delBtn);
					item.appendChild(singleapprovebtn);
					
					document.getElementById("itemlist1").appendChild(item);
					
					itemIndex++;
					
					</script>
					<?php
				}
			}
			else
			{
				$myLength = sizeof($_SESSION['holdCartItemQuantity']);
				for ($x = 0; $x < $myLength; $x++)
				{
					$itemValue = $_SESSION['cartlist'][$x];
					
					?>
					<script type="text/javascript">
					
					var f = document.getElementById("itemlist1");
					
					var item = document.createElement("div");
					item.className = ('input-group');
					
					var label = document.createElement("label");
					var itemValue = "<?php echo $itemValue; ?>"
					label.innerHTML = itemValue;
					
					var qty = document.createElement("input");
					qty.setAttribute('type',"number");
					qty.setAttribute('min',"1");
					qty.setAttribute('max',"20");
					qty.setAttribute('name',"cartitems[]");
					qty.setAttribute('value',"<?php echo  $_SESSION['holdCartItemQuantity'][$x] ?>");
				
					var delBtn = document.createElement("button");
					delBtn.setAttribute('name',"delete");
					delBtn.setAttribute('value',itemValue);
					delBtn.className = ('delbtn');
					delBtn.innerHTML = "x";
					
					var singleapprovebtn = document.createElement("button");
					singleapprovebtn.setAttribute('name',"singleApprove");
					singleapprovebtn.setAttribute('value',itemValue);
					singleapprovebtn.className = ('singleapprovebtn');
					singleapprovebtn.innerHTML = "Approve";
					
					item.appendChild(label);
					item.appendChild(qty);
					item.appendChild(delBtn);
					item.appendChild(singleapprovebtn);
					
					document.getElementById("itemlist1").appendChild(item);
					
					itemIndex++;
					
					</script>
					<?php
				}
			}
			
			$fetchQueryB = "SELECT `item_name`, `".$_SESSION['studentuserid']."` FROM `rental_info` WHERE `".$_SESSION['studentuserid']."` > '0';";
			$result = mysqli_query($db, $fetchQueryB);
			
			$itemindex = 0;
	
			?>
			<script type="text/javascript">
			var itemIndex = "<?php echo $itemindex; ?>";
			</script>
			<?php
			
			if(empty($_SESSION['holdRentedItemQuantity']))
			{			
				while($row = mysqli_fetch_array($result))
				{
					$itemname = $row[0];
					$quantity = $row[1];
					
					if (in_array($itemname, $_SESSION['possessionlist']))
					{
						//Do Nothing
					}
					else
					{
						//Add a product ID to our cart session variable.
						$_SESSION['possessionlist'][] = $itemname;
					}
					
					?>
					<script type="text/javascript">
					
					var f = document.getElementById("itemlist2");
					var maxQty = "<?php echo $quantity ?>";
					
					var item = document.createElement("div");
					item.className = ('input-group');
					
					var label = document.createElement("label");
					var itemValue = "<?php echo $itemname; ?>";
					label.innerHTML = itemValue;
					
					var qty = document.createElement("input");
					qty.setAttribute('type',"number");
					qty.setAttribute('min',"1");
					qty.setAttribute('max', maxQty);
					qty.setAttribute('name',"rentalitems[]");
					qty.setAttribute('value',"<?php echo $quantity ?>");
					
					var singlereturnbtn = document.createElement("button");
					singlereturnbtn.setAttribute('name',"singleReturn");
					singlereturnbtn.setAttribute('value',itemValue);
					singlereturnbtn.className = ('singleapprovebtn');
					singlereturnbtn.innerHTML = "Return";
					
					item.appendChild(label);
					item.appendChild(qty);
					item.appendChild(singlereturnbtn);
					
					document.getElementById("itemlist2").appendChild(item);
					
					itemIndex++;
				
					</script>
					<?php
				}
			}
			else
			{
				$myLength = sizeof($_SESSION['holdRentedItemQuantity']);
				for ($x = 0; $x < $myLength; $x++)
				{
					$itemValue = $_SESSION['possessionlist'][$x];
					
					?>
					<script type="text/javascript">
					
					var f = document.getElementById("itemlist2");
					var maxQty = "<?php echo $quantity ?>";
					
					var item = document.createElement("div");
					item.className = ('input-group');
					
					var label = document.createElement("label");
					var itemValue = "<?php echo $itemname; ?>";
					label.innerHTML = itemValue;
					
					var qty = document.createElement("input");
					qty.setAttribute('type',"number");
					qty.setAttribute('min',"1");
					qty.setAttribute('max', maxQty);
					qty.setAttribute('name',"rentalitems[]");
					qty.setAttribute('value',"<?php echo $quantity ?>");
					
					var singlereturnbtn = document.createElement("button");
					singlereturnbtn.setAttribute('name',"singleReturn");
					singlereturnbtn.setAttribute('value',itemValue);
					singlereturnbtn.className = ('singleapprovebtn');
					singlereturnbtn.innerHTML = "Return";
					
					item.appendChild(label);
					item.appendChild(qty);
					item.appendChild(singlereturnbtn);
					
					document.getElementById("itemlist2").appendChild(item);
					
					itemIndex++;
					
					</script>
					<?php
				}
			}
		?>
	</form>
	
	</div>
	<div style="clear: both;"></div>
	</div>
	
	<script>
	function openCity(evt, cityName)
	{
	    var i, tabdbtent, tablinks;
	    tabdbtent = document.getElementsByClassName("tabdbtent");
	    for (i = 0; i < tabdbtent.length; i++)
	    {
	        tabdbtent[i].style.display = "none";
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

<?php

function clearForm()
{
	?>
	<script type="text/javascript">
	document.getElementById("itemlist1").innerHTML = "";
	</script>
	<?php
}

?>