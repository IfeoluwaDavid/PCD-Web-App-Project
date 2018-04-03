<?php

//Connect to Database
include 'init.php';

// LOGIN USER
if (isset($_POST['login_user'])) 
{
	$username = mysqli_real_escape_string($db, $_POST['username']);
	$password = mysqli_real_escape_string($db, $_POST['password']);

	if (count($errors) == 0)
	{
		$password = md5($password);
		$query = "SELECT * FROM user_info WHERE username = '$username' AND password = '$password'";
		$results = mysqli_query($db, $query);

		if (mysqli_num_rows($results) == 1)
		{
			$row = mysqli_fetch_array($results);
			$_SESSION['user_id'] = $row[0];
			$_SESSION['success'] = "Hi! You are now logged in";
			header("Location: mainoperationsA.php");
		}
		else
		{
			array_push($errors, "Incorrect username/password combination");
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>PartsCribber Login</title>
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
</head>
<body>

	<div class="header">
	<h2>PartsCribber: Login</h2>
	</div>
	
	<form method="post" action="login.php" autocomplete="on">

		<?php 
		
		include('errors.php'); 
		
		?>

		<div class="input-group">
		<label>Username:</label>
		<input type="text" name="username" required>
		</div>
		
		<div class="input-group">
		<label>Password:</label>
		<input type="password" name="password" required>
		</div>
		
		<div class="input-group">
		<button type="submit" class="btn" name="login_user">Login</button>
		</div>
		
	</form>

</body>
</html>