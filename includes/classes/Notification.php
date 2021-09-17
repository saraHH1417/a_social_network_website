<?php

class Notification
{
    private $con;
    private $user_obj;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con , $user);
    }

    public function getUnreadNumber()
    {
        $userLoggedin = $this->user_obj->GetUsername();
        $query = mysqli_query($this->con , "Select id from notifications where user_to='$userLoggedin' and viewed='no'");
        return mysqli_num_rows($query);

    }

    public function insertNotification($post_id , $user_to , $type)
    {
        $user_from = $this->user_obj;
        $user_from_username = $user_from->GetUsername();
        $user_from_firstlastname = $user_from->GetFirstLastName();

        $date_time = date("Y-m-d H:i:s");

        switch ($type) {
            case 'comment':
                $messagge = $user_from_firstlastname . " commented on your post.";
                break;
            case 'like':
                $messagge = $user_from_firstlastname . " liked your post.";
                break;
            case 'unlike':
                $messagge = $user_from_firstlastname . " unliked your post.";
                break;
            case 'profile_post':
                $messagge = $user_from_firstlastname. " posted on your profile.";
                break;
            case 'profile_comment':
                $messagge = $user_from_firstlastname . " commented on a post in your profile.";
                break;
            case 'comment_on_the_post_you_commented':
                $messagge = $user_from_firstlastname . " commented on a post you commented before.";
                break;


        }

        $link = "post.php?id=" . $post_id ;

        $insert_query = mysqli_query($this->con , "insert into notifications values('' , '$user_to', '$user_from_username', '$messagge',
                                        '$link' , '$date_time' , 'no' , 'no')");

    }

    public function getNotifications($data , $limit)
    {

        $page = $data['page'];

        if($page == 1) {
            $start = 0;

        }else {
            $start = ($page -1 )* $limit;
        }

        $userLoggedin = $data['userLoggedin'];
        $return_string = "";

        $set_viewed_query = mysqli_query($this->con , "update notifications set viewed='yes' where user_to='$userLoggedin' order by id desc");
        $notifications_query = mysqli_query($this->con , "select * from notifications where user_to='$userLoggedin' order by id desc ");

        $num_iterations = 0; // Number of messages checked
        $count = 1 ; // Number of messages posted

        while($row= mysqli_fetch_array($notifications_query)) {
            $user_from = $row['user_from'];
            $user_from_obj = new User($this->con , $user_from);
            $user_from_obj_profilepic = $user_from_obj->getProfilePic();
            $message = $row['message'];
            $date_time = $row['datetime'];
            $link = $row['link'];
            $opened = $row['opened'];

            $style = ($opened == 'no') ? 'background-color: #DDEDFF;' : "";

            if($num_iterations++ < $start ) {
                continue;
            }

            if($count++ > $limit) {
                break;
            }

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


            $return_string .= "<a href='". $link . "'>
                                    <div class='resultDisplay resultDisplayNotification' style='" . $style. "'>
                                        <div class='notificationsProfilePic'>
                                            <img src=". $user_from_obj_profilepic .">
                                        </div>
                                        <p class='time_stamp_smaller' id='grey'>". $time_msg . "</p>" . $message.
                                    "</div>    
                              </a>";

        }
        if($count > $limit ) {
            $return_string .= "<input class='nextPageDropDownData' type='hidden' value='" . ($page + 1) . "'>
                               <input class='noMoreDropDownData' type='hidden' value='false'>";
        }else {
            $return_string .= "<input class='noMoreDropDownData' type='hidden' value='true'>
                                <br><p style='text-align: center;' class='noMoreMessage'>No more notifications to load!</p>";
        }
        return $return_string;

    }
}
