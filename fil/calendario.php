<?php
function calendario($m,$y){
  return cal_from_jd(
    unixtojd(mktime(0,0,0,$m,1,$y)),
    CAL_GREGORIAN
  );
}

// Nomi
$mese = [1=>"Gennaio",2=>"Febbraio",3=>"Marzo",4=>"Aprile",
         5=>"Maggio",6=>"Giugno",7=>"Luglio",8=>"Agosto",
         9=>"Settembre",10=>"Ottobre",11=>"Novembre",12=>"Dicembre"];
$giorno = ["Lunedì","Martedì","Mercoledì","Giovedì","Venerdì","Sabato","Domenica"];
$giornoEn = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];

// Data odierna
$oggiG = date("j");
$oggiM = date("n");
$oggiY = date("Y");

// Mese/Anno correnti o da GET
$m = isset($_GET['month'])?(int)$_GET['month']:$oggiM;
$y = isset($_GET['year'])?(int)$_GET['year']:$oggiY;

// Prev/Next
$pm = ($m==1?12:$m-1);
$py = ($m==1?$y-1:$y);
$nm = ($m==12?1:$m+1);
$ny = ($m==12?$y+1:$y);

// Pulsanti mese
if ($py<$oggiY || ($py==$oggiY && $pm<$oggiM)) {
  echo "<button disabled><-</button>";
} else {
  echo "<a href='?month=$pm&year=$py'><button><-</button></a>";
}
echo "<h1>{$mese[$m]} $y</h1>";
echo "<a href='?month=$nm&year=$ny'><button>-></button></a>";

// Tabella
echo "<table border=1><tr>";
foreach($giorno as $d) echo "<th>$d</th>";
echo "</tr>";

$cal = calendario($m,$y);
$start = false;
$dd=1;
$tot = cal_days_in_month(CAL_GREGORIAN,$m,$y);
for($r=0;$r<6;$r++){
  echo "<tr>";
  for($c=0;$c<7;$c++){
    if(!$start && $cal['dayname']!=$giornoEn[$c]){
      echo "<td></td>";
    } else {
      $start = true;
      if($dd<=$tot){
        // data del bottone
        $isPast = ($y<$oggiY) ||
                  ($y==$oggiY && $m<$oggiM) ||
                  ($y==$oggiY && $m==$oggiM && $dd<$oggiG);

        $attr = $isPast ? "disabled" : "";
        echo "<td>
                <form action='scegliOra.php' method='get'>
                  <input type='hidden' name='day'   value='$dd'>
                  <input type='hidden' name='month' value='$m'>
                  <input type='hidden' name='year'  value='$y'>
                  <button $attr type='submit'>$dd</button>
                </form>
              </td>";
        $dd++;
      } else {
        echo "<td></td>";
      }
    }
  }
  echo "</tr>";
}
echo "</table>";
print("<a href=\"index.php\">Home</a><br>");
?>
