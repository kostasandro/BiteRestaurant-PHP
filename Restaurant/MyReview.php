<?php
// Start the session
session_start();
?>
<html>
    <head>
        <title>Αξιολόγηση Εστιατορίου</title>
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
        $commentErr = $message = "";

        //μεταβλητές για την σύνδεση με την βάση
        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $dbname = "restaurant";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //έλεγχος συμπλήρωσης πεδίου σχόλια
            if (empty($_POST["comment"])) {
                $commentErr = "Γράψτε την γνώμη σας";
            }

            if ($commentErr == "") {
                //δημιουργία connection
                $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
                //έλεγχος connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                
                //προσθήκη εγγραφής στον πίνακα review
                $sql = "INSERT INTO review (Customer_ID, Comment, Date) VALUES (" . $_SESSION["customer_id"] . ",'" . $_POST["comment"] . "', NOW())";
                if (mysqli_query($conn, $sql)) {
                    $message = "Ευχαριστούμε για τα σχόλια";
                } else {
                    $message = "Προέκυψε πρόβλημα, προοσπαθήστε ξανά";
                }
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
                <h2>Αξιολόγηση Εστιατορίου</h2>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                <div class="form-group">
                    <label for="comment">Σχόλια</label> <span class="error">* <?php echo $commentErr; ?></span>
                    <textarea class="form-control" rows="4" name="comment"></textarea>            
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>     
            </form>
        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>
