<?php 
require "config/config.php";
require "includes/form_handlers/register_handler.php";
require "includes/form_handlers/login_handler.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
    <script src="assets/js/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>
</head>
<body>
    <?php
    if(isset($_POST["register_button"])) {
        echo '
                <script>
                    $(document).ready(function() {
                        $("#first").hide();
                        $("#second").show();
                    })
               </script>
                ';
    }
    ?>

    <div class="wrapper">

        <div class="login_box">
            <div class="login-header">

                <h1>SwirlFeed!</h1>
                Login or sign up below

            </div>

            <div id="first" class="first">

                <form action="register.php" method="POST">
                    <input type="email" name="log_email" placeholder="Email Address" value="<?php 
                    if(isset($_SESSION['log_email'])){
                        echo $_SESSION['log_email'];
                    }
                    ?>" required>
                    <br>
                    <input type="password" name="log_password" placeholder="Password" required>
                    <br>
                    <?php if(in_array("Email or password is incorrect" , $error_array)) {
                        echo "<br><span style='color:#14C800; font-weight= bold'> Email or password is incorrect</span><br>"; } ?>
                    <input type="submit" name="login_button" value="Login">
                    <br>
                    <a href="#" id="signup" class="signup"> Need an account? Sign up here. </a>
                </form>

            </div>

            <div id="second" class="second">

                <form action="register.php" method="POST">

                    <input type="text" name="reg_fname" placeholder="First name" value="<?php 
                    if(isset($_SESSION['reg_fname'])){
                        echo $_SESSION['reg_fname'];
                    }
                    ?>" required>
                    <br>
                    <?php 
                        if(in_array("Your first name should be between 2 and 25 characters<br>", $error_array)) {
                             echo "Your first name should be between 2 and 25 characters<br>";
                        }
                    ?>
                    <input type="text" name="reg_lname" placeholder="First name" value="<?php 
                    if(isset($_SESSION['reg_lname'])){
                        echo $_SESSION['reg_lname'];
                    }
                    ?>" required>
                    <br>
                    <?php 
                        if(in_array("Your last name should be between 2 and 25 characters<br>", $error_array)){
                             echo "Your last name should be between 2 and 25 characters<br>";
                        }
                    ?>
                    <input type="email" name="reg_email" placeholder="Email" value="<?php 
                    if(isset($_SESSION['reg_email'])){
                        echo $_SESSION['reg_email'];
                    }
                    ?>" required>
                    <br>
                    <input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php 
                    if(isset($_SESSION['reg_email2'])){
                        echo $_SESSION['reg_email2'];
                    }
                    ?>" required>
                    <br>
                    <?php 
                        if(in_array("Email already in use <br>", $error_array)) echo "Email already in use <br>";
                        else if(in_array("Invalid format fr email <br>", $error_array)) echo "Invalid format fr email <br>";
                        else if(in_array("Emails don't match <br>", $error_array)) echo "Emails don't match <br>";
                    ?>
                    <input type="password" name="reg_password" placeholder="Password" required>
                    <br>
                    <input type="password" name="reg_password2" placeholder="Confirm password" required>
                    <br>
                    <?php 
                        if(in_array("Invalid format fr email <br>", $error_array)) echo "Invalid format fr email <br>";
                        else if(in_array("Password can only contain numbers and characters <br>", $error_array))
                            {
                                echo "Password can only contain numbers and characters <br>";
                            }
                        else if(in_array("Password should be between 2 and 30 characters <br>", $error_array)) {
                            echo "Password should be between 2 and 30 characters <br>";
                        }
                    ?>
                    <input type="submit" name="register_button" value="Register">
                    <br>
                    <?php 
                        if(in_array("<span style='color:#14C800; font-weight= bold'> You are all set. go ahead and login</span><br>",$error_array))
                        {
                            echo "<span style='color:#14C800; font-weight= bold'> You are all set. go ahead and login</span><br>";
                        }
                    ?>
                    <a href="#" id="signin" class="signin" > Already have an account? Sign in here  </a>

                </form>

            </div>
        </div>
    </div>
</body>
</html>