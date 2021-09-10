<?php
include_once ('includes/header.php');

$message_obj = new Message($con , $userLoggedin);

if(isset($_GET['u'])) {
    $user_to =  $_GET['u'];

}else {
    $user_to = $message_obj->getMostRecentUser();
    if($user_to == false) {
        $user_to = 'new';
    }
}

if($user_to != 'new') {
    $user_to_obj = new User($con , $user_to);
}

if(isset($_POST['post_message'])) {

    if(isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($con , $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
  }
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
        echo "<br> Likes:" . $user['num_likes'];
        ?>
    </div>
</div>

<div class="main_column column" id="main_column">
    <?php
        if($user_to != 'new') {
            echo "<h4> You and <a href='$user_to'>".  $user_to_obj->GetFirstLastName() . "</a></h4><hr><br>";
        }
    ?>

    <div class="loaded_messages">
        <form action="" method="post">
            <?php
                if($user_to == 'new' ) {
                    echo "Select the friend you want to message. <br><br>";
                    echo "To: <input type='text' >";
                    echo "<div class='results'></div>";
                }
                else {
                    echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
                    echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
                }
            ?>
        </form>
    </div>

</div>
