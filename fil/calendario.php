<?php
function calendario($month, $year){
	$today = unixtojd(mktime(0, 0, 0, $month, 1, $year)); // imposto funzione che richiama il calendario(mese, giorno, anno) restituendomi il giorno della settimana
	$cal = cal_from_jd($today, CAL_GREGORIAN);
	return $cal;
}
?>

<?php
//Array associativo dei mesi dell'anno
$mese = array(
	1 => "Gennaio",
	2 => "Febbraio",
	3 => "Marzo",
	4 => "Aprile",
	5 => "Maggio",
	6 => "Giugno",
	7 => "Luglio",
	8 => "Agosto",
	9 => "Settembre",
	10 => "Ottobre",
	11 => "Novembre",
	12 => "Dicembre"
);

//Array associativo dei giorni della settimana
$giorno = array(
	0 => "Lunedì",
	1 => "Martedì",
	2 => "Mercoledì",
	3 => "Giovedì",
	4 => "Venerdì",
	5 => "Sabato",
	6 => "Domenica"
);

//Array associativo dei giorni della settimana in inglese
$giornoIngl = array(
	0 => "Monday",
	1 => "Tuesday",
	2 => "Wednesday",
	3 => "Thursday",
	4 => "Friday",
	5 => "Saturday",
	6 => "Sunday"
);

$t=time(); //Data corrente
$day = date("d",$t); //Ricava giorno corrente
$month = isset($_GET['month']) ? (int)$_GET['month'] : date("n", $t); // Ricava mese corrente, ma lo prende da $_GET se presente con isset si guarda se esiste month nel get
$year = isset($_GET['year']) ? (int)$_GET['year'] : date("Y", $t); // Ricava l'anno corrente

// Mostra il pulsante per il mese successivo
$nextMonth = $month == 12 ? 1 : $month + 1; // nextMonth se è dicembre(12) sarà Gennaio(1) sennò sarà il prossimo incremetando di 1
$nextYear = $month == 12 ? $year + 1 : $year; // nextYear se il mese è dicembre(12), l'anno sarà +1 sennò resta invariato
$prevMonth = $month == 1 ? 12 : $month - 1; //prevMonth se è gennaio(1) allora diventa dicembre(12) sennò -1
$prevYear = $month == 1 ? $year - 1 : $year; // prevYear se il mese è gennaio(1), l'anno sarà -1 sennò resta invariato

$totGiorni = cal_days_in_month(CAL_GREGORIAN, $month, $year);


print("<a href='?month=$prevMonth&year=$prevYear'><button><-</button></a>"); // Pulsante mese precedente
print("<a href='?month=$nextMonth&year=$nextYear'><button>-></button></a>"); // Pulsante mese successivo
print("<h1>" . $mese[$month] . " " . $year . "</h1> \n"); // stampa il mese e anno corrente
//Creazione tabella
print("<table> \n");
	print("<tr> \n");
		for($i=0; $i<7; $i++){ //ciclo for che aggiunge i giorni della settimana
			print("<td>" . $giorno[$i] . "</td> \n");
		}
	print("</tr>\n");
	$cal = calendario($month,$year);
	$trovaGSett = false;
	$giorniM = 1;
	for($r=0; $r<6; $r++){
		print("<tr>\n");
			for($c=0; $c<7; $c++){
				if($cal["dayname"]!= $giornoIngl[$c] && $trovaGSett == false){
					print("<td></td>\n");
				}else{
					$trovaGSett = true;
					if($giorniM <= $totGiorni){
						// Passa giorno, mese e anno a scegliOra.php
						print("<form action='scegliOra.php' method='GET'>");
							print("<td><button id='giorno' type='submit' name='day' value='$giorniM'>$giorniM</button></td>\n");
							// Aggiungi i parametri mese e anno
							print("<input type='hidden' name='month' value='$month'>");
							print("<input type='hidden' name='year' value='$year'>");
						print("</form>");
						$giorniM++;
					}
				}
			}
		print("<tr>\n");
	}
print("</table>");

?>