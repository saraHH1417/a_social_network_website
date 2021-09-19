<?php
if(isset($_POST['update_details'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $check_email = mysqli_query($con , "select username from users where email='$email'");
    $matched_user = mysqli_fetch_array($check_email);
    $matched_user_username = $matched_user['username'];

    if($matched_user_username == "" or $matched_user_username == $userLoggedin) {
        $message = "Details updated. <br><br>";

        $query = mysqli_query($con , "update users set first_name='$first_name',
                            last_name='$last_name', email='$email' where username='$userLoggedin' ");

    }else {
        $message = "This email already exists.<br><br>";
    }
}else {
    $message = "";
}

// ***********************************************************************************
if(isset($_POST['update_password'])) {
    $old_password = md5(strip_tags($_POST['old_password']));
    $new_password_1 = strip_tags($_POST['new_password_1']);
    $new_password_2 = strip_tags($_POST['new_password_2']);

    $check_password = mysqli_query($con, "select id from users where username='$userLoggedin' and 
                                        password='$old_password'");
    $matched_user = mysqli_num_rows($check_password);

    if ($matched_user == 1) {
        if($new_password_1 == $new_password_2) {
            if(strlen($new_password_1) <=3 ) {
                $password_message= "Your password must be at least 4 characters.<br><br>";
            }else {
                $password_message = "Password updated. <br><br>";
                $new_password_md5 = md5($new_password_1);
                $query = mysqli_query($con, "update users set password='$new_password_md5'");
            }
        }else {
            $password_message = "New passwords don't match..<br><br>";
        }
    } else {
        $password_message = "Old Password is wrong.<br><br>";
    }
}else {
    $password_message = "";
}

// ***********************************************************************************
if(isset($_POST['close_account'])) {
    header("Location: close_account.php");
}