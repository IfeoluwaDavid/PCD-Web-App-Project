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
if (isset($_POST['rem_student'])) 
{
	$currentTab = "defaultOpen";

	$selected_val = $_POST['studentvalue'];
	
	$checkPossessions = "SELECT `possession_qty` FROM `user_info` WHERE `username` = $selected_val";
	$feedback = mysqli_query($db, $checkPossessions);
	
	while($row = mysqli_fetch_array($feedback))
	{
		$possessionqty = $row[0];
	}
	
	//echo $possessionqty;
	
	if($possessionqty == 0)
	{
		$updateItemInfoQuery = "DELETE FROM `user_info` WHERE `username` = $selected_val";
		$result1 = mysqli_query($db, $updateItemInfoQuery);
		//echo $updateItemInfoQuery;
		
		if(!$result1)
		{
			array_push($errors, "Unable to delete Please Try Again.");
		}
		else
		{
			$_SESSION['success'] = "Successfully deleted ".$selected_val.".";
		}
	}
	else
	{
		array_push($errors, "The selected student still has some unreturned items in possession.");
	}
	
}

// REGISTER ADMIN
if (isset($_POST['rem_admin'])) 
{
	$currentTab = "AdminTab";

	$selected_val = $_POST['adminvalue'];

	$updateItemInfoQuery = "DELETE FROM `user_info` WHERE `username` = $selected_val";
	$result1 = mysqli_query($db, $updateItemInfoQuery);
	
	if(!$result1)
	{
		array_push($errors, "Unable to delete Please Try Again.");
	}
	else
	{
		$_SESSION['success'] = "Successfully deleted ".$selected_val.".";
	}
}

// REGISTER ADMIN
if (isset($_POST['rem_item'])) 
{
	$currentTab = "itemTab";
	
	$selected_val = $_POST['itemvalue'];

	$updateItemInfoQuery = "DELETE FROM `item_info` WHERE `item_name` = $selected_val";
	$result1 = mysqli_query($db, $updateItemInfoQuery);
	
	$updateCartInfoQuery = "DELETE FROM `cart_info` WHERE `item_name` = $selected_val";
	$result2 = mysqli_query($db, $updateCartInfoQuery);
	
	$updateRentalInfoQuery = "DELETE FROM `rental_info` WHERE `item_name` = $selected_val";
	$result3 = mysqli_query($db, $updateRentalInfoQuery);
	
	if(!$result1 || !$result2 || !$result3)
	{
		array_push($errors, "Unable to delete item. Please Try Again.");
	}
	else
	{
		$_SESSION['success'] = "Successfully deleted ".$selected_val.".";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/removals.css">
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
	<li><a class="active" href="removals.php">Removals</a></li>
	<li><a href="inventory.php">Update Inventory</a></li>
	<li><a href="reports.php">Daily Reports</a></li>
	<li><a href="vieweditprofile.php">Profile Settings</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
	<li><a href="logout.php">Log Out</a></li>
	</ul>
	</div>
	
	<div id="rightcolumn">
	
	<form method="post" action="removals.php">
	
		<div class="title">
		<h3>
		<?php echo "Removals"; ?>
		</h3>
		</div>
		
		<div class="tab">
		<button class="tablinks" onclick="openCity(event, 'Student'); return false;" id="defaultOpen">Remove Student</button>
		<button class="tablinks" onclick="openCity(event, 'Admin'); return false;" id="AdminTab">Remove Admin</button>
		<button class="tablinks" onclick="openCity(event, 'item'); return false;" id="itemTab">Remove Item</button>
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
		
		<select id="studentselect" name="studentvalue" size="17" class="select">
		</select>
		
		<div class="input-group">
		<button type="submit" class="btn" name="rem_student">Remove Student</button>
		</div>	
		
		</div>
		
		<div id="Admin" class="tabcontent">
		
		<select id="adminselect" name="adminvalue" size="17" class="select">
		</select>
		
		<div class="input-group">
		<button type="submit" class="btn" name="rem_admin">Remove Admin</button>
		</div>	
		
		</div>
		
		<div id="item" class="tabcontent">
		
		<select id="itemselect" name="itemvalue" size="17" class="select">
		</select>
		
		<div class="input-group">
		<button type="submit" class="btn" name="rem_item">Remove Item</button>
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
<?php

$fetchQuery= "SELECT * FROM `user_info` WHERE `admin_status` = '0';";
$result = mysqli_query($db, $fetchQuery);

while($row = mysqli_fetch_array($result))
{
	$username = $row[1];
	$first_name = $row[3];
	$last_name= $row[4];
	
	$student = "".$first_name." ".$last_name." (".$username.")";
	
	?>
	<script type="text/javascript">

	insertValue();
	
	function insertValue()
	{
		var x = document.getElementById("studentselect");
		var option = document.createElement("option");
		option.text = '<?php echo $student; ?>';
		option.setAttribute('value', "'<?php echo $username; ?>'");
		
		if(option.text)
		{	
			option.className = ('option');
			x.size = "9";
			x.add(option);
		}
		else
		{
			
		}	
	}
	</script>
	<?php
}

$fetchQuery= "SELECT * FROM `user_info` WHERE `admin_status` = '1';";
$result = mysqli_query($db, $fetchQuery);

while($row = mysqli_fetch_array($result))
{
	$username= $row[1];
	$first_name = $row[3];
	$last_name= $row[4];
	
	$admin = "".$first_name." ".$last_name." (".$username.")";
	
	?>
	<script type="text/javascript">

	insertValue();
	
	function insertValue()
	{
		var x = document.getElementById("adminselect");
		var option = document.createElement("option");
		option.text = '<?php echo $admin; ?>';
		option.setAttribute('value', "'<?php echo $username; ?>'");
		
		if(option.text)
		{	
			option.className = ('option');
			x.size = "9";
			x.add(option);
		}
		else
		{
			
		}	
	}
	</script>
	<?php
}

$fetchQuery = "SELECT * FROM `item_info`;";
$result = mysqli_query($db, $fetchQuery);

while($row = mysqli_fetch_array($result))
{
	$itemname = $row[1];
	$serialno = $row[2];
	
	?>
	<script type="text/javascript">

	insertValue();
	
	function insertValue()
	{
		var x = document.getElementById("itemselect");
		var option = document.createElement("option");
		option.text = '<?php echo $itemname; ?>';
		option.setAttribute('value', "'<?php echo $itemname; ?>'");
		
		if(option.text)
		{	
			option.className = ('option');
			x.size = "9";
			x.add(option);
		}
		else
		{
			
		}	
	}
	</script>
	<?php
}

?>