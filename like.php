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
    <style>
        *{
            font-family: Arial, Helvetica, Sans-serif ;
        }
        body {
            background-color: #fff;
        }
        form {
            position: absolute;
            top:0;
        }

    </style>
    <?php
    //Get id of the post
    if(isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
    }

    $get_likes = mysqli_query($con , "SELECT likes, added_by FROM posts WHERE id='$post_id'");
    $row = mysqli_fetch_array($get_likes);
    $total_likes_of_post = $row['likes'];
    $user_has_been_liked = $row['added_by'];

    $user_details_query = new User($con , $user_has_been_liked);
    $total_user_likes = $user_details_query->getNumLikes();

    // like button

    if(isset($_POST['like_button'])) {
        $total_likes_of_post ++;
        $total_user_likes++;
        $query = mysqli_query($con , "UPDATE posts set likes='$total_likes_of_post' WHERE id='$post_id' ");
        $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_has_been_liked' ");
        $insert_like = mysqli_query($con , "INSERT INTO likes values('', '$userLoggedin', '$post_id' )");
    }

    // unlike button
    if(isset($_POST['unlike_button'])) {
        $total_likes_of_post --;
        $total_user_likes--;
        $query = mysqli_query($con , "UPDATE posts set likes='$total_likes_of_post' WHERE id='$post_id' ");
        $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_has_been_liked' ");
        $insert_like = mysqli_query($con , "DELETE FROM likes WHERE username='$userLoggedin' AND post_id='$post_id' ");
    }

    //check for previous likes
    $check_query = mysqli_query($con , "SELECT * FROM likes WHERE username='$userLoggedin' AND post_id='$post_id'");
    $num_rows = mysqli_num_rows($check_query);

    if($num_rows == 1 ) {
        echo "<form action='like.php?post_id=$post_id' method='POST'>
                <input type='submit' class='comment_like' name='unlike_button' value='Unlike' >
                <div class='like_value'>
                    Likes($total_likes_of_post)
                </div>
              </form>";
    }else if($num_rows == 0){
        echo "<form action='like.php?post_id=$post_id' method='POST'>
                <input type='submit' class='comment_like' name='like_button' value='Like' >
                <div class='like_value'>
                    Likes($total_likes_of_post)
                </div>
              </form>";
    } else {
        echo " ----";
    }
    ?>
</body>
</html>