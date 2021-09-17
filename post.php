<?php

include_once ("includes/header.php");


if(isset($_GET['id'])) {
    $post_id = $_GET['id'];
}
else {
    $post_id = 0;
}

//$find_post_query = mysqli_query($con , "select * from posts where $post_id='$post_id'");
//if(mysqli_num_rows($find_post_query) > 0 ){
//    $post = mysqli_fetch_array($find_post_query);
//    $post_body = $post['body'];
//}else {
//    echo "This post has been removed.";
//}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
    <div class="user_details column">
        <a href="<?php echo $userLoggedin; ?>"> <img src="<?php echo $user['profile_pic']; ?>" alt="not available"></a>

        <div class="user_details_left_right">
            <a href="<?php echo $userLoggedin; ?>">
                <?php
                echo $user['first_name'] . " " . $user['last_name'];
                ?>
            </a>
            <?php
            echo "<br> Posts:" . $user['num_posts'];
            echo "<br> Likes:" . $user['num_likes'];
            ?>
        </div>
    </div>

    <div class="main_column column" id="main_column">
        <div class="posts_area">
            <?php
                $post = new Post($con , $userLoggedin);
                $post->getSinglePost($post_id);
            ?>
        </div>
    </div>
</body>
</html>
