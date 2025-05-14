<html>
	<head>
		<link rel="stylesheet" href="stile.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>	
		<?php 
			ini_set('display_errors', 'On');
			error_reporting(E_ALL);
		?>
		<form action='loginDB.php' method='post' >
			<input placeholder='username' type='text' name='username' /><br>
			<input placeholder='password' type='password' name='password' /><br>
			<input type ='submit' value='ok'>
		</form>
		<a href="index.php">Home</a>
	</body>
</html>