<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Login Page</title>
        <style>.error {color: #FF0000;}
        </style>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>

        <?php
        $imerominiaErr = $message = "";

        if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == '') {
            header('Location: login.php');
        }

        function checkReservationDate($date) {
            $servername = "localhost";
            $username = "root";
            $dbpassword = "";
            $dbname = "restaurant";
            $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
            $message = "";
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $sql = "SELECT * FROM open_date WHERE '" . $date . "'>date_from AND '" . $date . "' < date_to; ";
            if ($result = $conn->query($sql)) {
                $count = $result->num_rows;
                if ($count > 0) {
                    header('Location: Reservation_hour.php?date=' . $date);
                } else {
                    $message = "Είμαστε κλειστά";
                }
            }
            $conn->close();
            return $message;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST['imerominia'] == "") {
                $imerominiaErr = "δώσε ημερομηνία";
            } else {
                $message = checkReservationDate($_POST['imerominia']);
            }
        }

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
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
                    <img src="./images/calendar-alt-regular.svg" >
                </div>
            </div>  
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                <div class="form-group row">
                    <label for="imerominia" class="col-4 col-form-label">Ημερομηνία: </label>
                    <div class="col-8">
                        <input type="date" class="form-control" name="imerominia" placeholder="ημερομηνία">
                        <span class="error">* <?php echo $imerominiaErr; ?></span>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Συνέχεια</button> <br>
                </div>
            </form>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>
