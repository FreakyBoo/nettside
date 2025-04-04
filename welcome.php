<?php
session_start(); // Start sesjonen for å få tilgang til brukerinformasjon

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['kunde_id'])) {
    header("Location: login.php"); // Hvis ikke logget inn, omdiriger til login-siden
    exit();
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="nettside.css">
    <title>Velkommen</title>
</head>
<body>
    <h1>Velkommen, <?php echo $_SESSION['fornavn']; ?>!</h1>
    <p>Du er nå logget inn på nettbutikken.</p>
    <a href="order.php">Bestill produkter</a> | 
    <a href="index.php">Logg ut</a>

    <div class="bilde-container">
        <img  src="">
        <img  src="">
        <img  src=" >
    </div>


</body>
</html>
