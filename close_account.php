<?php
include_once ("includes/header.php");

if(isset($_POST['cancel'])) {
    header("Location:settings.php");
}
if(isset($_POST['close_account'])) {
    $close_query = mysqli_query($con , "update users set user_closed='yes' where username='$userLoggedin'");
    session_destroy();
    header("Location:register.php");
}
?>

<div class="main_column column">
    <h4> Close Account</h4>
    Are you sure you want to close your account ?<br><br>
    Closing your account will hide your profile and all your activity from other users.<br><br>
    You can re-open your account at any time by simply logging in.<br><br>

    <form action="close_account.php" method="post">
        <input type="submit" name="close_account"  class="danger settings_submit" value="Yes! Close it!">
        <input type="submit" name="cancel"  class="info settings_submit" value="No way!">

    </form>
</div>

