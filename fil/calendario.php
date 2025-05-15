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
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Calendario Prenotazioni</title>
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="calendar-container">
            <div class="calendar-header">
                <div class="month-nav">
                    <?php if ($py<$oggiY || ($py==$oggiY && $pm<$oggiM)): ?>
                        <button class="nav-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <?php else: ?>
                        <a href="?month=<?= $pm ?>&year=<?= $py ?>" class="nav-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <h2 class="month-title"><?= $mese[$m] ?> <?= $y ?></h2>
                    
                    <a href="?month=<?= $nm ?>&year=<?= $ny ?>" class="nav-btn">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            
            <table class="calendar-table">
                <tr>
                    <?php foreach($giorno as $d): ?>
                        <th><?= $d ?></th>
                    <?php endforeach; ?>
                </tr>
                
                <?php
                $cal = calendario($m,$y);
                $start = false;
                $dd = 1;
                $tot = cal_days_in_month(CAL_GREGORIAN,$m,$y);
                
                for($r=0; $r<6; $r++): ?>
                    <tr>
                        <?php for($c=0; $c<7; $c++):
                            if(!$start && $cal['dayname']!=$giornoEn[$c]): ?>
                                <td></td>
                            <?php else:
                                $start = true;
                                if($dd <= $tot):
                                    $isPast = ($y<$oggiY) ||
                                              ($y==$oggiY && $m<$oggiM) ||
                                              ($y==$oggiY && $m==$oggiM && $dd<$oggiG);
                                    $isToday = ($y==$oggiY && $m==$oggiM && $dd==$oggiG);
                                ?>
                                <td>
                                    <form action='scegliOra.php' method='post'>
                                        <input type='hidden' name='day' value='<?= $dd ?>'>
                                        <input type='hidden' name='month' value='<?= $m ?>'>
                                        <input type='hidden' name='year' value='<?= $y ?>'>
                                        <button class="day-btn" <?= $isPast ? 'disabled' : '' ?>>
                                            <?php if($isToday): ?>
                                                <span class="today"><?= $dd ?></span>
                                            <?php else: ?>
                                                <?= $dd ?>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                                <?php $dd++; ?>
                            <?php else: ?>
                                <td></td>
                            <?php endif;
                        endif;
                        endfor; ?>
                    </tr>
                <?php endfor; ?>
            </table>
            
            <div class="footer-links">
                <a href="index.php" class="home-link">
                    <i class="fas fa-home"></i> Torna alla Home
                </a>
            </div>
        </div>
    </div>
    
    <footer>
        <p>Email: <a href="mailto:vrtf03000v@istruzione.it">vrtf03000v@istruzione.it</a> | Tel: <a href="tel:+390458101428">+39 045 810 1428</a></p>
        <p>&copy; 2025 Prenotazioni Aule.</p>
        <p>&copy; Realizzato da: Corrazzini Riccardo Samer, Palumbo Antonio e Tezza Pietro</p>
    </footer>
</body>
</html>