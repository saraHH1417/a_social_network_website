<?php


class Message
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getMostRecentUser()
    {
        $userLoggedin = $this->user_obj->GetUsername();

        $query = mysqli_query($this->con, "select user_to , user_from from messages where user_to='$userLoggedin' or 
                                               user_from='$userLoggedin' order by id desc limit 1");
        if(mysqli_num_rows($query) == 0)
            return false;

        $row = mysqli_fetch_array($query);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if($user_to != $userLoggedin) {
            return $user_to;
        }else {
            return  $user_from;
        }
    }

    public function sendMessage($user_to , $body , $date)
    {
        if($body != "") {
            $userLoggedin = $this->user_obj->GetUsername();
            $query = mysqli_query($this->con, "insert into messages values ('' , '$user_to' , '$userLoggedin' ,
                                '$body' , '$date' , 'no' , 'no' , 'no')");
        }
    }
}