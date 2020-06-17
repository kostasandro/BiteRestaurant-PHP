<?php
// Start the session
session_start();
?><html>
    <head>

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

        function getCustomers() {
            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

            //ανάκτηση όλων των πελατών προκειμένου να χρησιμοποιηθεί στην λίστα του ονόματος κράτησης
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "SELECT CONCAT(First_name,' ',Last_name) CNAME, Customer_ID FROM customer";
            $result = $conn->query($sql);
            $customers = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $customer_id = (int) $row["Customer_ID"];
                    $customerName = (string) $row["CNAME"];
                    $customers[$customer_id] = $customerName;
                }
            }
            $conn->close();
            return $customers;
        }

        $customers = getCustomers();

        //μεταβλητές για την σύνδεση με την βάση
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "restaurant";

        // καθορισμός μεταβλητών και άδεισμα τιμών
        $reservation_id = $date_string = $hour = $persons = $date = $status = $message = $customer_id = "";
        $date_err = $hour_err = $persons_err = $status_err = "";
        $title = "Δημιουργία νέας κράτησης";
        $showEditTablesButton = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'EDIT') {
            $showEditTablesButton = true;
            $title = "Διαχείριση κράτησης";
            $reservation_id = (int) $_POST['id'];

            //δημιουργία connection     
            $conn = new mysqli($servername, $username, $password, $dbname);
            //έλεγχος connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            //ανάκτηση των στοιχείων της κράτησης για το συγκερκιμένο id
            $sql = "SELECT * FROM reservation WHERE Reservation_ID = " . $reservation_id . ";";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $date_string = (string) $row["Date"];
                    $persons = (int) $row["Persons"];
                    $hour = (string) $row["Start_time"];
                    $status = (int) $row["Status"];
                    $customer_id = (int) $row ["Customer_ID"];
                }
            }
            $conn->close();
        }

        //αποθήκευση
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'SaveReservation') {
            $conn = new mysqli($servername, $username, $password, $dbname);
        
            //αν υπάρχει id κράτησης κάνω update αλλιώς insert 
            if ($_POST['id'] != "") {
                $sql = "UPDATE reservation SET status = '" . $_POST['status'] . "', Employee_ID = " . $_SESSION['employee_id'] . " WHERE Reservation_ID = '$_POST[id]';";

                if (mysqli_query($conn, $sql)) {
                    $message = "Status updated";
                }
            } else {
                $sql = "INSERT INTO reservation (Date, Persons, Start_time, Customer_ID, Employee_ID, Status)
                    VALUES ('" . $_POST['date'] . "', '" . $_POST['persons'] . "','" . $_POST['hour'] . "','" . $_POST['customer_id'] . "','" . $_SESSION['employee_id'] . "','" . $_POST['status'] . "')";

                if (mysqli_query($conn, $sql)) {
                    $lastDate_id = $conn->insert_id;
                    $message = "Reservation added";
                }
            }
            $conn->close();
            $title = "Edit Reservation date";
            $showEditTablesButton = true;
            if ($_POST['id'] == "") {
                $reservation_id = $lastDate_id;
            } else {
                $reservation_id = (int) $_POST['id'];
            }

            $date_string = $_POST['date'];
            $persons = $_POST['persons'];
            $hour = $_POST['hour'];
            $customer_id = $_POST['customer_id'];
            $status = $_POST['status'];
        }
        ?>

        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        <div class="container page">
            <div class="message" style="display:<?php echo $message != "" ? 'block' : 'none' ?>">
                <?php echo $message; ?>
            </div> 
            <div>
                <h2><?php echo $title ?></h2>
            </div>
            <div class="col-sm">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                    <input type='hidden' name='_METHOD' value='SaveReservation'>

                    <div class="form-group">
                        <label for="id">Id</label> <span class="error">* </span>
                        <input type="text" name="id" class="form-control" id="id" readonly value="<?php echo $reservation_id ?>">              
                    </div>

                    <div class="form-group">
                        <label for="date">Ημερομηνία Κράτησης</label> <span class="error">* <?php echo $date_err; ?></span>
                        <input type="date" name="date" class="form-control" value="<?php echo $date_string ?>" 
                        <?php
                        if ($showEditTablesButton == true) {
                            echo "readonly='readonly'";
                        }
                        ?>
                               >              
                    </div>

                    <div class="form-group">
                        <label for="hour">Ώρα κράτησης</label> <span class="error">* <?php echo $hour_err; ?></span>
                        <input type="text" name="hour" class="form-control"  value="<?php echo $hour
                        ?>"<?php
                               if ($showEditTablesButton == true) {
                                   echo "readonly='readonly'";
                               }
                               ?>>              
                    </div>

                    <div class="form-group">
                        <label for="persons">Αριθμός ατόμων</label> <span class="error">* <?php echo $persons_err; ?></span>
                        <input type="text" name="persons" class="form-control"  value="<?php echo $persons ?>"<?php
                        if ($showEditTablesButton == true) {
                            echo "readonly='readonly'";
                        }
                        ?>>              
                    </div>
                    <div class="form-group">
                        <label for="status">Όνομα κράτησης</label> 
                        <select name="customer_id" class="form-control"<?php
                        if ($showEditTablesButton == true) {
                            echo "readonly='readonly'";
                        }
                        ?>>
                                    <?php
                                    foreach ($customers as $key => $value) {
                                        echo "<option value='{$key}' ";
                                        if ($customer_id == $key)
                                            echo 'selected="selected"';
                                        echo '>' . $value . '</option>';
                                    }
                                    ?>
                        </select>            
                    </div>
                    <div class="form-group">
                        <label for="status">Κατάσταση κράτησης</label> <span class="error">* <?php echo $status_err; ?></span>
                        <select name="status" class="form-control">
                            <option value="1" <?= $status == '1' ? ' selected="selected"' : ''; ?>>Υπό έγκριση</option>
                            <option value="2" <?= $status == '2' ? ' selected="selected"' : ''; ?>>Εγκεκριμένη</option>
                            <option value="3" <?= $status == '3' ? ' selected="selected"' : ''; ?>>Ακυρωμένη</option>
                        </select>            
                    </div>
                    <button type="submit" class="btn btn-primary">Υποβολή</button>
                </form>
                <?php if ($showEditTablesButton == true) {
                    ?>
                    <form method="POST" action="<?php echo 'ReservationTables.php?id=' . $reservation_id; ?>" >
                        <input type='hidden' name='reservation_id' value='<?php echo $reservation_id ?>'>
                        <input type='hidden' name="_METHOD" value="GET_TABLES">
                        <button type="submit" class="btn btn-primary" >Διαχείρηση τραπεζιών κράτησης</button>
                    </form> 
                <?php } ?>
                <form method="POST" action="ReservationManagement.php" >
                    <button type="submit" class="btn btn-secondary">Πίσω</button>
                </form> 
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>