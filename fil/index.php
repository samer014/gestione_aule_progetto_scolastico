<html>
    <head>
        <link rel="stylesheet" href="../sty/InterfaceIndex.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <title>Prenotazioni Aule</title>
    </head>
    <body>
        <header>
            <h1>PRENOTAZIONI AULE</h1>
        </header>
        
        <div class="main-container">
            <?php
                session_start();
                $user = $_SESSION["username"] ?? "";

                ini_set('display_errors', 'On');
                error_reporting(E_ALL);
                include "./db_connect.php";

                if ($user == "") { 
                    echo '<div class="login-container">
                            <a href="login.php" class="login-btn">
                                <i class="fas fa-sign-in-alt"></i>
                                Accedi al Sistema
                            </a>
                          </div>';
                } else {
                    $query = "SELECT amministratore FROM utenti WHERE username = :user";
                    try {
                        $stmt = $con->prepare($query);
                        $stmt->bindParam(':user', $user, PDO::PARAM_STR);
                        $stmt->execute();
                        
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $isAdmin = ($row["amministratore"] == 1);
                        
                        echo '<div class="user-card ' . ($isAdmin ? 'admin-menu' : '') . '">';
                        echo '<h3>';
                        echo $isAdmin 
                            ? '<i class="fas fa-shield-alt"></i> Benvenuto Amministratore'
                            : '<i class="fas fa-user"></i> Benvenuto ' . htmlspecialchars($user);
                        echo '</h3>';
                        
                        echo '<div class="menu-grid">';
                        
                        if ($isAdmin) {
                            echo '
                                <a href="creaUtente.php" class="menu-item">
                                    <i class="fas fa-user-plus"></i>
                                    Crea Utente
                                </a>
                                <a href="indexAule.php" class="menu-item">
                                    <i class="fas fa-building"></i>
                                    Gestione Aule
                                </a>
                                <a href="orari.php" class="menu-item">
                                    <i class="fas fa-clock"></i>
                                    Modifica Orari
                                </a>
                                <a href="richieste.php" class="menu-item">
                                    <i class="fas fa-inbox"></i>
                                    Gestione Richieste
                                </a>';
                        }
                        
                        echo '
                            <a href="calendario.php" class="menu-item">
                                <i class="fas fa-calendar-check"></i>
                                Prenota Aule
                            </a>
                            <a href="prenotazioni.php" class="menu-item">
                                <i class="fas fa-list"></i>
                                Le Mie Prenotazioni
                            </a>
                            <a href="logout.php" class="menu-item logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                Esci
                            </a>';
                        
                        echo '</div></div>';

                    } catch(PDOException $ex) {
                        print($ex);
                        exit();
                    }
                }
            ?>
        </div>
        
        <footer>
            <p>Email: <a href="mailto:vrtf03000v@istruzione.it">vrtf03000v@istruzione.it</a> | Tel: <a href="tel:+390458101428">+39 045 810 1428</a></p>
            <p>&copy; 2025 Prenotazioni Aule.</p>
            <p>&copy; Realizzato da: Corrazzini Riccardo Samer, Palumbo Antonio e Tezza Pietro</p>
        </footer>
    </body>
</html>