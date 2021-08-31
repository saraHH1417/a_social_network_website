<?php

// Declaring variables to prevent errors
$fname = ""; //First name
$lname= ""; //Last name
$em=""; //email
$em2=""; // email 2
$password= ""; //password
$password2= ""; //password 2
$date = ""; // sign up date 
$error_array= array(); //holds error massages
if (isset($_POST['register_button'])){
    // Registeration form values

    //First name
    $fname = strip_tags($_POST['reg_fname']); // Remove html tags
    $fname = str_replace(" ", "", $fname); // Remove spaces
    $fname = ucfirst(strtolower($fname)); // first lowercases all alphabet then capitalize first letter
    $_SESSION['reg_fname'] = $fname ; // stores variable into session variable

    //Last name
    $lname = strip_tags($_POST['reg_lname']); // Remove html tags
    $lname = str_replace(" ", "", $lname); // Remove spaces
    $lname = ucfirst(strtolower($lname)); // first lowercases all alphabet then capitalize first letter
    $_SESSION['reg_lname'] = $lname ; // stores variable into session variable  

    //Email
    $em = strip_tags($_POST['reg_email']); // Remove html tags
    $em = str_replace(" ", "", $em); // Remove spaces
    $em = ucfirst(strtolower($em)); // first lowercases all alphabet then capitalize first letter
    $_SESSION['reg_email'] = $em ; // stores variable into session variable

    //Email2
    $em2 = strip_tags($_POST['reg_email2']); // Remove html tags
    $em2 = str_replace(" ", "", $em2); // Remove spaces
    $em2 = ucfirst(strtolower($em2)); // first lowercases all alphabet then capitalize first letter
    $_SESSION['reg_email2'] = $em2 ; // stores variable into session variable
    
    //passwords
    $password = strip_tags($_POST['reg_password']); // Remove html tags
    $password2 = strip_tags($_POST['reg_password2']); // Remove html tags

    $date = date("Y-m-d"); // current date

    if($em == $em2) {

        // check if email is in valid format
        if(filter_var($em, FILTER_VALIDATE_EMAIL)){
            
            $em = filter_var($em, FILTER_VALIDATE_EMAIL);
            
            // check if email already exists
            $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

            // count number of rows returned
            $num_rows= mysqli_num_rows($e_check);

            if($num_rows > 0) {
                array_push($error_array, "Email already in use <br>");
            }

        }
        else{
            array_push($error_array, "Invalid format fr email <br>");
        }

    }
    else {
        array_push($error_array, "Emails don't match <br>");
    }
    
    // check the length of first and last name
    if(strlen($fname) > 25 || strlen($fname) < 2) {
        array_push($error_array, "Your first name should be between 2 and 25 characters<br>");
    }

    if(strlen($lname) > 25 || strlen($lname) < 2) {
        array_push($error_array, "Your last name should be between 2 and 25 characters<br>");
    }
    
    if($password != $password2) {
        array_push($error_array, "Passwords don't match<br>");
    }
    else {
        if(preg_match('/[^A-Za-z0-9]/',$password)){

            array_push($error_array, "Password can only contain numbers and characters <br>");
        }

    }
    if(strlen($password) > 30 || strlen($password) < 2){
        array_push($error_array, "Password should be between 2 and 30 characters <br>");
    }
    if(empty($error_array)){
        $password = md5($password); // encrypting password before sending to database

        // generating username by concatenating first name and last name
        $username = strtolower($fname. "-". $lname);
        $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username ='$username' ");
        $i = 0;
        while(mysqli_num_rows($check_username_query)){
            $i++;
            $username = $username ."-". $i;
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username ='$username' ");
        }
         // Profile picture assignment
        $rand = rand(1,2);
        switch($rand) {
            case 1 :
                $profile_pic = "./assets/images/profile_pics/defaults/head_red.png";
            case 2 :
                $profile_pic = "./assets/images/profile_pics/defaults/head_sun_flower.png"; 
        }

        $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username' , '$em', '$password', '$date', '$profile_pic' , '0' , '0' , 'no' , ',')");
        array_push($error_array, "<span style='color:#14C800; font-weight= bold'> You are all set. go ahead and login</span><br>");
        $_SESSION["reg_fname"] = "";
        $_SESSION["reg_lname"] = "";
        $_SESSION["reg_email"] = "";
        $_SESSION["reg_email2"] = "";
    }

}
?>