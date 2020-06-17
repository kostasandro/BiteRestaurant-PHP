<html>
    <head>
        <style>
            .error {color: #FF0000;}
        </style>
        <style>.error {color: #FF0000;}
        </style>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
        <script>

            $(document).ready(function () {
                editTable = function (id, capacity) {
                    $("#capacity").val(capacity);
                    $("#table_number").val(id);
                }
                createTable = function () {
                    $("#capacity").val("");
                    $("#table_number").val("");
                }
            }
            );




        </script>
    </head>
    <body>  
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "restaurant";

        $capacity = $capacity_err = "";

        function getData() {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "restaurant";

// Create connection     
            $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM res_table";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                $table_data = "";
                while ($row = $result->fetch_assoc()) {
                    $table_data = $table_data .
                            "<tr>"
                            . "<td scope='row'>" . $row["Table_ID"] . "</td>"
                            . "<td>" . $row["Capacity"] . "</td>"
                            . "<td><input type='button' class='btn btn-secondary btn-sm' onclick='editTable(" . $row["Table_ID"] . "," . $row["Capacity"] . ")' value='edit'></td>"
                            . "</tr>";
                }
            }
            $conn->close();
            return $table_data;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST["capacity"])) {
                $capacity_err = "Number of persons required";
            } else {
                $capacity = test_input($_POST["capacity"]);
                if ($capacity < 1 || $capacity > 10) {
                    $capacity_err = "Number of person must be between 1-10";
                }
            }
            if ($capacity_err == "") {
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($_POST["table_number"] != "") {
                    $sql = "UPDATE res_table SET Capacity = '$_POST[capacity]' 
                            WHERE Table_ID = '$_POST[table_number]';";
                    if (mysqli_query($conn, $sql)) {
                        echo "table updated";
                    } else {
                        echo "table not updated";
                    }
                } else {
                    $sql = "INSERT INTO res_table (Capacity)
                            VALUES ('$_POST[capacity]')";
                    if (mysqli_query($conn, $sql)) {
                        echo "table added";
                    } else {
                        echo "table not added";
                    }
                }
                $conn->close();
            }
        }

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $table_data = getData();
        ?>

        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        <div class="container">
            <div class="Login"><h2>Tables Configuration</h2></div>
            <div class="row">
                <div class="col-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Table Number</th>
                                <th scope="col">Capacity</th> 
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $table_data ?>
                            <tr>
                                <td scope="row">

                                </td>
                                <td>
                                </td>
                                <td>
                                    <input type="button" value="insert"  onclick='createTable()' class="btn btn-secondary btn-sm">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm">
                    <h4>Add New Table</h4>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
                        <div class="form-group">
                            <label for="table_number">Table Number</label> <span class="error">* </span>
                            <input type="text" name="table_number" class="form-control" id="table_number" readonly>              
                        </div>
                        <div class="form-group">
                            <label for="capacity">Number of persons</label> <span class="error">* <?php echo $capacity_err; ?></span>
                            <input type="text" name="capacity" class="form-control" id="capacity" placeholder="4">              
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>

            </div>
        </div>       
        <?php include 'footer.php'; ?>  
    </body>

</html>
