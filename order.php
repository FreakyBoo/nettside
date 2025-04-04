<?php
session_start(); // Start sesjonen for å få tilgang til brukerinformasjon

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['kunde_id'])) {
    header("Location: login.php"); // Hvis ikke logget inn, omdiriger til login-siden
    exit();
}

// Enkel håndtering av handlekurv
if (isset($_POST['add_product'])) { // Sjekker om brukeren har trykket på en "legg til" knapp
    $produkt_id = $_POST['add_product']; // Hent produkt-ID fra knappen
    if (!isset($_SESSION['handlekurv'])) {
        $_SESSION['handlekurv'] = array(); // Hvis handlekurven ikke eksisterer så opprettes en tom Array
    }
    $_SESSION['handlekurv'][] = $produkt_id; // Legger til produktet i Arrayen
}

// Fullfør bestilling
if (isset($_POST['complete_order'])) { // Sjekker om brukeren har trykket på "fullfør bestilling" knappen
    $produkter = []; // Lager en tom array for å lagre produktnavnene
    $totalpris = 0; // Lager en variabel for å beregne totalprisen

    if (isset($_SESSION['handlekurv'])) { // Sjekker om handlekurven har produkter
        $conn = new mysqli("localhost", "julian", "Julian2007!", "klesbutikk");
        foreach ($_SESSION['handlekurv'] as $produkt_id) { // Går gjennom hvert produkt_id i handlekurven
            $sql = "SELECT * FROM produkter WHERE produkt_id = '$produkt_id'";
            $result = $conn->query($sql); // Kjører SQL-spørringen

            if ($result && $result->num_rows > 0) {
                $produkt = $result->fetch_assoc();
                $produkter[] = $produkt['produkt_navn']; // Legger til produktnavnet i listen
                $totalpris += $produkt['produkt_pris']; // Legger til produktets pris til totalprisen
            }
        }
        $conn->close();
    }

    if (count($produkter) > 0) {
        $produkter_liste = implode(", ", $produkter); // Lager en kommaseparert liste av produktene

        // Koble til databasen og legg inn bestillingen
        $conn = new mysqli("localhost", "julian", "Julian2007!", "klesbutikk");
        if ($conn->connect_error) {
            die("Tilkoblingsfeil: " . $conn->connect_error);
        }

        // Sett inn bestillingen i 'bestillinger' tabellen
        $kunde_id = $_SESSION['kunde_id'];
        $sql = "INSERT INTO bestillinger (kunde_id, status) VALUES ('$kunde_id', 'Behandler')";
        if ($conn->query($sql) === TRUE) {
            $bestilling_id = $conn->insert_id; // Hent ID for den nye bestillingen

            // Sett inn bestillingsdetaljene i 'bestillingsdetaljer' tabellen
            foreach ($_SESSION['handlekurv'] as $produkt_id) {
                // Hent produktprisen fra 'produkter' tabellen
                $sql = "SELECT produkt_pris FROM produkter WHERE produkt_id = '$produkt_id'";
                $result = $conn->query($sql);
                $produkt = $result->fetch_assoc();
                $produkt_pris = $produkt['produkt_pris'];

                // Sett inn produktdetaljer i 'bestillingsdetaljer' tabellen
                $sql = "INSERT INTO bestillingsdetaljer (bestilling_id, produkt_id, antall, produkt_pris) 
                        VALUES ('$bestilling_id', '$produkt_id', 1, '$produkt_pris')";
                $conn->query($sql);
            }

            echo "Bestillingen er fullført! <a href='welcome.php'>Tilbake til velkomstside</a>";
            unset($_SESSION['handlekurv']); // Tøm handlekurven etter at bestillingen er fullført
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
        // Koble til databasen
        $conn = new mysqli("localhost", "julian", "Julian2007!", "klesbutikk");

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
            while($row = $result->fetch_assoc()):
        ?>
            <div>
                <span><?php echo $row['produkt_navn']; ?> - <?php echo $row['produkt_pris']; ?> kr</span>
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

    <div class="bilde-container">
        <img src="https://image.hm.com/assets/005/4b/70/4b7017ae3644e3006cb5f66481a2cacd18a6b713.jpg?imwidth=1536">
        <img src="https://image.hm.com/assets/005/f4/83/f483e610eac0f767958acc4b21ee70bc5c9002f4.jpg?imwidth=1536">
        <img src="https://image.hm.com/assets/hm/1c/5f/1c5f64c102fc86b0fb9bd4c37a3b5bf247cea2cf.jpg?imwidth=1536">
    </div>
</body>
</html>
