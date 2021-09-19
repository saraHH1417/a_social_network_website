<?php
include_once("includes/header.php");
include_once ("includes/form_handlers/settings_handler.php");
?>

<div class="main_column column">
    <h4> Account settings</h4>
    <img src="<?php  echo $user['profile_pic']?>" id="setting_profile_pic">
    <br>
    <a href="upload.php"> Upload new profile picture</a>
    <br><br><br><hr>
    <h4>Modify the values and click 'Update Details'</h4>
    <?php
        $user_data_query = mysqli_query($con , "select first_name , last_name , email from users where 
                                                       username='$userLoggedin' ");
        $row = mysqli_fetch_array($user_data_query);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];
    ?>
    <form action="settings.php" method="post">
        First Name: <input type="text" name="first_name" value="<?php echo $first_name?>" class="settings_input"><br>
        Last Name: <input type="text" name="last_name" value="<?php echo $last_name?>" class="settings_input"><br>
        Email: <input type="text" name="email" value="<?php echo $email?>" class="settings_input" ><br>
        <?php echo $message; ?>
        <input type="submit" name="update_details" id="save_details" value="Update Details" class="info settings_submit">
    </form>

    <h4> Change Password</h4>
    <form action="settings.php" method="post">
        Old Password: <input type="password" name="old_password" class="settings_input"><br>
        New Password: <input type="password" name="new_password_1" class="settings_input"><br>
        Nwe Password Again: <input type="password" name="new_password_2" class="settings_input"><br>
        <?php echo $password_message; ?>
        <input type="submit" name="update_password" id="save_password" value="Update Password" class="info settings_submit">
    </form>

    <h4> Close Account</h4>
    <form action="settings.php" method="post">
        <input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
    </form>
</div>
