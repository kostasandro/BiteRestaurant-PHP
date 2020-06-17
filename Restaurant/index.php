<?php
// Start the session
session_start();
?>
<html>
    <head> 
        <meta charset="utf-8">
        <title>Restaurant</title>
        <link rel="stylesheet" type="text/css" media="all" href="./Css/styles.css">
        <link rel="stylesheet" type="text/css" media="all" href="./libraries/bootstrap-4.3.1-dist/css/bootstrap.min.css">
        <script src="./libraries/Jquery/jquery-3.3.1.min.js"></script>   
        <script src="./libraries/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php include 'header.php'; ?>
        <?php include 'menu.php'; ?>
        <?php include 'Carousel.php'; ?>
        <?php include 'footer.php'; ?>

        
    </body>
</html>
