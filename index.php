<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
//    session_destroy()

    if(isset($_POST['post'])) {
        $post = new Post($con, $userLoggedin);
        $post->SubmitPost($_POST['post_text'], "none");
    }

?>
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
                    echo "<br> Likes" . $user['num_likes'];
                ?>
            </div>
        </div>

        <div class="main_column column">
            <form class="post_form" action="index.php" method="POST">
                <textarea name="post_text" id="post_text" placeholder="Got something to say ?"> </textarea>
                <input type="submit" name="post" id="post_button" value="Post">
                <hr>

            </form>

        </div>


    </div>
</body>
</html>