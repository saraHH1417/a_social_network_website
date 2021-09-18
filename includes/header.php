<?php

require_once"config/config.php";
include_once("includes/classes/User.php");
include_once("includes/classes/Post.php");
include_once("includes/classes/Message.php");
include_once("includes/classes/Notification.php");


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
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/demo.js"></script>
    <script src="assets/js/jquery.jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>

    <!--    CSS-->
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
</head>
<body>
    <div class="top-bar">

        <div class="logo">
            <a href="index.php">Swirlfeed!</a>
        </div>

        <div class="search">
            <form action="search.php" method="GET" name="search_form">
                <div class="div_nav_search_users">
                    <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedin;?>')" name="q"
                           placeholder="Search for Users" autocomplete="off" id="search_text_input">
                    <button type="submit" class="search_button">
                        <img src="assets/images/icons/magnifying_glass.png">
                    </button>
                </div>
            </form>

            <div class="search_results">

            </div>
            <div class="search_results_footer_empty">

            </div>
        </div>

        <nav>

            <?php
                // Unread messages
                $messages = new Message($con , $userLoggedin);
                $num_new_messages = $messages->getUnreadNumber();

                // Unread notifications
                $notifications = new Notification($con , $userLoggedin);
                $num_new_notifications = $notifications->getUnreadNumber();

                // Unread friend request
                $user_obj = new User($con , $userLoggedin);
                $num_new_friend_requests = $user_obj->getNumberOfFriendRequests();
            ?>

            <a href="<?php echo $userLoggedin; ?>">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="index.php">
                <i class="fa fa-home" aria-hidden="true"></i>
            </a>
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedin; ?>' , 'message')">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <?php
                    if($num_new_messages > 0 ) {
                        echo "<span class='notification_badge' id='unread_message'>" . $num_new_messages . "</span>";
                    }
                ?>
            </a>
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedin; ?>' , 'notification')">
                <i class="fa fa-bell" aria-hidden="true"></i>
                <?php
                if($num_new_notifications > 0 ) {
                    echo "<span class='notification_badge' id='unread_notification'>" . $num_new_notifications . "</span>";
                }
                ?>
            </a>
            <a href="requests.php">
                <i class="fa fa-users" aria-hidden="true"></i>
                <?php
                if($num_new_friend_requests > 0 ) {
                    echo "<span class='notification_badge' id='unread_notification'>" . $num_new_friend_requests . "</span>";
                }
                ?>
            </a>
            <a>
                <i class="fa fa-cog" aria-hidden="true"></i>
            </a>

            <a href="includes/handlers/logout.php">
                <i class="fa fa-sign-out" aria-hidden="true"></i>
            </a>
        </nav>
        <div class="dropdown_data_window" style="height:0px; border:none"> </div>
        <input type="hidden" id="dropdown_data_type" value="">
    </div>

    <script>
        $(document).ready(function () {
            let userLoggedin = "<?php echo $userLoggedin; ?>";
            $('.dropdown_data_window').scroll(function () {
                let scrollHeight = $('.dropdown_data_window')[0].scrollHeight;
                let scrollPos = $('.dropdown_data_window').innerHeight() + $('.dropdown_data_window')[0].scrollTop;
                // pageY0ffset means window.scrollTop()
                let page = $('.dropdown_data_window').find('.nextPageDropDownData').val();
                let noMoreDropDownData = $('.dropdown_data_window').find('.noMoreDropDownData').val();
                if ((scrollPos >= scrollHeight) && noMoreDropDownData == 'false') {

                    let pageName; // name of the page that ajax sends request to
                    let type = $('#dropdown_data_type').val();

                    if(type == 'notification')
                        pageName = "ajax_load_notifications.php";
                    else if(type == 'message')
                        pageName = "ajax_load_messages.php";


                    let ajaxReq = $.ajax({
                        url: "includes/handlers/" + pageName,
                        type: "POST",
                        data: "page=" + page + "&userLoggedin=" + userLoggedin,
                        cache: false,

                        success: function (response) {
                            $(".dropdown_data_window").find('.nextPageDropDownData').remove();
                            $(".dropdown_data_window").find('.noMoreDropDownData').remove();

                            // the below part was so hard for me  to get it right finally
                            // so important
                            if(!($('.noMoreMessage').text().indexOf('No more messages to load!') > -1) &&
                                !($('.noMoreMessage').text().indexOf('No more notifications to load!') > -1)){
                                $(".dropdown_data_window").append(response);
                            }

                        }
                    });
                } // End if
                //return false; It was in the course , but I didn't know the meaning so I commented it.
            }); // end $(window).scroll(function() {
        })
    </script>
<div class="wrapper">
