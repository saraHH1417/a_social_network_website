<?php
include_once ('includes/header.php');

$message_obj = new Message($con , $userLoggedin);

if(isset($_GET['u'])) {
    $user_from_or_to =  $_GET['u'];

}else {
    $user_from_or_to = $message_obj->getMostRecentUser();
    if($user_from_or_to == false) {
        $user_from_or_to = 'new';
    }
}

if($user_from_or_to != 'new') {
    $user_from_or_to_obj = new User($con , $user_from_or_to);
}

if(isset($_POST['post_message'])) {

    if(isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($con , $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_from_or_to, $body, $date);
        header("Location:messages.php");
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
        <h4> Conversations</h4>

        <div class="loaded_conversations">
            <?php echo $message_obj->getConversations();?>
        </div>

        <br>
        <a href="messages.php?u=new"> New Message</a>
    </div>
</div>


<div class="message_main_column column" id="main_column">
    <?php
        if($user_from_or_to != 'new') {
            echo "<h4> You and <a href='$user_from_or_to'>".  $user_from_or_to_obj->GetFirstLastName() . "</a></h4><hr><br>";
            echo "<div class='loaded_messages' id='scroll_messages'>";
                echo $message_obj->getMessages($user_from_or_to);
            echo "</div>";

        }else {
            echo "<h4> New Message</h4>";
        }
    ?>

    <div class="messge_post">
        <form action="" method="post">
            <?php
                if($user_from_or_to == 'new' ) {
                    echo "Select the friend you want to message. <br><br>";
                    ?> To: <input type='text' name='q' placeholder='name' autocomplete='on' id='search_text_input'
                                onkeyup='getUsers(this.value , "<?php echo $userLoggedin; ?>")'>
                    <?php
                    echo "<div class='results'></div>";
                }
                else {
                    echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
                    echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
                }
            ?>
        </form>
    </div>
    <script>
        const div = document.getElementById("scroll_messages");
        div.scrollTop = div.scrollHeight;
        })
    </script>

</div>
