<?php
session_start(); // Start en sesjon for å huske brukeren

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Sjekker om forespørselen er gjort med POST-metoden
    // Koble til databasen
    $host = "localhost";
    $user = "julian";
    $pass = "Julian2007!"; 
    $db = "klesbutikk";
    
    $conn = new mysqli($host, $user, $pass, $db);

    // Sjekk tilkobling
    if ($conn->connect_error) {
        die("Tilkoblingsfeil: " . $conn->connect_error);
    }

    // Hent data fra skjemaet
    $epost = $_POST['epost'];
    $passord = $_POST['passord'];

    // Sjekk om brukerens epost finnes i databasen
    $sql = "SELECT * FROM kunder WHERE epost = '$epost'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { // Sjekker om resultatet inneholder én eller flere rader
        // Bruker finnes, sjekk passordet
        $row = $result->fetch_assoc();
        if (password_verify($passord, $row['passord'])) {
            // Passordet er riktig, lagre brukerens info i sesjonen
            $_SESSION['kunde_id'] = $row['kunde_id'];
            $_SESSION['fornavn'] = $row['fornavn'];
            header("Location: welcome.php"); // Omviderer til velkomstside
            exit();
        } else {
            echo "Feil epost eller passord.";
        }
    } else {
        echo "Feil epost eller passord.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="nettside.css">
    <title>Logg inn</title>
</head>
<body>
    <h1>Logg inn</h1>
    <form action="login.php" method="POST">
        <input type="email" name="epost" placeholder="Epost" required  tabindex="1">
        <input type="password" name="passord" placeholder="passord" required  tabindex="2">
        <button type="submit"  tabindex="3">Logg inn</button>
    </form>
</body>
</html>
