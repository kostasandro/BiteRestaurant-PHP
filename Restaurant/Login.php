<?php
// Start the session
session_start();
?>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>

        <?php
        // καθορισμός μεταβλητών και άδεισμα τιμών
        $emailErr = $passwordErr = $message = "";
        $password = $email = "";

        //μεταβλητές για την σύνδεση με την βάση
        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $dbname = "restaurant";
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //έλεγχος email
            if (empty($_POST["email"])) {
                $emailErr = "Το email απαιτείται";
            } else {
                $email = test_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Η γραμμογράφηση του email δεν είναι σωστή";
                }
            }
            
            //έλεγχος κωδικού πρόσβασης
            if (empty($_POST["password"])) {
                $passwordErr = "Ο κωδικός πρόσβασης απαιτείται";
            }

            if ($emailErr == "" && $passwordErr == "") {
                // Δημιουργία connection
                $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
                // Έλεγχος connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                
                //έλεγχος ύπαρξης του συνδιασμού του email και του κωδικού πρόσβασης
                //αν επιστρέψει αποτελέσματα σημαίνει ότι ο χρήστης υπάρχει και κάνει login επιτυχώς
                $sql = "SELECT * FROM USERS WHERE EMAIL= '$_POST[email]' AND PASSWORD='$_POST[password]'";
                if ($result = $conn->query($sql)) {
                    $count = $result->num_rows;
                    if ($count > 0) {
                        $message = "Καλώς ήρθατε";
                        while ($row = $result->fetch_assoc()) {
                            //στην περίπτωση που ο χρήστης είναι πελάτης, βρίσκω τα στοιχεία του στον πίνακα customer,
                            //αλλιώς, στον πίνακα employee
                            if ($row["Role"] == '01') {
                                $sql = "SELECT 	First_name FROM customer WHERE Customer_ID = " . $row["Customer_ID"] . ";";
                            } else {
                                $sql = "SELECT First_name FROM employee WHERE Employee_ID = " . $row["Employee_ID"] . ";";
                            }
                            $result1 = $conn->query($sql);
                            $count = $result1->num_rows;
                            if ($count > 0) {
                                while ($row1 = $result1->fetch_assoc()) {
                                    //δημιουργία μεταβλητων στο session
                                    $_SESSION["user_first_name"] = $row1["First_name"];
                                    $_SESSION["user_id"] = $row["User_ID"];
                                    $_SESSION["user_role"] = $row["Role"];
                                    $_SESSION["customer_id"] = $row["Customer_ID"];
                                    $_SESSION["employee_id"] = $row["Employee_ID"];
                                }
                            }
                        }
                    } else {
                        $message = "Δεν βρέθηκε κάποιος χρήστης με αυτά τα στοιχεία. Προσπαθήστε ξανά";
                    }
                }
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
                <h2>Login</h2>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                <div class="form-group">
                    <label for="exampleInputEmail1">Email</label> <span class="error">* <?php echo $emailErr; ?></span>
                    <input type="text" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="πχ. andronis.konst@gmail.com">              
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Κωδικός Πρόσβασης</label> <span class="error">* <?php echo $passwordErr; ?></span>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="πχ. myPass1234">      
                </div>

                <button type="submit" class="btn btn-primary">Υποβολή</button>     
            </form>
            <div class="line">
                Δεν είστε μέλος ακόμα;
                <a href="Register.php">Δημιουργία λογαριασμού</a>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>
