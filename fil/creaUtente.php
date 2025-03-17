<h1 style="text-align: center;">Creazione utente</h1>
<?php
	session_start();
	$user=$_SESSION["username"]??"";
	if ($user==""){
		header('Location: login.php');
		exit();
	}
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	include "./db_connect.php";
?>

<?php // inserisci l'username e password del nuovo utente
	print("<tr>".
			"<td>Inserisci nome e password</td>\n".
			"<form action='doCreaUtente.php' method='post'>".
				"<td><input name='newUser'></td>\n".
				"<td><input name='newPass'></td>\n".
				"<td>".
					"<a href='index.php'><button/>CREA</button></a>".
				"</td>".
			"</form>".
				"<br>".
		"</tr>\n"
	);
	print("<a href='index.php'> Home </a>");
?>