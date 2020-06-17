<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Kρατήσεις</title>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
        //redirect στην αρχική σελίδα αν ο ρόλος του χρήστη που έχει κάνει login δεν είναι τύπου εργαζομένου
        if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == '01') {
            header('Location: index.php');
        }

        function getReservation() {
            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

            // καθορισμός μεταβλητών και άδεισμα τιμών
            $reservation_data = "";

            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            //ανάκτηση των στοιχείων των κρατήσεων
            $sql = "SELECT r.* ,CONCAT(C.First_name,' ',c.Last_name) CNAME 
                    FROM reservation R 
                    INNER JOIN customer C ON R.Customer_ID=C.Customer_ID 
                    ORDER BY Date desc";
            if ($result = $conn->query($sql)) {
                $count = $result->num_rows;
                if ($count > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $date = (string) $row["Date"];
                        $date = date("d/m/Y", strtotime($date));
                        $persons = (int) $row["Persons"];
                        $hour = (string) $row["Start_time"];
                        $status = (int) $row["Status"];
                        $id = (int) $row["Reservation_ID"];
                        $customer = (string) $row["CNAME"];

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
                                . "<td>" . $customer . "</td>"
                                . "<td>"
                                . "<form  class='inline-element' method='POST' action='EditReservation.php'>"
                                . "<input type='hidden' name='_METHOD' value='EDIT'>"
                                . "<input type='hidden' name='id' value='" . $id . "'>"
                                . "<button type='submit' class='btn btn-secondary btn-sm'>Επεξεργασία</button>"
                                . "</form>"
                                . "</td>"
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
            <div>
                <h2>Kρατήσεις</h2>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Ημερομηνία</th>
                        <th scope="col">Ώρα</th>
                        <th scope="col">Αριθμός ατόμων</th> 
                        <th scope="col">Κατάσταση κράτησης</th>
                        <th scope="col">Όνομα κράτησης</th>
                        <th scope="col">Ενέργεια</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $reservation_data ?>
                    <tr>
                        <th scope="row"></th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <form method="POST" action='EditReservation.php'> 
                                <input type='hidden' name='_METHOD' value='CREATE'>
                                <input type="submit" value="Εισαγωγή" class="btn btn-primary btn-sm">
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div> 
        <?php include 'footer.php'; ?>
    </body>
</html>