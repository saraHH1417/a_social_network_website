<?php


class Post
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }


    public function SubmitPost($body, $user_to)
    {
        $body = strip_tags($body);
        $body = mysqli_real_escape_string($this->con, $body);

        $body = str_replace('\r\n', '<br>', $body);
//      $body = nl2br($body);

        $check_empty = preg_replace('/\s+/', "", $body);

        if ($check_empty != "") {
            // current date
            $date_added = date("Y-m-d H:i:s");

            // Get username
            $added_by = $this->user_obj->GetUsername();

            // if post is on own profile user to is none
            if ($user_to == $added_by) {
                $user_to = "none";
            }

            //insert post
            $query = mysqli_query($this->con, "INSERT INTO posts VALUES ('', '$body', '$added_by','$user_to', '$date_added',
                          'no', 'no', '0' , '')");
            $returned_id = mysqli_insert_id($this->con);

            //Insert Notification

            // Update post count for user
            $num_posts = $this->user_obj->GetNumPosts();
            $num_posts++;
            $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by' ");

        }
    }

    public function loadPostFriends($data, $limit)
    {

        $page = $data['page'];
        //$page = (int)$page;
        $userLoggedin = $this->user_obj->GetUsername();

        if ($page == 1) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $str = ""; // string to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC ");

        if (mysqli_num_rows($data_query) > 1) {

            $num_iterations = 0; // number of results checked. not necessarily posted
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $user_to = $row['user_to'];
                $date_time = $row['date_added'];

                // Prepare user_to string so it can be included even if it's not posted for a user
                if ($row['user_to'] == 'none') {
                    $user_to = "";
                } else {
                    $user_to_obj = new User($con, $user_to);
                    $user_to_name = $user_to_obj->GetFirstLastName();
                    $user_to = "to <a href='" . $row["user_to"] . "'>" . $user_to_name . "</a>";
                }

                // check if the user who has posted this, has their account closed or no
                $added_by_obj = new User($this->con, $added_by);
                if ($added_by_obj->isClosed()) {
                    continue;
                }

                $user_logged_obj = new User($this->con , $userLoggedin);
                if($user_logged_obj->isFriend($added_by)) {


                    if ($num_iterations++ < $start) {
                        continue;
                    }


                    // Once 10 posts have been loaded , break
                    if ($count > $limit) {
                        break;
                    } else {
                        $count++;
                    }

                    $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE 
                                                                    username='$added_by'");
                    $user_row = mysqli_fetch_array($user_details_query);
                    $first_name = $user_row['first_name'];
                    $last_name = $user_row['last_name'];
                    $profile_pic = $user_row['profile_pic'];
                    ?>

                    <script>
                        function toggle<?php echo $id; ?>() {
                            let element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if(element.style.display == "block") {
                                element.style.display = "none";
                            }
                            else {
                                element.style.display = "block";

                            }
                        }
                    </script>

                    <?php
                    // Timeframe
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time);
                    $end_date = new DateTime($date_time_now);
                    $interval = $start_date->diff($end_date);

                    // print time interval
                    if ($interval->y >= 1) {
                        if ($interval->y == 1) {
                            $time_msg = y + " year ago";
                        } else {
                            $time_msg = y + " years ago";
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


                    $str .= "<div class='status_post' onClick='javascript:toggle$id()' >
                            <div class='post_profile_pic'>
                                <img src='$profile_pic' width='50'>
                            </div>
                            
                            <div class='posted_by' style='color:#ACACAC'>
                                <a href='$added_by' > $first_name  $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                                $time_msg
                            </div>
                            
                            <div  id='post_body'> 
                                $body
                                <br>
                            </div>
                        </div>
                        <div class='post_comment' id='toggleComment$id' style='display:none;'>
                            <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'  frameborder='0'></iframe>
                         </div>
                        <hr>";
                }
            } // End while loop

            if ($count > $limit) {
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                            <input type='hidden' class='noMorePosts' value='false' >";
            } else {

                $str.= "<input type='hidden' class='noMorePosts' value='true' >
                    <p text-align='centre;'> No more posts to show!</p> ";
            }
        }
        echo $str;
    }
}
