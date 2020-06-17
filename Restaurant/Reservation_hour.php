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
        $date = $message = $date_data = "";

        if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == '') {
            header('Location: login.php');
        }

        $date = $_GET["date"];

        function getHours($date) {
            // Create connection 
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "SELECT hour_from , hour_to FROM   work_hours W
                INNER JOIN open_dates_hours ODH
                ON W.id = ODH.work_hours_id 
                INNER JOIN open_date D
                ON ODH.open_date_id = D.id 
                WHERE '" . $date . "' >= date_from AND '" . $date . "' <= date_to ";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                $hour_data = "";
                while ($row = $result->fetch_assoc()) {
                    $hour_from_string = (string) $row["hour_from"];
                    $hour_to_string = (string) $row["hour_to"];
                    $hour_data = $hour_data .
                            "<tr>"
                            . "<td>" . $hour_from_string . "</td>"
                            . "<td>" . $hour_to_string . "</td>"
                            . "<td> <input type='radio' name='reservation' value='" . $hour_from_string . "'></td>"
                            . "</tr>";
                }
            }

            $conn->close();
            return $hour_data;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!isset($_POST['reservation']) || $_POST['reservation'] == "") {
                $message = "Επέλεξε ώρα";
            } else {
                header('Location: Reservation_persons.php?date=' . $date . '&hour=' . $_POST['reservation']);
            }
        }

        $date_data = getHours($date);
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
                    <img src="./images/clock-regular.svg" >
                </div>
            </div>  
            <form method="post" action="Reservation_hour.php?date=<?PHP echo $date ?>">
                <input type='hidden' name='date' value='<?php echo $date ?>'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Hour from</th>
                            <th scope="col">Hour to</th>
                            <th scope="col">Επιλογή</th>                 
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $date_data ?>
                    </tbody>
                </table>
                <div>
                    <button type="submit" class="btn btn-primary">Συνέχεια</button> <br>
                </div>
            </form>
        </div> 
        <?php include 'footer.php'; ?>

    </body>
</html>
