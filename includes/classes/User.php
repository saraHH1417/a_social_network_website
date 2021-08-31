<?php


class User
{
    private $user;
    private $con;

    public function __construct($con, $user) {
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
        return $row['first_name'] ." ". $row['last_name'];
    }

    public function isClosed()
    {
        // MY WAY TUTOR WAY IS DIFFERENT
        if($this->user['user_closed'] == "yes") {
            return True;
        }else {
            return false;
        }
    }

    public function isFriend($username_to_check) {
        $usernamecomma = "," . $username_to_check . "," ;

        if ((strstr($this->user['friend_array'] , $usernamecomma)) || $this->user['username'] == $username_to_check) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getProfilePic()
    {
        return $this->user['profile_pic'];
    }

}
