<html>
    <head>
        <link rel="stylesheet" href="../sty/InterfaceIndex.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <title>Accesso - Prenotazioni Aule</title>
    </head>
    <body>
        <header>
            <h1>PRENOTAZIONI AULE</h1>
        </header>
        
        <div class="login-container">
            <div class="login-card">
                <h2 class="login-title">Accedi al tuo account</h2>
                
                <form action='loginDB.php' method='post'>
                    <input type="text" name='username' placeholder="Username" class="login-field" required>
                    <input type="password" name='password' placeholder="Password" class="login-field" required>
                    
                    <button type="submit" value='ok' class="login-btn">
                        <i class="fas fa-check"></i> Accedi
                    </button>
                </form>
				<a href="index.php" class="home-link">
					<i class="fas fa-arrow-left"></i> Torna alla Home
				</a>
            </div>
        </div>
        
        <footer>
            <p>Email: <a href="mailto:vrtf03000v@istruzione.it">vrtf03000v@istruzione.it</a> | Tel: <a href="tel:+390458101428">+39 045 810 1428</a></p>
            <p>&copy; 2025 Prenotazioni Aule.</p>
            <p>&copy; Realizzato da: Corrazzini Riccardo Samer, Palumbo Antonio e Tezza Pietro</p>
        </footer>
    </body>
</html>

