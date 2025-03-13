		<?php 
			ini_set('display_errors', 'On');
			error_reporting(E_ALL);
		?>
		<?php
			$user=$_POST["username"]??"";
			$pass=$_POST["password"]??"";
			if ($user=="" ){
				header('Location: index.php');
				exit();
			}
			session_start();
			include 'db_connect.php';
			$query = "SELECT COUNT(id) AS esistente FROM utenti WHERE username = :user";
			try {
				$stmt = $con->prepare( $query );
				$stmt->bindParam(':user', $user, PDO::PARAM_STR);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($row["esistente"] == 1){
					session_destroy();
					header('Location: registerErrato.php');
					exit(0);
				}else{
					$insert = "INSERT INTO utenti(username, password, amministratore) VALUES(:user, :pass, 0);";
					try{
						$stmt = $con->prepare( $insert );
						$stmt->bindParam(':user', $user, PDO::PARAM_STR);
						$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
						$stmt->execute();
					}catch(PDOException $ex) {
						print($ex);
						exit();
					}
					$_SESSION["username"] = $user;
					header('Location: index.php');
					exit(0);
					
					
				}
			} catch(PDOException $ex) {
				print($ex);
				exit();
			}
		?>
