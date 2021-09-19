<?php
include_once("includes/header.php");
include_once("includes/classes/User.php");
include_once("includes/classes/Post.php");
//    session_destroy()

if (isset($_POST['post'])) {

    if($_POST['post_text'] != "") {
        $uploadOk = 1;
        $imageName = $_FILES['fileToUpload']['name'];
        $errorMessage = "";

        if ($imageName != "") {
            $targetDir = "assets/images/posts/";
            $imageDir = $targetDir . uniqid() . basename($imageName);
            $imageFileType = pathinfo($imageDir, PATHINFO_EXTENSION);

            if ($_FILES['fileToUpload']['size'] > 10000000) {
                $errorMessage = "Sorry your filesize should be less than 10MB.";
                $uploadOk = 0;
            }
            if (strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
                $errorMessage = "Sorry only jpeg , jpg and png images are allowed. ";
                $uploadOk = 0;
            }

            if ($uploadOk) {
                if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageDir)) {
                    $post = new Post($con, $userLoggedin);
                    $post->SubmitPost($_POST['post_text'], "none", $imageDir);
                    header("Location: index.php");
                    // image uploaded ok
                } else {
                    echo "<div style='text-align:center' class='alert alert-danger'>
                            $errorMessage;
                          </div>";
                }
            }
        } else {
            $post = new Post($con, $userLoggedin);
            $post->SubmitPost($_POST['post_text'], "none", "");
            header("Location: index.php");
        }
    }

}

?>
<div class="left_column_messages">
    <div class="user_details_messages_up column">
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

    <div class="user_details_messages_low column"  id="conversations">
        <h4> Popular Words</h4>
        <div class="trends">
            <?php
            $query = mysqli_query($con , "select * from trends order by hits desc limit 9");

            foreach($query as $row) {
                $word = $row['title'];
                $word_dot = strlen($word) >= 14 ? "..." : "";
                $trimmed_word = str_split($word , 14);
                $trimmed_word = $trimmed_word[0];

                echo "<div style='padding: 1px'>";
                echo  $trimmed_word . $word_dot;
                echo "<br></div>";
            }
            ?>
        </div>
    </div>
</div>

<div class="main_column column">
    <form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" id="fileToUpload">
        <textarea name="post_text" id="post_text" placeholder="Got something to say ?"></textarea>
        <input type="submit" name="post" id="post_button" value="Post">
        <hr>
    </form>

    <div class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif">
</div>
<script>
    let userLoggedin = '<?php echo $userLoggedin; ?>';
    $(document).ready(function () {
        $('#loading').show();

        // original ajax request for loading first posts
        $.ajax({
            url: "includes/handlers/ajax_load_posts.php",
            type: "POST",
            data: "page=1&userLoggedin=" + userLoggedin,
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
            let page = $('.nextPage').val();
            let noMorePosts = $('.posts_area').find('.noMorePosts').val();


            if ((scrollHeight - scrollPos < 2) && noMorePosts == 'false') {
                $('#loading').show();

                let ajaxReq = $.ajax({
                    url: "includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedin=" + userLoggedin,
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