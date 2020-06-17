<?php
// Start the session
session_start();
?><!DOCTYPE HTML>  
<html>
    <head>
        <title>Διαχείριση λογαριασμού</title>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>  
        <?php
        //redirect στην αρχική σελίδα αν ο ρόλος του χρήστη που έχει κάνει login δεν είναι τύπου πελάτη
        if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == '02') {
            header('Location: index.php');
        }
        
        // καθορισμός μεταβλητών και άδεισμα τιμών
        $nameErr = $SurnameErr = $emailErr = $passwordErr = $cpasswordErr = $genderErr = "";
        $name = $surname = $password = $cpassword = $email = $gender = $message = "";

        //μεταβλητές για την σύνδεση με την βάση
        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $dbname = "restaurant";

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
                //έλεγχος connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                //έλεγχος αν χρήστης που είναι logged in διαθέτει το συγκεκριμένο email που προσπαθεί να κάνει αλλαγή
                //το αποτέλεσμα πρέπει να είναι μία μόνο εγγραφή
                $sql = "SELECT * FROM USERS WHERE EMAIL = '" . $_POST["email"] . "' AND Customer_ID =" . $_SESSION["customer_id"];
                if ($result = $conn->query($sql)) {
                    $count = $result->num_rows;
                    if ($count == 1) {
                        //ενημέρωση των στοιχείων του πελάτη στον πίνακα των customers
                        $sql = "UPDATE customer 
                                SET First_name='$_POST[name]', Last_name='$_POST[surname]', Gender='$_POST[gender]'"
                                . " WHERE Customer_ID=" . $_SESSION["customer_id"];
                        if (mysqli_query($conn, $sql)) {
                            //ενημέρωση των στοιχείων του πελάτη στον πίνακα των users
                            $sql2 = "UPDATE users 
                                SET Password='$_POST[password]' WHERE Customer_ID=" . $_SESSION["customer_id"];
                            if (mysqli_query($conn, $sql2)) {
                                $message = "Τα στοιχεία του χρήστη άλλαξαν";
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

        // Create connection
        $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        //ανάκτηση των στοιχείων του χρήστη και ανάθεση τους στις μεταβλητές τις φόρμας
        $sql = "SELECT * FROM users U 
                    INNER JOIN customer C
                    ON U.Customer_ID = C.Customer_ID
                    WHERE User_ID=" . $_SESSION["user_id"];
        if ($result = $conn->query($sql)) {
            /* determine number of rows result set */
            if ($result->num_rows > 0) {
                // output data of each row
                while ($row = $result->fetch_assoc()) {
                    $email = (string) $row["Email"];
                    $name = (string) $row["First_name"];
                    $surname = (string) $row["Last_name"];
                    $gender = (string) $row ["Gender"];
                }
            }
            $conn->close();
        }
        ?>

        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>

        <div class="container page">
            <div class="message" style="display:<?php echo $message != "" ? 'block' : 'none' ?>">
                <?php echo $message; ?>
            </div>
            <div class="Login"><h2>Διαχείριση λογαριασμού</h2></div>
            <form method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                <div class="form-group row">
                    <label for="name" class="col-4 col-form-label">Όνομα <span class="error">*</span> </label>
                    <div class="col-8">
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo $name; ?>" placeholder="πχ. Κώνσταντίνος">
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
                        <input type="text" readonly="readonly" name="email" class="form-control" id="email" value="<?php echo $email; ?>" placeholder="πχ. andronis.konst@gmail.com">
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
                    <label for="cpassword" class="col-4 col-form-label">Επιβεβαίωση Κωδικού Πρόσβασης <span class="error">*</span></label>
                    <div class="col-8">
                        <input type="password" name="cpassword" class="form-control" id="cpassword" value="<?php echo $cpassword; ?>" placeholder="πχ. myPass1234">
                        <span class="error"><?php echo $passwordErr; ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="gender" class="col-4 col-form-label">Φύλο </label>
                    <div class="col-8">
                        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "female") echo "checked"; ?>  id="gender" value="female"> Female <br>
                        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "male") echo "checked"; ?>  id="gender" value="male"> Male                 
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
