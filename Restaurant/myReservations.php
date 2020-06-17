<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Οι κρατήσεις μου</title>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
        //redirect στην αρχική σελίδα αν ο ρόλος του χρήστη που έχει κάνει login δεν είναι τύπου πελάτη
        if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == '02') {
            header('Location: index.php');
        }

        function getReservation() {
            // καθορισμός μεταβλητών και άδεισμα τιμών
            $reservation_data = "";

            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $dbpassword = "";
            $dbname = "restaurant";

            //δημιουργία connection
            $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
            //έλεγχος connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            //ανάκτηση των κρατήσεων του πελάτη χρησιμοποιώντας το id του από το session
            $sql = "SELECT * FROM reservation WHERE Customer_ID = '" . $_SESSION["customer_id"] . "';";
            if ($result = $conn->query($sql)) {
                $count = $result->num_rows;
                if ($count > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $date = (string) $row["Date"];
                        $date = date("d/m/Y", strtotime($date));
                        $persons = (int) $row["Persons"];
                        $hour = (string) $row["Start_time"];
                        $status = (int) $row["Status"];

                        if ($status == 1) {
                            $status = "Υπό έγκριση";
                        } elseif ($status == 2) {
                            $status = "Εγκεκριμένη";
                        } else {
                            $status = "Ακυρωμένη";
                        }
                        $reservation_data = $reservation_data .
                                "<tr>"
                                . "<td>" . $date . "</td>"
                                . "<td>" . $hour . "</td>"
                                . "<td>" . $persons . "</td>"
                                . "<td>" . $status . "</td>"
                                . "</tr>";
                    }
                }
            }
            $conn->close();
            return $reservation_data;
        }

        $reservation_data = getReservation();
        ?>
        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>

        <div class="container page"> 
            <div class="Login">
                <h2>Οι κρατήσεις μου</h2>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Ημερομηνία</th>
                        <th scope="col">Ώρα</th>
                        <th scope="col">Αριθμός ατόμων</th> 
                        <th scope="col">Κατάσταση κράτησης</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $reservation_data ?>
                </tbody>
            </table>

        </div> 
        <?php include 'footer.php'; ?>
    </body>
</html>