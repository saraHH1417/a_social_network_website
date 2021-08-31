<?php
include("../../config/config.php");
include("../../includes/classes/User.php");
include("../../includes/classes/Post.php");

$limit = 10; // Number of posts that can be loaded per  call

$posts = new Post($con , $_REQUEST['userLoggedin'] );
$posts->loadPostFriends($_REQUEST, $limit);