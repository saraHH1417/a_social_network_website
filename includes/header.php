<?php

require"config/config.php";
if(isset($_SESSION["username"])) {
    $userLoggedin = $_SESSION["username"];
    $userDetailsQuery = mysqli_query($con , "SELECT * FROM users WHERE username='$userLoggedin'");
    $user = mysqli_fetch_array($userDetailsQuery);
}
else {
    header("Location: register.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!--    Javascript-->
    <script src="./assets/js/jquery/3.5.1/jquery.min.js"></script>
    <script src="./assets/js/bootstrap.js"></script>
    <script src="https://use.fontawesome.com/1420ea7a38.js"></script>

    <!--    CSS-->
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>
<body>
    <div class="top-bar">

        <div class="logo">
            <a href="index.php">Swirlfeed!</a>
        </div>

        <nav>

            <a href="<?php echo $userLoggedin; ?>">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="index.php">
                <i class="fa fa-home" aria-hidden="true"></i>
            </a>
            <a href="#">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </a>
            <a href="#">
                <i class="fa fa-bell" aria-hidden="true"></i>
            </a>
            <a>
                <i class="fa fa-users" aria-hidden="true"></i>
            </a>
            <a>
                <i class="fa fa-cog" aria-hidden="true"></i>
            </a>
            <a>
                <i class="fa fa-logout" aria-hidden="true"></i>
            </a>
            <a>
                <i class="fa fa-sign-out" aria-hidden="true"></i>
            </a>
        </nav>

    </div>
<div class="wrapper">
