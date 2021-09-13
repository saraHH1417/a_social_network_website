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

    public function getMessages($otherUser)
    {
        $userLoggedin = $this->user_obj->GetUsername();
        $data = "";

        $query = mysqli_query($this->con , "update messages set opened='yes' where user_to='$userLoggedin' and user_from='$otherUser'");

        $get_messages_query = mysqli_query($this->con , "select * from messages where (user_to='$userLoggedin' and user_from='$otherUser')
                                                                    or (user_to='$otherUser' and user_from='$userLoggedin') ");

        while($row= mysqli_fetch_array($get_messages_query)) {
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            $body = $row['body'];

            $div_top = ($user_to == $userLoggedin) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
            $data = $data . $div_top . $body . "</div><br><br>";
        }
        return $data;
    }

    public function getLatestMessage($userLoggedin, $otherUser)
    {
        $details_array = array();

        $query = mysqli_query($this->con , "select body, user_to, date from messages where (user_to='$userLoggedin' and user_from='$otherUser')
                                      or (user_to='$otherUser' and user_from='$userLoggedin') order by id desc limit 1 ");
        $row = mysqli_fetch_array($query);
        $sent_by = ($row['user_to'] == $userLoggedin) ? "They said: " : "You said: ";
        $date_time = $row['date'];
        // Timeframe
        $date_time_now = date("Y-m-d H:i:s");
        $start_date = new DateTime($date_time);
        $end_date = new DateTime($date_time_now);
        $interval = $start_date->diff($end_date);

        // print time interval
        if ($interval->y >= 1) {
            if ($interval->y == 1) {
                $time_msg = $interval->y + " year ago";
            } else {
                $time_msg = $interval->y + " years ago";
            }
        } elseif ($interval->m >= 1) {
            if ($interval->d == 1) {
                $days = $interval->d . " day ago";
            } else {
                $days = $interval->d . " days ago";
            }
            if ($interval->m == 1) {
                $time_msg = $interval->m . " month" . $days;
            } else {
                $time_msg = $interval->m . " months ago" . $days;
            }
        } elseif ($interval->d >= 1) {
            if ($interval->d == 1) {
                $time_msg = $interval->d . " day ago";
            } else {
                $time_msg = $interval->d . " days ago";
            }
        } elseif ($interval->h >= 1) {
            if ($interval->h == 1) {
                $time_msg = $interval->h . " hour ago";
            } elseif ($interval->h > 1) {
                $time_msg = $interval->h . " hours ago";
            }
        } elseif ($interval->i >= 1) {
            if ($interval->i == 1) {
                $time_msg = $interval->i . " minute ago";
            } else {
                $time_msg = $interval->i . " minutes ago";
            }
        } else {
            $time_msg = 'just now';
        }

        array_push($details_array , $sent_by);
        array_push($details_array , $row['body']);
        array_push($details_array , $time_msg);

        return $details_array;
    }

    public function getConversations()
    {
        $userLoggedin = $this->user_obj->GetUsername();
        $return_string = "";
        $conversations = array();

        $query = mysqli_query($this->con, "select user_to , user_from from messages where user_to='$userLoggedin' or user_from='$userLoggedin'");

        while($row= mysqli_fetch_array($query)) {
            $user_to_push = ($row['user_to'] != $userLoggedin) ? $row['user_to'] : $row['user_from'] ;

            if(!in_array($user_to_push , $conversations)) {
                array_push($conversations, $user_to_push);
            }
        }

        foreach($conversations as $username) {
            $user_found_obj = new User($this->con , $username);
            $latest_message_details = $this->getLatestMessage($userLoggedin , $username);
            $dots = (strlen($latest_message_details[1]) > 12) ? "..." : "";
            $split = str_split($latest_message_details[1] , 12 );
            $split = $split[0] . $dots;

            $return_string .=  "<a href='messages.php?u=$username'>
                                    <div class='user_founded_messages'>
                                        <img src='". $user_found_obj->getProfilePic() ."' alt='Not available' >".
                                            $user_found_obj->getFirstLastName().
                                            "<span class='timestamp_smaller' id='grey'> <br> $latest_message_details[2] </span>
                                            <p id='grey' style='margin: 0'>" . $latest_message_details[0] . $split . "</p>
                                    </div>
                                </a><hr>" ;
        }
        return $return_string ;
    }
}