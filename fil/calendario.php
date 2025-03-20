<?php
//Array associativo dei mesi dell'anno
$mese = array(
	"01" => "Gennaio",
	"02" => "Febbraio",
	"03" => "Marzo",
	"04" => "Aprile",
	"05" => "Maggio",
	"06" => "Giugno",
	"07" => "Luglio",
	"08" => "Agosto",
	"09" => "Settembre",
	"10" => "Ottobre",
	"11" => "Novembre",
	"12" => "Dicembre"
);
$t=time(); //Data corrente
$day = date("d",$t); //Ricava giorno
$month = date("m",$t); //Ricava mese
$year = date("Y",$t); //Ricava anno

print($day . $month . $year . "\n");

print(cal_days_in_month(CAL_GREGORIAN, $month, $year));

print("<h1>" . $mese[$month] . " " . $year . "</h1>");

?>