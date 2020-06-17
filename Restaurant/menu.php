<div class="container menu">
    <div class="row">
        <div class="col menu-item">  
            <a href="/Restaurant/index.php">Αρχική</a>
        </div>
        <div class="col menu-item">
            <a href="/Restaurant/Catalogue.php">Το μενού μας</a>
        </div>
        <div class="col menu-item">
            <a href="/Restaurant/Reservation_date.php">Κράτηση</a>
        </div>
        <div class="col menu-item">
            <a href="AboutUs.php">Σχετικά με εμάς</a>
        </div>
        <div class="col menu-item">
            <a href="/Restaurant/login.php">Login / Δημιουργία λογαριασμού</a>
        </div>
    </div>


<?php
if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == '02') {
    include 'EmployeeMenu.php';
}
if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == '01') {
    include 'CustomerMenu.php';
}
?>

</div>