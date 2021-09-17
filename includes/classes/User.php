<?php


class User
{
    private $user;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $user_details_query = mysqli_query($con, "SELECT * FROM users where username='$user'");
        $this->user = mysqli_fetch_array($user_details_query);
    }

    public function GetUsername()
    {
        return $this->user['username'];
    }

    public function GetNumPosts()
    {
        return $this->user['num_posts'];
    }

    public function GetFirstLastName()
    {
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT first_name, last_name from users WHERE username='$username' ");
        $row = mysqli_fetch_array($query);
        return $row['first_name'] . " " . $row['last_name'];
    }

    public function isClosed()
    {
        // MY WAY TUTOR WAY IS DIFFERENT
        if ($this->user['user_closed'] == "yes") {
            return true;
        } else {
            return false;
        }
    }

    public function isFriend($username_to_check)
    {
        $usernameTwoComma =  $username_to_check . ",";
        if ((strstr($this->user['friend_array'], $usernameTwoComma)) || $this->user['username'] == $username_to_check) {
            return true;
        } else {
            return false;
        }
    }

    public function getProfilePic()
    {
        return $this->user['profile_pic'];
    }

    public function getNumLikes()
    {
        return $this->user['num_likes'];
    }

    public function getFriendArray()
    {
        return $this->user['friend_array'];
    }

    public function didReceiveRequest($user_from)
    {
        $user_to = $this->GetUsername();
        $query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' 
                               AND user_from='$user_from'");

        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function didSendRequest($user_to)
    {
        $user_from = $this->GetUsername();
        $query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' 
                               AND user_from='$user_from'");
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function removeFriend($user_to_remove)
    {
        $logged_in_username = $this->GetUsername();
        $user_friends = $this->user['friend_array'];
        if ($this->isFriend($user_to_remove)) {
            $logged_in_user_friends_new = str_replace( $user_to_remove . ",", "", $user_friends);
            $delete_from_friend_array = mysqli_query($this->con, "update users set friend_array='$logged_in_user_friends_new' 
                                            where username='$logged_in_username'");

            $user_to_remove_obj = new User($this->con, $user_to_remove);
            $user_to_remove_friends = $user_to_remove_obj->getFriendArray();
            $user_to_remove_friends_new = str_replace( $logged_in_username . ",", "", $user_to_remove_friends);
            $delete_from_friend_array = mysqli_query($this->con, "update users set friend_array='$user_to_remove_friends_new' 
                                            where username='$user_to_remove'");

            $user_from = $this->GetUsername();
            $remove_friend_request = mysqli_query($this->con , "delete from friend_requests where user_to='$user_to_remove' 
                                                    and user_from='$user_from'");
        }
    }

    public function sendFriendRequest($user_to)
    {
        $user_from = $this->GetUsername();
        $friend_request_query = mysqli_query($this->con, "insert into friend_requests values('', '$user_to' , '$user_from')");
    }

    public function addFriend($user_to_add)
    {
        $logged_in_user = $this->user['username'];
        $logged_in_user_friends = $this->getFriendArray();
        $logged_in_user_friends .= $user_to_add  .",";
        $query = mysqli_query($this->con , "UPDATE users SET friend_array='$logged_in_user_friends' where username='$logged_in_user'");

        $user_to_add_obj = new User($this->con, $user_to_add);
        $user_to_add_friends = $user_to_add_obj->getFriendArray();
        $user_to_add_friends .= $logged_in_user . ",";
        $query_two = mysqli_query($this->con, "UPDATE users SET friend_array='$user_to_add_friends' where username='$user_to_add'" );

        $remove_from_requests_query = mysqli_query($this->con , "delete from friend_requests where user_to='$logged_in_user' 
                              and user_from='$user_to_add'");
    }

    public function removeFriendRequest($user_to_ignore)
    {
        $user_to = $this->GetUsername();
        mysqli_query($this->con , "delete from friend_requests where user_to='$user_to' and user_from='$user_to_ignore'");
    }

    public function getMutualFriends($user_to_check) {
       $mutual_friends = 0;
       $user_friends_string = $this->getFriendArray();
       $user_friends_array = explode("," , $user_friends_string);

       $user_to_check_obj = new User($this->con , $user_to_check);
       $user_to_check_friends = $user_to_check_obj->getFriendArray();
       $user_to_check_friends_array = explode("," , $user_to_check_friends);
       if($this->GetUsername() != $user_to_check) {
           foreach ($user_friends_array as $i) {
               foreach ($user_to_check_friends_array as $j) {
                   if ($i == $j and $i != 0) {
                       $mutual_friends++;
                   }
               }
           }
       }
       return $mutual_friends ;
    }

    public function getNumberOfFriendRequests()
    {
        $userLoggedin = $this->GetUsername();
        $friend_request_query = mysqli_query($this->con , "select id from friend_requests where user_to='$userLoggedin'");
        return mysqli_num_rows($friend_request_query);
    }

}