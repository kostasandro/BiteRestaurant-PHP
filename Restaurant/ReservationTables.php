<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Διαχείριση τραπεζιών κράτησης</title>
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

        //μεταβλητές για την σύνδεση με την βάση
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "restaurant";
        $message = "";

        // καθορισμός μεταβλητών και άδεισμα τιμών
        $capacity = $capacity_err = "";
        $reservation_id = $_GET["id"];

        function getData($reservation_id) {
            $tables = getAvailableTables($reservation_id);
            $tablesReservation = getReservationTables($reservation_id);
            $table_data = "";

            if (!empty($tables)) {
                foreach ($tables as $key => $value) {
                    $table_id = $key;
                    $capacity = $value;
                    $selected = "";
                    if (array_key_exists($key, $tablesReservation)) {
                        $selected = "checked";
                    }
                    $table_data = $table_data .
                            "<tr>"
                            . "<td scope='row'>" . $table_id . "</td>"
                            . "<td>" . $value . "</td>"
                            . "<td><input type='checkbox' name='reservation_list[]' class='btn btn-secondary btn-sm' value='" . $table_id . "'" . $selected . "></td>"
                            . "</tr>";
                }
            }
            return $table_data;
        }

        function getAvailableTables($reservation_id) {
            $sql = "SELECT * FROM (
                SELECT * FROM res_table 
                    WHERE Table_ID NOT IN (SELECT RT.Table_ID FROM reservation R 
                                            INNER JOIN reservation_table RT
                                                ON R.Reservation_ID = RT.Reservation_ID
                                            WHERE r.Date=(SELECT r1.date FROM reservation r1 WHERE r1.Reservation_ID= " . $reservation_id . ")
                                                AND r.status!=3                                           
                                                AND r.Start_time =(SELECT r1.Start_time FROM reservation r1 WHERE r1.Reservation_ID=" . $reservation_id . ")) 
                    UNION 
                    SELECT * FROM res_table 
                    WHERE Table_ID IN (SELECT RT.Table_ID FROM reservation_table RT WHERE RT.Reservation_ID =" . $reservation_id . " )"
                    . ") TABLES ORDER BY Table_ID";

            return GetTables($reservation_id, $sql);
        }

        function getReservationTables($reservation_id) {
            $sql = "SELECT * FROM res_table 
                    WHERE Table_ID IN (SELECT RT.Table_ID FROM reservation_table RT WHERE RT.Reservation_ID =" . $reservation_id . " )";
            return GetTables($reservation_id, $sql);
        }

        function GetTables($reservation_id, $sql) {
            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

            //δημιουργία connection     
            $conn = new mysqli($servername, $username, $password, $dbname);
            //έλεγχος connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $result = $conn->query($sql);
            $tables = array();

            if ($result->num_rows > 0) {
                $table_data = "";
                while ($row = $result->fetch_assoc()) {
                    $table_id = $row["Table_ID"];
                    $capacity = $row["Capacity"];
                    $tables[$table_id] = $capacity;
                }
            }
            $conn->close();
            return $tables;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST['reservation_list'])) {
                $message = "Πρέπει να επιλέξετε τουλάχιστον ένα τραπέζι";
            } else {
                $requestedPersons = "";
                $sql = "SELECT Persons FROM reservation WHERE Reservation_ID = " . $reservation_id;
                //δημιουργία connection     
                $conn = new mysqli($servername, $username, $password, $dbname);
                //έλεγχος connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
               
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $requestedPersons = $row["Persons"];
                    }
                }
                $conn->close();
                $sum = 0;
                $tables = getAvailableTables($reservation_id);

                foreach ($_POST['reservation_list'] as $selected) {
                    if ($sum == 0) {
                        $sum = $tables[$selected];
                    } else {
                        $sum = $sum + $tables[$selected] - 2;
                    }
                }
                if ($sum < $requestedPersons) {
                    $message = "Δεν επαρκούν οι θέσεις των τραπεζιών για την συγκεκριμένη κράτηση.";
                }
            }
            
            if ($message == "") {
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "DELETE FROM reservation_table WHERE Reservation_ID = " . $reservation_id;
                if (mysqli_query($conn, $sql)) {
                    foreach ($_POST['reservation_list'] as $selected) {
                        $table_id = $selected;
                        $sql = "INSERT INTO reservation_table(Table_ID, Reservation_ID) VALUES (" . $selected . "," . $reservation_id . ")";
                        if (mysqli_query($conn, $sql)) {
                            $message = "Η κράτηση ενημερώθηκε.";
                        } else {
                            $message = "Προέκυψε πρόβλημα προσπαθήστε ξανά2.";
                        }
                    }
                } else {
                    $message = "Προέκυψε πρόβλημα προσπαθήστε ξανά.";
                }


                $conn->close();
            }
        }

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $table_data = getData($reservation_id);
        ?>

        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        <div class="container page">

            <div class="message" style="display:<?php echo $message != "" ? 'block' : 'none' ?>">
                <?php echo $message; ?>
            </div> 
            <div>
                <h2>Διαχείριση τραπεζιών κράτησης</h2>
            </div>
            <div class="row">
                <div class="col-sm">
                    <form method="post" action="Reservationtables.php?id=<?PHP echo $reservation_id; ?>">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Table Number</th>
                                    <th scope="col">Capacity</th> 
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo $table_data ?>

                            </tbody>
                        </table>
                        <div>
                            <button type="submit" class="btn btn-primary">Υποβολή</button> <br>
                        </div>
                    </form>
                </div>

            </div>  
        </div>     
        <?php include 'footer.php'; ?>  
    </body>
</html>
