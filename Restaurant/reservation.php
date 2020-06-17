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
        if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == '') {
            header('Location: login.php');
        }

        $nameErr = $SurnameErr = $emailErr = $passwordErr = $cpasswordErr = $peopleErr = $hourErr = "";
        $name = $surname = $password = $cpassword = $email = $people = $hour = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            

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
        <div class="Login"><h2>Reservation request</h2></div>
        <form method="post" class="container col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
            <div class="form-group row">
                <label for="people" class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-form-label">Άτομα</label>
                <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8">
                    <input type="text" class="form-control" id="people" value="<?php echo $name; ?>" placeholder="Αριθμός Ατόμων">

                </div>
            </div>
            <div class="form-group row">
                <label for="imerominia" class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-form-label">Ημερομηνία: </label>
                <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8">
                    <input type="date" class="form-control" id="imerominia" value="<?php echo $name; ?>" placeholder="ημερομηνία">

                </div>
            </div>
            <div class="form-group row">
                <label for="gender" class="col-sm-4 col-form-label">Reservation hour</label>
                <div class="col-sm-8">
                    <input type="radio" name="hour" <?php if (isset($hour) && $hour == "afternoon") echo "checked"; ?>  id="hour" value="afternoon"> 18:00-21:00 <br>
                    <input type="radio" name="hour" <?php if (isset($hour) && $hour == "night") ; ?>  id="hour" value="night"> 21:00-24:00                 
                </div>
            </div>
            <div class="form-check">
                <button type="submit" class="btn btn-primary">Submit</button> <br>
            </div>
        </form>
    </body>
</html>
