<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Επεξεργασία Ωρών λειτουργίας</title>
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

        // καθορισμός μεταβλητών και αρχικοποίηση τιμών
        $hour_id = $hour_from_string = $hour_to_string = $date_id = $message = "";
        $hourTo_err = $hourFrom_err = "";
        $title = "Εισαγωγή νέων ωρών λειτουργίας";

        //γέμισμα της φόρμας
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'EDIT') {
            $title = "Επεξεργασία ωρών λειτουργίας";
            $date_id = (int) $_POST['date_id'];
            $hour_id = (int) $_POST['id'];
            $hour_from_string = (string) $_POST['hour_from'];
            $hour_to_string = (string) $_POST['hour_to'];
        }

        //γέμισμα της φόρμας
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'CREATE') {
            $date_id = (int) $_POST['date_id'];
        }

        //αποθήκευση
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'SaveHours') {
            //έλεγχος ώρα από
            if (empty($_POST["hour_from"])) {
                $hourFrom_err = "hour from is required";
            }
            if ($hourFrom_err == "") {
                $conn = new mysqli($servername, $username, $password, $dbname);

                //αν υπάρχει id σημαίνει πως υπάρχει εγγραφή στην βάση, αρά γίνεται update με τα νέα στοιχεία
                //αν δεν υπάρχει δημιουργώ νέα εγγραφή
                if ($_POST['hour_id'] != "") {
                    $sql = "UPDATE work_hours SET hour_from = '$_POST[hour_from]', hour_to = '$_POST[hour_to]' WHERE id = '$_POST[hour_id]';";
                    if (mysqli_query($conn, $sql)) {
                        $message = "Οι ώρες λειτουργίας ενημερώθηκαν";
                    }
                } else {
                    $sql = "INSERT INTO work_hours (hour_from, hour_to) VALUES ('$_POST[hour_from]', '$_POST[hour_to]')";
                    $lasthour_id = "";
                    if (mysqli_query($conn, $sql)) {
                        $lasthour_id = $conn->insert_id;
                        $message = "Οι ημερομηνίες λειτουργίας προστέθηκαν επιτυχώς";
                    }
                    $sql2 = "INSERT INTO open_dates_hours (open_date_id, work_hours_id) VALUES ('$_POST[date_id]', $lasthour_id)";
                    if (mysqli_query($conn, $sql2)) {
                        $message = "Οι ώρες λειτουργίας προστέθηκαν επιτυχώς";
                    }
                }
                $conn->close();
                $title = "Επεξεργασία ωρών λειτουργίας";
                if ($_POST['hour_id'] == "") {
                    $hour_id = $lasthour_id;
                } else {
                    $hour_id = (int) $_POST['hour_id'];
                }
                $hour_from_string = (string) $_POST['hour_from'];
                $hour_to_string = (string) $_POST['hour_to'];
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
                <h2><?php echo $title ?></h2>
            </div> 
            <div class="col-sm">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                    <input type='hidden' name='_METHOD' value='SaveHours'>
                    <input type='hidden' name='date_id' value= "<?php echo $date_id ?>">

                    <div class="form-group">
                        <label for="id">Id</label> <span class="error">* </span>
                        <input type="text" name="hour_id" class="form-control" id="hour_id" readonly value="<?php echo $hour_id ?>">              
                    </div>

                    <div class="form-group">
                        <label for="hour_from">Ώρα από</label> <span class="error">* <?php echo $hourFrom_err; ?></span>
                        <input type="text" name="hour_from" class="form-control" id="hour_from" value="<?php echo $hour_from_string ?>">              
                    </div>

                    <div class="form-group">
                        <label for="hour_to">Ώρα έως</label> <span class="error">* <?php echo $hourTo_err; ?></span>
                        <input type="text" name="hour_to" class="form-control" id="hour_to" value="<?php echo $hour_to_string ?>">              
                    </div>

                    <button type="submit" class="btn btn-primary">Υποβολή</button>
                </form>
                <form method="POST" action="OpenHours.php" >
                    <input type='hidden' name='date_id' value='<?php echo $date_id ?>'>
                    <input type='hidden' name="_METHOD" value="GET_HOURS">
                    <button type="submit" class="btn btn-secondary">Πίσω</button>
                </form> 
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>


