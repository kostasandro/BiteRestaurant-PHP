<?php
// Start the session
session_start();
?><!DOCTYPE HTML>  
<html>
    <head>
        <title>Προσθήκη υπαλλήλου</title>
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
        $dbpassword = "";
        $dbname = "restaurant";

        // καθορισμός μεταβλητών και άδεισμα τιμών
        $nameErr = $SurnameErr = $emailErr = $passwordErr = $cpasswordErr = $genderErr = "";
        $name = $surname = $password = $cpassword = $email = $gender = $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //έλεγχος ονόματος
            if (empty($_POST["name"])) {
                $nameErr = "Το όνομα απαιτείται";
            } else {
                $name = test_input($_POST["name"]);
                if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
                    $nameErr = "Επιτρέπονται μόνο γράμματα και ο κενός χαρακτήρας";
                }
            }

            //έλεγχος επώνυμου
            if (empty($_POST["surname"])) {
                $SurnameErr = "Το επώνυμο απαιτείται";
            } else {
                $surname = test_input($_POST["surname"]);
                if (!preg_match("/^[a-zA-Z ]*$/", $surname)) {
                    $SurnameErr = "Επιτρέπονται μόνο γράμματα και ο κενός χαρακτήρας";
                }
            }

            //έλεγχος email
            if (empty($_POST["email"])) {
                $emailErr = "Το email απαιτείται";
            } else {
                $email = test_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Η γραμμογράφηση του email δεν είναι σωστή";
                }
            }

            //έλεγχος κωδικού πρόσβασης και της επιβεβαίωσης
            if (empty($_POST["password"])) {
                $passwordErr = "Ο κωδικός πρόσβασης απαιτείται";
            } elseif (empty($_POST["cpassword"])) {
                $cpasswordErr = "Η επιβεβαίωση του κωδικού πρόσβασης απαιτείται";
            } else {
                if ($_POST["password"] == $_POST["cpassword"]) {
                    $password = test_input($_POST["password"]);
                    $cpassword = test_input($_POST["cpassword"]);
                    if (strlen($_POST["password"]) < 8) {
                        $passwordErr = "Ο κωδικός πρόσβασης πρέπει να αποτελείται από τουλάχιστον 8 χαρακτήρες!";
                    } elseif (!preg_match("#[0-9]+#", $password)) {
                        $passwordErr = "Ο κωδικός πρόσβασης πρέπει να περιέχει τουλάχιστον έναν αριθμό!";
                    } elseif (!preg_match("#[A-Z]+#", $password)) {
                        $passwordErr = "Ο κωδικός πρόσβασης πρέπει να περιέχει τουλάχιστον ένα κεφαλαίο χαρακτήρα!";
                    } elseif (!preg_match("#[a-z]+#", $password)) {
                        $passwordErr = "Ο κωδικός πρόσβασης πρέπει να περιέχει τουλάχιστον έναν πεζό χαρακτήρα!";
                    }
                } else {
                    $passwordErr = "Ο κωδικός πρόσβασης και η επιβεβαίωση του δεν ταυτίζονται";
                    $cpasswordErr = "Ο κωδικός πρόσβασης και η επιβεβαίωση του δεν ταυτίζονται";
                }
            }

            if ($nameErr == "" && $SurnameErr == "" && $emailErr == "" && $passwordErr == "" && $cpasswordErr == "" && $genderErr == "") {

                //δημιουργία connection
                $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
                //έλεγος connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                //έλεγχος αν υπάρχει χρήστης με αυτό το email, 
                //αν δεν υπάρχει δημιουργούμε μια νέα εγγραφή στον πίνακα των employees
                //και μια νέα εγγραφή στον πίνακα των users
                if ($result = $conn->query("SELECT * FROM USERS WHERE EMAIL= '$_POST[email]'")) {
                    $count = $result->num_rows;
                    if ($count == 0) {
                        $sql = "INSERT INTO employee (First_name,Last_name,Address,Phone_number)
                                VALUES ('$_POST[name]', '$_POST[surname]', '$_POST[address]','$_POST[phone_number]')";
                        if (mysqli_query($conn, $sql)) {
                            $employee_id = mysqli_insert_id($conn);
                            $sql2 = "INSERT INTO users (Role,Email,Password,Employee_ID,Customer_ID)
                                VALUES ('02', '$_POST[email]', '$_POST[password]', " . $employee_id . ",null)";
                            if (mysqli_query($conn, $sql2)) {
                                $message = "Επιτυχής εγγραφή νέου εργαζόμενου";
                            } else {
                                $message = "Παρουσιάστηκε κάποιο πρόβλημα, παρακαλω προσπαθήστε ξανά";
                            }
                        } else {
                            $message = "Παρουσιάστηκε κάποιο πρόβλημα, παρακαλω προσπαθήστε ξανά";
                        }
                    } else {
                        $emailErr = "Το email που δώσατε χρησιμοποιείται ήδη";
                    }
                }

                mysqli_close($conn);
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
                <h2>Προσθήκη υπαλλήλου</h2>
            </div>
            <form method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                <div class="form-group row">
                    <label for="name" class="col-4 col-form-label">Όνομα <span class="error">*</span> </label>
                    <div class="col-8">
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo $name; ?>" placeholder="πχ. Κωνσταντίνος">
                        <span class="error"> <?php echo $nameErr; ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="surname" class="col-4 col-form-label">Επώνυμο <span class="error">*</span> </label>
                    <div class="col-8">
                        <input type="text" name="surname" class="form-control" id="surname" value="<?php echo $surname; ?>" placeholder="πχ. Ανδρονής">
                        <span class="error"> <?php echo $SurnameErr; ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-4 col-form-label">E-mail <span class="error">*</span></label>
                    <div class="col-8">
                        <input type="text" name="email" class="form-control" id="email" value="<?php echo $email; ?>" placeholder="πχ. andronis.konst@gmail.com">
                        <span class="error"> <?php echo $emailErr; ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-4 col-form-label">Κωδικός Πρόσβασης <span class="error">*</span></label>
                    <div class="col-8">
                        <input type="password" name="password" class="form-control" id="password" value="<?php echo $password; ?>" placeholder="πχ. myPass1234">
                        <span class="error"><?php echo $passwordErr; ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="cpassword" class="col-4 col-form-label">Επιβεβαίωση Κωδικού Πρόσβασης<span class="error">*</span></label>
                    <div class="col-8">
                        <input type="password" name="cpassword" class="form-control" id="cpassword" value="<?php echo $cpassword; ?>" placeholder="πχ. myPass1234">
                        <span class="error"><?php echo $passwordErr; ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address" class="col-4 col-form-label">Διεύθυνση</label>
                    <div class="col-8">
                        <input type="text" name="address" class="form-control" placeholder="πχ. Αριστοτέλους 88">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone_number" class="col-4 col-form-label">Τηλέφωνο</label>
                    <div class="col-8">
                        <input type="text" name="phone_number" class="form-control" placeholder="πχ. 999000111">
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
