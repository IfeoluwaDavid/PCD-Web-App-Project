<?php

//Connect to Database
include 'init.php';
//unset($_SESSION['scanneditemlist']);

if (!isset($_SESSION['user_id']))
{
	array_push($errors, "Please log in first");
	header('location: login.php');
}

if (!isset($_SESSION['studentuserid']))
{
	array_push($errors, "Student must be verified first");
	header('location: mainoperationsA.php');
}

if (isset($_POST['next']))
{		
	if (empty($_SESSION['scanneditemlist']))
	{
		array_push($errors, "No items have been entered yet!");
	}
	else
	{
		header("Location: mainoperationsC.php");
	}
}

if (isset($_POST['back']))
{
	unset($_SESSION['studentuserid']);
	unset($_SESSION['studentfullname']);
	unset($_SESSION['scanneditemlist']);
	header("Location: mainoperationsA.php");
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="CSS/mainoperationsB.css">
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
	<form method="post" action="mainoperationsB.php">
	
		<div class="title">
		<h3>
		<?php echo "Main Operations - Scan Items (2/3)"; ?>
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
		<label>Scan Barcode:</label>
		<input type="text" id="barcode" name="barcode" value=""> 
		</div>
		
		<div class="input-group">
		<button class="btn" name="insert">Enter</button>
		</div>
		
		<div class="input-group">
		<button class="btn" name="delete">Delete Item</button>
		</div>
		
		<div class="input-group">
		<button class="btn" name="next">Next</button>
		</div>
		
		<div class="input-group">
		<button class="btn" name="back">Cancel</button>
		</div>
		
		<select id="myselect" name="color" size="17" class="select">
		</select>
		
	</form>
	
	</div>
	<div style="clear: both;"></div>
	</div>
		
</body>
</html>

<?php

showList();

if (isset($_POST['insert'])) 
{
	$barcode = mysqli_real_escape_string($db, $_POST['barcode']);
	
	$query = "SELECT * FROM item_info WHERE serial_no = '$barcode'";
	$results = mysqli_query($db, $query);
	
	if (mysqli_num_rows($results) == 1)
	{
		$row = mysqli_fetch_array($results);
		$itemname = $row[1];
		
		if(!isset($_SESSION['scanneditemlist']))
		{
			//If it doesn't, create an empty array.
			$_SESSION['scanneditemlist'] = array();
		}
		else
		{
			//Do Nothing
		}
		
		if (in_array($itemname, $_SESSION['scanneditemlist']))
		{
			//Do Nothing
		}
		else
		{
			//Add a product ID to our cart session variable.
			$_SESSION['scanneditemlist'][] = $itemname;
		}
		//echo "<script></script>";
		clearbox();
		showList();
	}
	else
	{
		$_SESSION['error'] = 'Failed to find item';
		clearbox();
		showList();
	}
}
else
{
	//showList();
}

if (isset($_POST['delete'])) 
{
	//$barcode = mysqli_real_escape_string($db, $_POST['barcode']);
	
	if (isset($_POST['color'])) 
	{
		$selected_val = $_POST['color'];  // Storing Selected Value In Variable
		
		if (in_array($selected_val, $_SESSION['scanneditemlist']))
		{
			$sessionIndex = array_search($selected_val, $_SESSION['scanneditemlist']);
			array_splice($_SESSION['scanneditemlist'], $sessionIndex, 1);
				
		}
		else
		{
			//echo "Match not found";
		}
		clearbox();
		showList();
	}
	else
	{
		clearbox();
		showList();
	}	
}
else
{
	//showList();
}

function showList()
{
	if(empty($_SESSION['scanneditemlist']))
	{
		?>
		<script type="text/javascript">

		setLength();
		
		function setLength()
		{
			var x = document.getElementById("myselect");
			x.size = "17";
			document.getElementById("barcode").focus();
		}
		</script>
		<?php
	}
	else
	{
		foreach ($_SESSION['scanneditemlist'] as $key => $value)
		{
			?>
			<script type="text/javascript">
	
			insertValue();
			
			function insertValue()
			{
				var x = document.getElementById("myselect");
				var option = document.createElement("option");
				option.text = '<?php echo $value ?>';
				
				if(option.text)
				{	
					option.className = ('option');
					x.size = "9";
					x.add(option);
					document.getElementById("barcode").value = "";
					document.getElementById("barcode").focus();
				}
				else
				{
					
				}	
			}
			</script>
			<?php
		}
	}
}

function clearbox()
{
	?>
	<script type="text/javascript">
	
	var x = document.getElementById("myselect");
	clearList();
	function clearList()
	{
		x.options.length = 0;
	}
	
	</script>
	<?php
}

?>