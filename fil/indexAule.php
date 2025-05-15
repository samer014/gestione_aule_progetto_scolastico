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
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Aule</title>
    <link rel="stylesheet" href="../sty/InterfaceIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>PRENOTAZIONI AULE</h1>
    </header>
    
    <div class="main-container">
        <div class="classrooms-container">
            <h2 class="page-title">
                <i class="fas fa-building"></i> Gestione Aule
                <span class="admin-badge">Amministratore</span>
            </h2>
            
            <?php
            // Recupera l'elenco delle aule
            $query = "SELECT nome FROM aule ORDER BY nome;";
            try {
                $stmt = $con->prepare($query);
                $stmt->execute();
                $aule = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch(PDOException $ex) {
                die("<div class='error-message'>Errore nel recupero delle aule: ".htmlspecialchars($ex->getMessage())."</div>");
            }
            ?>
            
            <!-- Sezione Rimuovi Aula -->
            <div class="action-form-Aule">
                <h3 class="section-title"><i class="fas fa-trash-alt"></i> Rimuovi Aula</h3>
                <form action='rimuoviAula.php' method='post'>
                    <div class="form-group">
                        <label for="aula">Seleziona aula:</label>
                        <select name="aula" id="aula" class="form-select" required>
                            <option value="" selected disabled>Seleziona un'aula</option>
                            <?php foreach ($aule as $aula): ?>
                                <option value="<?= htmlspecialchars($aula) ?>"><?= htmlspecialchars($aula) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn remove">
                        <i class="fas fa-trash-alt"></i> Rimuovi
                    </button>
                </form>
            </div>
            
            <!-- Sezione Aggiungi Aula -->
            <div class="action-form-Aule">
                <h3 class="section-title"><i class="fas fa-plus-circle"></i> Aggiungi Aula</h3>
                <form action='aggiungiAula.php' method='post'>
                    <div class="form-group">
                        <label for="new_aula">Nome aula:</label>
                        <input type="text" id="new_aula" name="aula" class="form-input small" 
                               placeholder="4 caratteri" maxlength="4" required>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-plus"></i> Aggiungi
                    </button>
                </form>
            </div>
            
            <!-- Sezione Gestione Note -->
            <div class="action-form-Aule">
                <h3 class="section-title"><i class="fas fa-sticky-note"></i> Gestione Note</h3>
                <form action='inserisciNoteAula.php' method='post'>
                    <div class="form-group">
                        <label for="note_aula">Seleziona aula:</label>
                        <select name="aula" id="note_aula" class="form-select" required>
                            <option value="" selected disabled>Seleziona un'aula</option>
                            <?php foreach ($aule as $aula): ?>
                                <option value="<?= htmlspecialchars($aula) ?>"><?= htmlspecialchars($aula) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Note:</label>
                        <input type="text" id="note" name="note" class="form-input" 
                               placeholder="Inserisci note" required>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Salva Note
                    </button>
                </form>
            </div>
            
            <div style="text-align: center;">
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