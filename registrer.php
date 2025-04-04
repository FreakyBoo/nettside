<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    $fornavn = $_POST['fornavn'];
    $etternavn = $_POST['etternavn'];
    $epost = $_POST['epost'];
    $telefon = $_POST['telefon'];
    $adresse = $_POST['adresse'];
    $fodselsdato = $_POST['fodselsdato'];
    $passord = password_hash($_POST['passord'], PASSWORD_DEFAULT);// Hasher passordet

        
    $sql = "SELECT * FROM kunder WHERE epost = '$epost'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Epost finnes allerede!";
    } else {
        $sql = "INSERT INTO kunder (fornavn, etternavn, epost, telefon, adresse, fodselsdato, passord) VALUES ('$fornavn', '$etternavn', '$epost', '$telefon', '$adresse', '$fodselsdato', '$passord')";
    
        if ($conn->query($sql) === TRUE) {
            echo "Bruker registrert! <a href='login.php'>Logg inn her</a>";
        } else {
            echo "Feil: " . $conn->error;
        }

        $conn->close();
}
    }
    
    // Sett inn bruker i databasen
    
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="nettside.css">
    <title>Registrering</title>
</head>
<body>
    <h1>Registrer deg</h1>
    <form action="registrer.php" method="POST">
        <input type="text" name="fornavn" placeholder="Fornavn" required>
        <input type="text" name="etternavn" placeholder="Etternavn" required>
        <input type="email" name="epost" placeholder="Epost" required>
        <input type="password" name="passord" placeholder="Passord" required>
        <input type="text" name="telefon" placeholder="Telefon" required>
        <textarea name="adresse" placeholder="Adresse" required></textarea>
        <input type="date" name="fodselsdato" required>
        <button type="submit">Registrer</button>
    </form>
</body>
</html>
