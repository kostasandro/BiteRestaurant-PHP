<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Επεξεργασία Ημερομηνίας λειτουργίας</title>
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
        $date_id = $date_from_string = $date_to_string = $message = "";
        $dateFrom_err = $dateTo_err = "";
        $title = "Προσθήκη νέων ημερομηνιών λειτουργίας";
        $showEditHoursButton = false;

        //γέμισμα της φόρμας
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'EDIT') {
            $showEditHoursButton = true;
            $title = "Επεξεργασία Ημερομηνίας λειτουργίας";
            $date_id = (int) $_POST['id'];

            //δημιουργία connection     
            $conn = new mysqli($servername, $username, $password, $dbname);
            // έλεγχος connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            //ανάκτηση των δεδομένων από την βάση για το επιλεγμένο id
            $sql = "SELECT date_from,date_to FROM open_date WHERE id = " . $date_id . ";";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $date_from_string = (string) $row["date_from"];
                    $date_to_string = (string) $row["date_to"];
                }
            }
            $conn->close();
        }
        
        //αποθήκευση
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'SaveDate') {
            //έλεγχος ημερομηνίας από
            if (empty($_POST["date_from"])) {
                $dateFrom_err = "Date from is required";
            }
            if ($dateFrom_err == "") {
                $conn = new mysqli($servername, $username, $password, $dbname);
                //αν υπάρχει id σημαίνει πως υπάρχει εγγραφή στην βάση, αρά γίνεται update με τα νέα στοιχεία
                //αν δεν υπάρχει δημιουργώ νέα εγγραφή
                if ($_POST['id'] != "") {
                    $sql = "UPDATE open_date SET date_from = '$_POST[date_from]', date_to = '$_POST[date_to]' WHERE id = '$_POST[id]';";
                    if (mysqli_query($conn, $sql)) {
                        $message = "Οι ημερομηνίες λειτουργίας έχουν ενημερωθεί";
                    }
                } else {
                    $sql = "INSERT INTO open_date (date_from, date_to) VALUES ('$_POST[date_from]', '$_POST[date_to]')";
                    if (mysqli_query($conn, $sql)) {
                        $lastDate_id = $conn->insert_id;
                        $message = "Οι ημερομηνίες λειτουργίας προστέθηκαν επιτυχώς";
                    }
                }
                $conn->close();
                $title = "Επεξεργασία ημερομηνιών λειτουργίας";
                $showEditHoursButton = true;
                if ($_POST['id'] == "") {
                    $date_id = $lastDate_id;
                } else {
                    $date_id = (int) $_POST['id'];
                }
                $date_from_string = (string) $_POST['date_from'];
                $date_to_string = (string) $_POST['date_to'];
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
                    <input type='hidden' name='_METHOD' value='SaveDate'>

                    <div class="form-group">
                        <label for="id">Id</label> <span class="error">* </span>
                        <input type="text" name="id" class="form-control" id="id" readonly value="<?php echo $date_id ?>">              
                    </div>

                    <div class="form-group">
                        <label for="date_from">Ημερομηνία από</label> <span class="error">* <?php echo $dateFrom_err; ?></span>
                        <input type="date" name="date_from" class="form-control" id="date_from" value="<?php echo $date_from_string ?>">              
                    </div>

                    <div class="form-group">
                        <label for="date_to">Ημερομηνία έως</label> <span class="error">* <?php echo $dateTo_err; ?></span>
                        <input type="date" name="date_to" class="form-control" id="date_to" value="<?php echo $date_to_string ?>">              
                    </div>

                    <button type="submit" class="btn btn-primary">Υποβολή</button>
                </form>
                <?php if ($showEditHoursButton == true) {
                    ?>
                    <form method="POST" action="OpenHours.php" >
                        <input type='hidden' name='date_id' value='<?php echo $date_id ?>'>
                        <input type='hidden' name="_METHOD" value="GET_HOURS">
                        <button type="submit" class="btn btn-primary">Επεξεργασία ωρών λειτουργίας</button>
                    </form> 
                <?php } ?>
                <form method="POST" action="OpenDate.php" >
                    <button type="submit" class="btn btn-secondary">Πίσω</button>
                </form> 
            </div>
        </div>
        
        <?php include 'footer.php'; ?>
    </body>
</html>