<?php
    include_once("includes/header.php"); //Header
    include_once("includes/classes/User.php");
    include_once("includes/classes/Post.php");
?>

<div class="main_column column" id="main_column">
    <h4> Friend requests</h4>

    <?php
    
    $query = mysqli_query($con , "SELECT * FROM friend_requests WHERE user_to='$userLoggedin'");

    if(mysqli_num_rows($query) == 0 ) {
        echo "You have no friend request at this time!";
    }else {
        while($row = mysqli_fetch_array($query)) {
            $user_from = $row['user_from'];
            $user_from_obj = new User($con , $user_from);

            echo $user_from_obj->GetFirstLastName() . " sent you a friend request!";
            
//            $user_from_friend_array = $user_from_obj->getFriendArray() ;

            if(isset($_POST['accept_request' . $user_from ])) {
                $userLoggedin_obj = new User($con , $userLoggedin);
                $userLoggedin_obj->addFriend($user_from);
                echo "You are now friends";
                header("Location:requests.php");
                }

            if(isset($_POST['ignore_request' . $user_from])) {
                $userLoggedin_obj = new User($con , $userLoggedin);
                $userLoggedin_obj->removeFriendRequest($user_from);
                echo "Request ignored";
                header("Location:requests.php");
            }
    ?>

            <form action="requests.php" method="POST">
                <input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_request_button" value="Accept">
                <input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_request_button" value="Ignore">
            </form>
    <?php
       }
    }
    ?>
</div>
