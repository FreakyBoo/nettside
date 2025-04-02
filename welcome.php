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
        <img  src="https://cdn.alloallo.media/catalog/product/apple/iphone/iphone-13/iphone-13-pink.jpg">
        <img  src="https://no.jbl.com/dw/image/v2/BFND_PRD/on/demandware.static/-/Sites-masterCatalog_Harman/default/dw53533ee8/JBL_BOOMBOX3_WIFI_HERO_37919_x4.png">
        <img  src="https://playtech.co.nz/cdn/shop/files/105423-01.png?v=1720744094" >
    </div>


</body>
</html>
