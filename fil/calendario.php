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
$month = date("m",$t); //Ricava mese corrente
$month = (int)$month;
$year = date("Y",$t); //Ricava anno corrente

print("<button onclick=" . "\"" . $month++ "\"" . "> -> </button>");
$totGiorni = cal_days_in_month(CAL_GREGORIAN, $month, $year);
print($month);
print("<h1>" . $mese[$month] . " " . $year . "</h1> \n"); // stampa il mese e anno corrente
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
				print("<td>" . $giorniM . "</td>\n");
				$giorniM++;
			}
		}
	}
	print("<tr>\n");
}
print("</table>");
?>