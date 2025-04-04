<h1 style="text-align: center;">Gestione Aule</h1>
<?php
    session_start();
    $user = $_SESSION["username"] ?? "";
    if ($user != "adm") {
        header('Location: index.php');
        exit();
    }
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    include "./db_connect.php";
?>

<?php
    $query = "SELECT nome FROM aule ORDER BY nome;";
    try {
        $stmt = $con->prepare($query);
        $stmt->execute();
    } catch(PDOException $ex) {
        print("Errore !" . $ex->getMessage());
        exit;
    }

    print("<h3>Rimuovi aula</h3>");
    print("<form action='doRimuoviAula.php' method='post'>");
    print("<table>");
    print("<select name='aula'>");
    print("<option value='none' selected disabled hidden>Seleziona un'aula</option>");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print("<option value=". $row['nome'].">".$row['nome']."</option>");
    }
    print("</select>");
    print("<button type='submit'>Rimuovi</button>");
    print("</form>");
    print("</table><br><br>");



	print("<h3>Aggiungi aula</h3>");
	print("<form action='doAggiungiAula.php' method='post'>");
	print("<input placeholder='4 CARATTERI' maxlength='4' type='text' name='aula'>");
    print("<table>");
	print("<button type='submit'>Aggiungi</button>");
	print("</form>");
    print("</table><br><br>");
	
	
	$query = "SELECT nome FROM aule ORDER BY nome;";
    try {
        $stmt = $con->prepare($query);
        $stmt->execute();
    } catch(PDOException $ex) {
        print("Errore !" . $ex->getMessage());
        exit;
    }
	
	
	print("<h3>Inserisci note</h3>");
	print("<form action='doInserisciNote.php' method='post'>");
	print("<input placeholder='note' type='text' name='note'>");
    print("<table>");
	print("<select name='aula'>");
    print("<option value='none' selected disabled hidden>Seleziona un'aula</option>");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print("<option value=". $row['nome'].">".$row['nome']."</option>");
    }
    print("</select>");
	print("<button type='submit'>Aggiungi</button>");
	print("</form>");
    print("</table><br><br>");
	//inserisci e cancella note di un aula
	//cambia nome aula
	print("<a href='index.php'>Home</a>");
?>
