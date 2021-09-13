<?php

require_once "config/config.php";
include_once("includes/classes/User.php");
include_once("includes/classes/Post.php");
include_once("includes/classes/Message.php");

if(isset($_SESSION["username"])) {
    $userLoggedin = $_SESSION["username"];
    $userDetailsQuery = mysqli_query($con , "SELECT * FROM users WHERE username='$userLoggedin'");
    $user = mysqli_fetch_array($userDetailsQuery);
}
else {
    header("Location: register.php");
}

if(isset($_GET['profile_username'])) {
    $profile_username = $_GET['profile_username'];

}else {
    header("Location:$userLoggedin");
}
$profile_user_obj = new User($con , $profile_username);
$message_obj = new message($con , $userLoggedin);

    if(isset($_POST['post_message'])) {

        if(isset($_POST['message_body'])) {
            $body = mysqli_real_escape_string($con , $_POST['message_body']);
            $date = date("Y-m-d H:i:s");
            $message_obj->sendMessage($profile_username, $body, $date);
        }
    }

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>

        <!--    js-->
        <script src="./assets/js/jquery/3.5.1/jquery.min.js"></script>
        <script src="./assets/js/bootstrap.js"></script>
        <script src="assets/js/bootbox.min.js"></script>
        <script src="assets/js/demo.js"></script>

        <!-- CSS-->
        <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/style.css">

    </head>
    <body>
        <style>
            body {
                overflow-y: hidden;
            }
        </style>

        <div class="message_main_column_profile_iframe column" id="main_column">
            <?php
            echo "<h4> You and <a href='$profile_username'>".  $profile_user_obj->GetFirstLastName() . "</a></h4><hr><br>";
            echo "<div class='loaded_messages_iframe_profile' id='scroll_messages'>";
            echo $message_obj->getMessages($profile_username);
            echo "</div>";
            ?>

            <div class="message_post_profile_iframe">
                <form method="post">
                    <textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
                    <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
                </form>
            </div>
            <script>
                $(document).ready(function () {
                    const div = document.getElementById("scroll_messages");
                    div.scrollTop = div.scrollHeight;

                })
            </script>
        </div>
    </body>
    </html>
<?php
