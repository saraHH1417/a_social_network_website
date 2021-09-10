<?php
    require_once"../../config/config.php";
    include_once("../classes/User.php");
    include_once("../classes/Post.php");

    if(isset($_POST['post_body'])) {
        $post = new Post($con , $_POST['user_from']);
        $post->SubmitPost($_POST['post_body'] , $_POST['user_to']);
    }