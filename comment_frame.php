<?php

    require_once "config/config.php";
    include_once("includes/classes/User.php");
    include_once("includes/classes/Post.php");


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
    <title></title>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>
<body>
    <style type="text/css">
        * {
            font-size: 12px;
            font-family: Arial, Helvetica, Sans-serif;
        }
    </style>

<!--    <script>-->
<!--        function toggle() {-->
<!--            let element = document.getElementById("comment_section");-->
<!---->
<!--            if(element.style.display == "block") {-->
<!--                element.style.display = "none";-->
<!--            }-->
<!--            else {-->
<!--                element.style.display = "block";-->
<!--            }-->
<!--        }-->
<!--    </script>-->

    <?php
        // Get id of the post
        if(isset($_GET["post_id"])) {
            $post_id = $_GET['post_id'];
        }

        $user_query = mysqli_query($con , "SELECT added_by,user_to FROM posts WHERE id='$post_id'");
        $row = mysqli_fetch_array($user_query);

        $posted_to = $row['added_by'];

        if(isset($_POST['postComment'. $post_id]) )  {
            $post_body = $_POST['post_body'];
            $post_body = mysqli_escape_string($con , $post_body);
            $date_time_now = date("Y-m-d H:i:s");

            if (trim($post_body) == "") {
                echo "Comment can not be empty , there is a bug here fix it. this message should disappear when you toggle the commentsection page";
            }else {
                echo "Comment is posted successfully";
                $insert_comment = mysqli_query($con, "INSERT INTO  comments VALUES ('' , '$post_body' , '$userLoggedin' , '$posted_to',
                                                '$date_time_now', 'no' , '$post_id') ");
            }
        }
    ?>

    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST" >
        <textarea name="post_body" > </textarea>
        <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post" >
    </form>

<!--    Load comments-->
    <?php
        $get_comments = mysqli_query($con , "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC ");
        $count = mysqli_num_rows($get_comments);

        if($count != 0) {
            while($comment = mysqli_fetch_array($get_comments)) {
                $comment_body = $comment['post_body'];
                $posted_to = $comment['posted_to'];
                $posted_by = $comment['posted_by'];
                $date_added = $comment['date_added'];
                $removed = $comment['removed'];


                // Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_added);
                $end_date = new DateTime($date_time_now);
                $interval = $start_date->diff($end_date);

                // print time interval
                if ($interval->y >= 1) {
                    if ($interval->y == 1) {
                        $time_msg = $interval->y + " year ago";
                    } else {
                        $time_msg = $interval->y + " years ago";
                    }
                } elseif ($interval->m >= 1) {
                    if ($interval->d == 1) {
                        $days = $interval->d . " day ago";
                    } else {
                        $days = $interval->d . " days ago";
                    }
                    if ($interval->m == 1) {
                        $time_msg = $interval->m . " month" . $days;
                    } else {
                        $time_msg = $interval->m . " months ago" . $days;
                    }
                } elseif ($interval->d >= 1) {
                    if ($interval->d == 1) {
                        $time_msg = $interval->d . " day ago";
                    } else {
                        $time_msg = $interval->d . " days ago";
                    }
                } elseif ($interval->h >= 1) {
                    if ($interval->h == 1) {
                        $time_msg = $interval->h . " hour ago";
                    } elseif ($interval->h > 1) {
                        $time_msg = $interval->h . " hours ago";
                    }
                } elseif ($interval->i >= 1) {
                    if ($interval->i == 1) {
                        $time_msg = $interval->i . " minute ago";
                    } else {
                        $time_msg = $interval->i . " minutes ago";
                    }
                } else {
                    $time_msg = 'just now';
                }

                $user_obj = new User($con , $posted_by);
    ?>
            <div class="comment_section">
                <hr>
                <a href="<?php echo $posted_by; ?>" target="_parent" >
                    <img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by;?>">
                </a>
                <a href="<?php echo $posted_by; ?>" target="_parent" >
                    <b><?php echo $user_obj->GetFirstLastName()?></b>
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo $time_msg . "<br>" . $comment_body ; ?>
            </div>
    <?php
            }
        }
        else {
            echo "<center><br><br> No comments to show! </center>";
        }
    ?>
</body>
</html>
