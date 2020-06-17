<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>

        <?php
        $personsErr = $message = "";

        $date = $_GET["date"];
        $hour = $_GET["hour"];

        if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == '') {
            header('Location: login.php');
        }

        function submitReservation($date, $hour, $persons) {
            $servername = "localhost";
            $username = "root";
            $dbpassword = "";
            $dbname = "restaurant";
            $message = $table = "";

            $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $sql = "SELECT * FROM res_table 
                    WHERE Table_ID NOT IN (SELECT RT.Table_ID FROM reservation R 
                                            INNER JOIN reservation_table RT
                                                ON R.Reservation_ID = RT.Reservation_ID
                                            WHERE Date= '" . $date . "' AND Start_time='" . $hour . "')"
                    . "ORDER BY Capacity";
            if ($result = $conn->query($sql)) {
                $count = $result->num_rows;
                if ($count > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $table_id = (int) $row["Table_ID"];
                        $capacity = (int) $row["Capacity"];
                        if ($persons <= $capacity) {
                            $table = $table_id;
                            break;
                        }
                    }
                    if ($table == "") {
                        $tables = array();
                        $sum = 0;
                        $result->data_seek(0);

                        while ($row = $result->fetch_assoc()) {
                            $table_id = (int) $row["Table_ID"];
                            $capacity = (int) $row["Capacity"];
                            echo $table_id, $capacity;
                            if ($sum == 0) {
                                array_push($tables, $table_id);
                                $sum = $capacity;
                            } else {
                                array_push($tables, $table_id);
                                $sum = $sum + $capacity - 2;
                            }
                            if ($persons <= $sum) {
                                break;
                            }
                        }
                        if ($sum <= $persons) {
                            $message = "Δεν είναι εφικτή η κράτηση. Παρακαλώ επικοινωνίστε μαζί μας τηλεφωνικά.";
                        } else {
                            $sql = "INSERT INTO reservation(Date, Persons, Start_time, Customer_ID, Status)
                                    VALUES ('" . $date . "', " . $persons . ", '" . $hour . "', " . $_SESSION["customer_id"] . ", '01')";
                            if ($result = $conn->query($sql)) {
                                $lastreservation_id = $conn->insert_id;
                                for ($x = 0; $x < $tables; $x++) {
                                    $table_id = $tables[$x];
                                    $sql = "INSERT INTO reservation_table(Table_ID, Reservation_ID) VALUES (" . $table_id . "," . $lastreservation_id . ")";
                                    $result = $conn->query($sql);
                                }

                                $message = "Ευχαριστούμε πολύ, θα σας ειδοποιήσουμε για την κράτησή σας. Θα ανακατευθυνθείτε στην αρχική σελίδα σε 5 δευτερόλεπτα";
                                header('refresh:5;url=myReservations.php');
                            } else {
                                $message = "Προσπαθήστε ξανά αργότερα.";
                            }
                        }
                    } else {
                        $sql = "INSERT INTO reservation(Date, Persons, Start_time, Customer_ID, Status)
                                    VALUES ('" . $date . "', " . $persons . ", '" . $hour . "', " . $_SESSION["customer_id"] . ", 1)";
                        if ($result = $conn->query($sql)) {
                            $lastreservation_id = $conn->insert_id;
                            $sql = "INSERT INTO reservation_table(Table_ID, Reservation_ID) VALUES (" . $table . "," . $lastreservation_id . ")";
                            $result = $conn->query($sql);

                            $message = "Ευχαριστούμε πολύ, θα σας ειδοποιήσουμε για την κράτησή σας. Θα ανακατευθυνθείτε στην αρχική σελίδα σε 5 δευτερόλεπτα";
                            header('refresh:5;url=myReservations.php');
                        }
                    }
                } else {
                    $message = "Είμαστε γεμάτοι";
                }
            }
            $conn->close();
            return $message;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST['persons'] == "") {
                $personsErr = "δώσε άτομα";
            } else {
                $message = submitReservation($date, $hour, $_POST['persons']);
            }
        }
        ?>

        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>

        <div class="container page">
            <div class="message" style="display:<?php echo $message != "" ? 'block' : 'none' ?>">
                <?php echo $message; ?>
            </div> 
            <div>
                <h2>Κράτηση</h2>
            </div>
            <div class="row justify-content-md-center" style="margin-bottom:20px; margin-top: 20px; ">
                <div class="col col-2">
                    <img src="./images/user-plus-solid.svg" >
                </div>
            </div> 
            <form method="post" action="Reservation_persons.php?date=<?PHP echo $date ?>&hour=<?php echo $hour ?>">
                <div class="form-group row">
                    <label for="persons" class="col-4 col-form-label">Άτομα: </label>
                    <div class="col-8">
                        <input type="text" class="form-control" name="persons" placeholder="4">
                        <span class="error">* <?php echo $personsErr; ?></span>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Υποβολή</button> <br>
                </div>
            </form>
        </div>
            <?php include 'footer.php'; ?>
    </body>
</html>
