<?php
session_start(); // Start sesjonen for å få tilgang til brukerinformasjon

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['kunde_id'])) {
    header("Location: login.php"); // Hvis ikke logget inn, omdiriger til login-siden
    exit();
}

// Enkel håndtering av handlekurv
if (isset($_POST['add_product'])) { //sjekker om brukeren har trykket på en "legg til" knapp
    $produkt_id = $_POST['add_product']; // Hent produkt-ID fra knappen
    if (!isset($_SESSION['handlekurv'])) {
        $_SESSION['handlekurv'] = array(); // Hvis handlekurven ikke eksisterer så opprettes en tom Array
    }
    $_SESSION['handlekurv'][] = $produkt_id; // Legger til produktet i Arrayen
}

// Fullfør bestilling
if (isset($_POST['complete_order'])) { //sjekker om brukeren har trykket på "fullfør bestilling" knappen
    // Hent produktene fra handlekurven og beregn totalpris
    $produkter = []; //Lager en tom array $produkter for å lagre navnene på bestilte produkter.
    $totalpris = 0; //Lager en variabel $totalpris som starter på 0.

    if (isset($_SESSION['handlekurv'])) { //Sjekker om handlekurven eksisterer.
        $conn = new mysqli("localhost", "julian", "Julian2007!", "nettbutikk"); //Lager en tilkobling til MySQL-databasen ($conn).
        foreach ($_SESSION['handlekurv'] as $produkt_id) { //Henter hvert produkt_id som er lagret i handlekurven "("$_SESSION['handlekurv"]. Foreach går gjennom hvert element i arrayen (som er [handlekurv]).
            // Hent produktinformasjon fra databasen
            $sql = "SELECT * FROM produkter WHERE produkt_id = '$produkt_id'";
            $result = $conn->query($sql); // Kjører $sql spørringen mot databasen og lagrer resultatet i $result

            if ($result && $result->num_rows > 0) { //sjekker om det finnes rader i $result (altså om spørringen mot databasen fant produkter). && betyr "og", så begge betingelsene må være true for at if-blokken skal kjøre.
                $produkt = $result->fetch_assoc(); // Lager en assosiativ liste
                $produkter[] = $produkt['navn']; // Legg til produktnavn
                $totalpris += $produkt['pris'];  // Legg til pris i totalpris
            }
        }
        $conn->close();
    }

    // Sett opp bestillingen i databasen
    if (count($produkter) > 0) {
        $produkter_liste = implode(", ", $produkter); // Lager en kommaseparert liste av produkter

        // Koble til databasen og legg inn bestillingen
        $conn = new mysqli("localhost", "julian", "Julian2007!", "nettbutikk");
        if ($conn->connect_error) {
            die("Tilkoblingsfeil: " . $conn->connect_error);
        }

        $kunde_id = $_SESSION['kunde_id'];
        $produkt_id = $_SESSION['produkt_id'];
        $sql = "INSERT INTO bestillinger (kunde_id, status, produkt_id) VALUES ('$kunde_id', 'Behandler', '$produkt_id')";
        if ($conn->query($sql) === TRUE) {
            $bestilling_id = $conn->insert_id; // Få ID for den nye bestillingen

            foreach ($_SESSION['handlekurv'] as $produkt_id) { //Henter hvert produkt_id som er lagret i handlekurven "("$_SESSION['handlekurv"
                // Legg til bestillingsdetaljer i databasen
                $sql = "INSERT INTO bestillingsdetaljer (bestilling_id, produkt_id, antall, pris) 
                        VALUES ('$bestilling_id', '$produkt_id', 1, (SELECT pris FROM produkter WHERE produkt_id = '$produkt_id'))";
                $conn->query($sql);
            }

            echo "Bestillingen er fullført! <a href='welcome.php'>Tilbake til velkomstside</a>";

            // Tøm handlekurven etter at bestillingen er fullført
            unset($_SESSION['handlekurv']);
        } else {
            echo "Feil med bestillingen: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Handlekurven er tom. Kan ikke fullføre bestilling.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="nettside.css">
    <title>Bestill produkter</title>
</head>
<body>
    <h1>Handlekurv</h1>
    <form action="order.php" method="POST">
        <h2>Velg produkter:</h2>
        <?php
    // Opprett en tilkobling til MySQL-databasen
    $conn = new mysqli("localhost", "julian", "Julian2007!", "nettbutikk");

    // Sjekk om tilkoblingen var vellykket
    if ($conn->connect_error) {
        die("Tilkoblingsfeil: " . $conn->connect_error);
    }

    // Hent alle produkter fra databasen
    $sql = "SELECT * FROM produkter";
    $result = $conn->query($sql);

    // Lukk tilkoblingen til databasen
    $conn->close();

    // Sjekk om det finnes produkter i databasen
    if ($result->num_rows > 0):
        // Loop gjennom hvert produkt og hent data
        while($row = $result->fetch_assoc()):
    ?>

                <div>
                    <span><?php echo $row['navn']; ?> - <?php echo $row['pris']; ?> kr</span>
                    <button type="submit" name="add_product" value="<?php echo $row['produkt_id']; ?>">Legg til</button> 
                </div>
        <?php
            endwhile;
        else:
            echo "<p>Ingen produkter tilgjengelig.</p>";
        endif;
        ?>
        
        <!-- Fullfør bestilling-knapp -->
        <button type="submit" name="complete_order">Fullfør bestilling</button>
    </form>

</body>
</html>
