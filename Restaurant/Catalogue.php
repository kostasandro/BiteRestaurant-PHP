<?php
// Start the session
session_start();
?><html>
    <head> 
        <meta charset="utf-8">
        <title>Το μενού μας</title>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        <div class="container page">
            <div>
                <h2>Το μενού μας</h2>
            </div>
            <div class="row justify-content-md-center" style="margin-bottom:20px; margin-top: 20px; ">
                <div class="col col-12">
                    <img src="./images/catalogue.jpg" style="max-width: 100%" alt="Italian Trulli">               
                </div>
            </div> 
        </div>

        <?php include 'footer.php'; ?>


    </body>
</html>