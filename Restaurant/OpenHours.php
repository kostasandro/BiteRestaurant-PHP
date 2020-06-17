<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Ώρες λειτουργίας</title>
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
        // καθορισμός μεταβλητών και αρχικοποίηση τιμών
        $message = "";

        function getHourData($date_id) {
            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

            // καθορισμός μεταβλητών και αρχικοποίηση τιμών
            $hour_data = "";

            //δημιουργία connection     
            $conn = new mysqli($servername, $username, $password, $dbname);
            //έλεγχος connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            //ανάκτηση των ωρών λειτουργίας για το επιλεγμένο διάστημα ημερομηνιών
            $sql = "SELECT WH.*  FROM open_dates_hours ODH INNER JOIN work_hours WH ON ODH.work_hours_id = WH.id
            WHERE ODH.open_date_id = " . $date_id . ";";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                $hour_data = "";
                while ($row = $result->fetch_assoc()) {
                    $hour_from_string = (string) $row["hour_from"];
                    $hour_to_string = (string) $row["hour_to"];
                    $hour_data = $hour_data .
                            "<tr>"
                            . "<td scope='row'>" . $row["id"] . "</td>"
                            . "<td>" . $hour_from_string . "</td>"
                            . "<td>" . $hour_to_string . "</td>"
                            . "<td>"
                            . "<form  class='inline-element' method='POST' action='OpenHoursForm.php'>"
                            . "<input type='hidden' name='_METHOD' value='EDIT'>"
                            . "<input type='hidden' name='date_id' value='" . $date_id . "'>"
                            . "<input type='hidden' name='id' value='" . $row["id"] . "'>"
                            . "<input type='hidden' name='hour_from' value='" . $hour_from_string . "'>"
                            . "<input type='hidden' name='hour_to' value='" . $hour_to_string . "'>"
                            . "<button type='submit' class='btn btn-secondary btn-sm'>Επεξεργασία</button>"
                            . "</form>&nbsp;"
                            . "<form  class='inline-element' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this?\");'>"
                            . "<input type='hidden' name='_METHOD' value='DELETE'>"
                            . "<input type='hidden' name='hour_id' value='" . $row["id"] . "'>"
                            . "<input type='hidden' name='date_id' value='" . $date_id . "'>"
                            . "<button type='submit' class='btn btn-danger btn-sm'>Διαγραφή</button>"
                            . "</form>"
                            . "</td>"
                            . "</tr>";
                }
            }
            $conn->close();
            return $hour_data;
        }

        function deleteData($hour_id) {
            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

            // καθορισμός μεταβλητών και αρχικοποίηση τιμών
            $hour_data = "";

            //δημιουργία connection     
            $conn = new mysqli($servername, $username, $password, $dbname);
            //έλεγχος connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            //διαγραφή των ωρών λειτουργία από τον πίνακα work_hours
            $sql = "DELETE FROM work_hours WHERE id = " . $hour_id . ";";
            if (mysqli_query($conn, $sql)) {
                $message = "Η εγγραφή διαγράφηκε επιτυχώς";

                //διαγραφή των ωρών λειτουργία από τον πίνακα open_dates_hours
                $sql2 = "DELETE FROM open_dates_hours WHERE work_hours_id = " . $hour_id . ";";
                if (mysqli_query($conn, $sql2)) {
                    $message = "Η εγγραφή διαγράφηκε επιτυχώς";
                } else {
                    $message = "Παρουσιάστηκε κάποιο πρόβλημα, παρακαλω προσπαθήστε ξανά";
                }
            } else {
                $message = "Παρουσιάστηκε κάποιο πρόβλημα, παρακαλω προσπαθήστε ξανά";
            }
            $conn->close();
            return $message;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'GET_HOURS') {
            $date_id = (int) $_POST['date_id'];
            $hour_data = getHourData($date_id);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'DELETE') {
            $hour_id = (int) $_POST['hour_id'];
            $date_id = (int) $_POST['date_id'];
            //διαγραφή δεδομένων
            deleteData($hour_id);
        }

        //ανάκτηση δεδομένων
        $hour_data = getHourData($date_id);
        ?>

        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>

        <div class="container page">
            <div class="message" style="display:<?php echo $message != "" ? 'block' : 'none' ?>">
                <?php echo $message; ?>
            </div> 
            <div>   
                <h2>Ώρες λειτουργίας</h2>
            </div> 
            <div class="col-sm">                      
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Ώρα από</th>
                            <th scope="col">Ώρα έως</th>
                            <th scope="col">Ενέργεια</th>                 
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $hour_data ?>
                        <tr>
                            <th scope="row"></th>
                            <td></td>
                            <td></td>
                            <td>
                                <form method="POST" action='OpenhoursForm.php'> 
                                    <input type='hidden' name='date_id' value= "<?php echo $date_id ?>">
                                    <input type='hidden' name='_METHOD' value='CREATE'>
                                    <input type="submit" value="Insert" class="btn btn-primary btn-sm">
                                </form>

                            </td>

                        </tr>
                    </tbody>
                </table> 
                <form method="POST" action="OpenDateForm.php" >
                    <input type='hidden' name='id' value='<?php echo $date_id ?>'>
                    <input type='hidden' name="_METHOD" value="EDIT">
                    <button type="submit" class="btn btn-secondary">Πίσω</button>
                </form> 
            </div>
        </div>
        <?php include 'footer.php'; ?>  
    </body>
</html>

