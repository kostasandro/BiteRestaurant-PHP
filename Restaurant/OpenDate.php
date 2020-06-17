<?php
// Start the session
session_start();
?><html>
    <head>
        <title>Διαχείριση Ημερομηνιών λειτουργίας</title>
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
        // καθορισμός μεταβλητών και άδεισμα τιμών
        $message = "";

        function getData() {
            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

            // δημιουργία connection     
            $conn = new mysqli($servername, $username, $password, $dbname);
            // έλεγχος connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            //ανάκτηση των ημερομηνιών που είναι ανοιχτό το restaurant
            $sql = "SELECT * FROM open_date";
            $result = $conn->query($sql);
            $date_data = "";
            if ($result->num_rows > 0) {
                // output data of each row
                while ($row = $result->fetch_assoc()) {
                    $date_from_string = (string) $row["date_from"];
                    $date_to_string = (string) $row["date_to"];
                    $date_from = date("d/m/Y", strtotime($row["date_from"]));
                    $date_to = date("d/m/Y", strtotime($row["date_to"]));
                    $date_data = $date_data .
                            "<tr>"
                            . "<td scope='row'>" . $row["id"] . "</td>"
                            . "<td>" . $date_from . "</td>"
                            . "<td>" . $date_to . "</td>"
                            . "<td >"
                            . "<form  class='inline-element' method='POST' action='OpenDateForm.php'>"
                            . "<input type='hidden' name='_METHOD' value='EDIT'>"
                            . "<input type='hidden' name='id' value='" . $row["id"] . "'>"
                            . "<button type='submit'  class='btn btn-secondary btn-sm'>Επεξεργασία</button>"
                            . "</form>&nbsp;"
                            . "<form  class='inline-element' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this?\");'>"
                            . "<input type='hidden' name='_METHOD' value='DELETE'>"
                            . "<input type='hidden' name='id' value='" . $row["id"] . "'>"
                            . "<button type='submit' class='btn btn-danger btn-sm'>Διαγραφή</button>"
                            . "</form>"
                            . "</td>"
                            . "</tr>";
                }
            }
            $conn->close();
            return $date_data;
        }

        function deleteData($id) {
            //μεταβλητές για την σύνδεση με την βάση
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

            // καθορισμός μεταβλητών και άδεισμα τιμών
            $message = "";
            //δημιουργία connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            //έλεγχος connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            //διαγραφή επιλεγμένης εγγραφής στον πίνακα open_date
            $sql = "DELETE FROM open_date WHERE id = " . $id . ";";
            if (mysqli_query($conn, $sql)) {
                $message = "Η εγγραφή διαγράφηκε επιτυχώς";

                //διαγραφή των εγγραφώ που συνδέονται με την επιλεγμένη εγγραφή στον πίνακα work_hours
                $sql2 = "SELECT work_hours_id FROM open_dates_hours WHERE open_date_id = " . $id . ";";
                $result = $conn->query($sql2);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $sql3 = "DELETE FROM work_hours WHERE id = " . $row["work_hours_id"] . ";";
                        if (mysqli_query($conn, $sql3)) {
                            $message = "Η εγγραφή διαγράφηκε επιτυχώς";

                            //διαγραφή των εγγραφώ που συνδέονται με την επιλεγμένη εγγραφή στον πίνακα open_dates_hours
                            $sql4 = "DELETE FROM open_dates_hours WHERE open_date_id = " . $id . ";";
                            if (mysqli_query($conn, $sql4)) {
                                $message = "Η εγγραφή διαγράφηκε επιτυχώς";
                            } else {
                                $message = "Παρουσιάστηκε κάποιο πρόβλημα, παρακαλω προσπαθήστε ξανά";
                            }
                        } else {
                            $message = "Παρουσιάστηκε κάποιο πρόβλημα, παρακαλω προσπαθήστε ξανά";
                        }
                    }
                }
            } else {
                $message = "Παρουσιάστηκε κάποιο πρόβλημα, παρακαλω προσπαθήστε ξανά";
            }

            $conn->close();
            return $message;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_METHOD']) && $_POST['_METHOD'] == 'DELETE') {
            $id = (int) $_POST['id'];
            //διαγραφή εγγραφών
            $message = deleteData($id);
        }
        //ανάκτηση εγγραφών
        $date_data = getData();
        ?>

        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        <div class="container page">           
            <div class="message" style="display:<?php echo $message != "" ? 'block' : 'none' ?>">
                <?php echo $message; ?>
            </div> 
            <div>   
                <h2>Διαχείριση Ημερομηνιών λειτουργίας</h2>
            </div>    
            <div class="col-sm">       
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Ημερομηνία από</th>
                            <th scope="col">Ημερομηνία έως</th>
                            <th scope="col">Ενέργεια</th>                 
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $date_data ?>
                        <tr>
                            <th scope="row"></th>
                            <td></td>
                            <td></td>
                            <td>
                                <form method="POST" action='OpenDateForm.php'> 
                                    <input type='hidden' name='_METHOD' value='CREATE'>
                                    <input type="submit" value="Εισαγωγή" class="btn btn-primary btn-sm">
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table> 
            </div>
        </div>
    </body>
</html> 