
<div class="header" >
    <div class="row" >
        <div class="col-sm">
            <a href="index.php" alt="Αρχική">
                <span class="name">bite</span>
                <span class="sub">Fine dining</span>
            </a>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <?PHP
            if (isset($_SESSION["user_first_name"])) {
                echo "Hello, ". $_SESSION["user_first_name"] . "&nbsp".
                ' <a href="logout.php" class="logout"> Log Out</a>';
            }
            ?> 
        </div>
    </div>
</div>