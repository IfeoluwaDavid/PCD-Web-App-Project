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
}

// REGISTER ADMIN
if (isset($_POST['reg_admin'])) 
{
	$currentTab = "studentPossessions";
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
	<li><a href="#about">Removals</a></li>
	<li><a href="#about">Update Inventory</a></li>
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
		
		<div class="ex2" id="identity">
		<?php echo "".$_SESSION['studentfullname']."";?>
		</div>
		
		<div class="tab">
		<button class="tablinks" onclick="openCity(event, 'cart'); return false;" id="defaultOpen">Student Cart</button>
		<button class="tablinks" onclick="openCity(event, 'possessions'); return false;" id="studentPossessions">Student Possessions</button>
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
		
		<div id="cart" class="tabcontent">
		
			<div class="ex1" id="itemlist1">
			</div>
			
			<div class="input-group">
			<button class="approveBtn" name="approve">Approve Items</button>
			</div>
			
			<div class="input-group">
			<button class="approveBtn" name="back">Back</button>
			</div>
			
		</div>
		
		<div id="possessions" class="tabcontent">
		
			<div class="ex1" id="itemlist2">
			</div>
			
			<div class="input-group">
			<button class="approveBtn" name="approve">Return All Items</button>
			</div>
			
			<div class="input-group">
			<button class="approveBtn" name="Cancel">Back</button>
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

$username = $_SESSION['studentusername'];

$fetchQueryA = "SELECT `item_name`, `$username` FROM `cart_info` WHERE `$username` > '0';";
$result = mysqli_query($db, $fetchQueryA);

while($row = mysqli_fetch_array($result))
{
	$itemname = $row[0];
	$quantity = $row[1];
	
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
	qty.setAttribute('name',"items[]");
	qty.setAttribute('value',"<?php echo $quantity ?>");

	var delBtn = document.createElement("button");
	delBtn.setAttribute('name',"delete");
	delBtn.setAttribute('value',itemValue);
	delBtn.className = ('delbtn');
	delBtn.innerHTML = "x";
	
	item.appendChild(label);
	item.appendChild(qty);
	item.appendChild(delBtn);
	
	document.getElementById("itemlist1").appendChild(item);
	
	itemIndex++;

	</script>
	<?php
}

$fetchQueryB = "SELECT `item_name`, `$username` FROM `rental_info` WHERE `$username` > '0';";
$result = mysqli_query($db, $fetchQueryB);

while($row = mysqli_fetch_array($result))
{
	$itemname = $row[0];
	$quantity = $row[1];
	
	?>
	<script type="text/javascript">
	
	var f = document.getElementById("itemlist2");
	
	var item = document.createElement("div");
	item.className = ('input-group');
	
	var label = document.createElement("label");
	var itemValue = "<?php echo $itemname; ?>";
	label.innerHTML = itemValue;
	
	var qty = document.createElement("input");
	qty.setAttribute('type',"number");
	qty.setAttribute('min',"1");
	qty.setAttribute('max',"20");
	qty.setAttribute('name',"items[]");
	qty.setAttribute('value',"<?php echo $quantity ?>");

	var delBtn = document.createElement("button");
	delBtn.setAttribute('name',"delete");
	delBtn.setAttribute('value',itemValue);
	delBtn.className = ('delbtn');
	delBtn.innerHTML = "x";
	
	item.appendChild(label);
	item.appendChild(qty);
	item.appendChild(delBtn);
	
	document.getElementById("itemlist2").appendChild(item);
	
	itemIndex++;

	</script>
	<?php
}
	
function clearForm()
{
	?>
	<script type="text/javascript">
	document.getElementById("itemlist1").innerHTML = "";
	</script>
	<?php
}

//clearForm();
showItems();

?>