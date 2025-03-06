<html>
	<?php
		session_start();
		session_destroy();
		$loc=$_SERVER['HTTP_REFERER']??"index.php";
		header('Location: ' . $loc);
		exit();
	?>
</html>