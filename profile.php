<?php
    include_once("includes/header.php");
    include_once("includes/classes/User.php");
    include_once("includes/classes/Post.php");
    if(isset($_GET['profile_username'])) {
        $username = $_GET['profile_username'];
        $user_details = new User($con , $username);
        $num_friends = (substr_count($user_details->getFriendArray() , ","));
        if($num_friends <0) {
            $num_friends = 0;
        }
    }
    $message_obj = new Message($con , $userLoggedin);
    $Logged_in_user_obj = new User($con , $userLoggedin);

    if(isset($_POST['remove_friend'])) {
        $Logged_in_user_obj->removeFriend($username);
        $num_friends--;
        if($num_friends <0) {
            $num_friends = 0;
        }

    }
    elseif(isset($_POST['add_friend'])) {
        $Logged_in_user_obj->sendFriendRequest($username);
    }
    elseif(isset($_POST['respond_request'])) {
        header("Location:requests.php");
    }
// this is another way for message tabs that has one problem , after sending message the page sends the message again; and needs a header
// but i don't know how to set header that page goes to the messages tab
//    if(isset($_POST['post_message'])) {
//
//        if(isset($_POST['message_body'])) {
//            $body = mysqli_real_escape_string($con , $_POST['message_body']);
//            $date = date("Y-m-d H:i:s");
//            $message_obj->sendMessage($username, $body, $date);
//        }
//
//        $link = '#profileTabs a[href="#messages_div"]';
//        echo  "<script>
//                    $(function() {
//                        $('". $link ."').tab('show');
//                    })
//                </script>";
//    }

?>
<style type="text/css">
    .wrapper {
        margin-left: 0;
        padding-left: 0;
    }
</style>
<script>
    document.getElementsByClassName('profile-left').height = document.height;
</script>

        <div class="profile-left">
            <img src="<?php echo $user_details->getProfilePic() ?>" alt="image not found">
            <div class="profile_info">
                <p> <?php echo "Name: " . $user_details->GetFirstLastName()?></p>
                <p><?php echo "Posts: " . $user_details->GetNumPosts() ;  ?></p>
                <p><?php echo "Likes: " . $user_details->getNumLikes() ;  ?></p>
                <p><?php echo "Friends: " .  $num_friends;  ?></p>
            </div>

            <form action="<?php echo $username; ?>" method="POST">
                <?php
                    $profile_user_obj = new User($con , $username);
                    if($profile_user_obj->isClosed()) {
                        Location("header: user_closed.php");
                    }

                    $Logged_in_user_obj_new = new User($con , $userLoggedin);
                    if($userLoggedin != $username) {
                        if($Logged_in_user_obj_new->isFriend($username)) {
                            echo "<input type='submit' name='remove_friend' class='danger' value='Remove Friend'>";
                        }
                        elseif ($Logged_in_user_obj_new->didReceiveRequest($username)) {
                            echo "<input type='submit' name='respond_request' class='warning' value='Respond Request'>";

                        }
                        elseif($Logged_in_user_obj_new->didSendRequest($username)) {
                            echo "<input type='submit' name='request_sended' class='default' value='Request pending'>";
                        }
                        else {
                            echo "<input type='submit' name='add_friend' class='success' value='Add Friend'>";
                        }
                    }
                ?>

            </form>

                <!--Modal trigger button-->
                <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post something">

                <div class="profile_info_buttom">
                    <?php
                    $mutual_friends = $Logged_in_user_obj->getMutualFriends($username);
                    echo "<br>" . $mutual_friends . " Mutual friends"
                    ?>
                </div>
        </div>

        <div class="profile_main_column column">
<!--            Bootstrap tabs-->
            <ul class="nav nav-tabs" role="tablist" id="profileTabs">
                <li class="nav-item active">
                    <a class="nav-link active" aria-controls="newsfeed_div" href="#newsfeed_div" role="tab" data-toggle="tab">
                        Newsfeed
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-controls="about_div" href="#about_div" role="tab" data-toggle="tab">
                        About
                    </a>
                </li>
                <li class="nav-item" id="message_tab">
                    <a class="nav-link"  aria-controls="messages_div" href="#messages_div" role="tab" data-toggle="tab">
                        Messages
                    </a>
                </li>
            </ul>


            <div class="tab-content">

                <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
                    <div class="posts_area"></div>
                    <img id="loading" src="assets/images/icons/loading.gif">
                </div>

                <div role="tabpanel" class="tab-pane fade" id="about_div">
                    <h1>hi</h1>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="messages_div">
                    <iframe src="profile_messages_frame.php?profile_username=<?php echo $username; ?>" id="iframe_profile_messages">
                    </iframe>

                </div>

            </div>

      </div>
            <!-- Modal -->
            <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="exampleModalCenterTitle">Post something!</h5>
                        </div>
                        <div class="modal-body">
                            <p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>

                            <form class="profile_post" action="" method="POST">
                                <div class="form_group">
                                    <textarea class="form_control" name="post_body" ></textarea>
                                    <input type="hidden" name="user_from" value="<?php echo $userLoggedin?>" >
                                    <input type="hidden" name="user_to" value="<?php echo $username?>" >
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
                        </div>
                    </div>
                </div>
            </div>


<script>
    let userLoggedin = '<?php echo $userLoggedin; ?>';
    let profileUsername = '<?php echo $username; ?>';

    if(userLoggedin == profileUsername) {
        $('#message_tab').hide();
    }
    $(document).ready(function () {
        $('#loading').show();

        // original ajax request for loading first posts
        $.ajax({
            url: "includes/handlers/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=1&userLoggedin=" + userLoggedin + "&profileUsername=" + profileUsername ,
            cache: false,

            success: function (data_load) {
                $("#loading").hide();
                $(".posts_area").html(data_load);
            }
        });

        $(window).scroll(function () {
            // let height = $(".posts_area").height(); // Div containing posts
            // let scroll_top = $(this).scrollTop();
            let scrollHeight = $(document).height();
            let scrollPos = $(window).height() + window.pageYOffset; // pageY0ffset means window.scrollTop()
            let page = $('.posts_area').find('.nextPage').val();
            let noMorePosts = $('.posts_area').find('.noMorePosts').val();


            if ((scrollHeight - scrollPos < 2) && noMorePosts == 'false') {
                $('#loading').show();

                let ajaxReq = $.ajax({
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedin=" + userLoggedin + "&profileUsername=" + profileUsername ,
                    cache: false,

                    success: function (response) {
                        $(".posts_area").find('.nextPage').remove();
                        $(".posts_area").find('.noMorePosts').remove();
                        $("#loading").hide();
                        $(".posts_area").append(response);
                    }
                });
            } // End if
            //return false; It was in the course , but I didn't know the meaning so I commented it.
        }); // end $(window).scroll(function() {
    });
</script>

    </div>
</body>
</html>

