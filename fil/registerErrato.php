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
		<form action='doRegister.php' method='post' >
			Username<input type='text' name='username' /><br>
			Password <input type='password' name='password' /><br>
			Mail<input type='text' name='mail' /><br>
			<input type ='submit' value='ok'>
		</form>
		<a href="index.php">Home</a>
		<h3>Utente già esistente </h3>
	</body>
</html>