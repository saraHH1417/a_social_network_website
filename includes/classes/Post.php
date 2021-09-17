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
            if($user_to != 'none') {
                $userLoggedin = $this->user_obj->GetUsername();
                mysqli_query($this->con , "update messages set body='$user_to' where id='27'");
                $notification_obj = new Notification($this->con ,$userLoggedin );
                $insert_query = $notification_obj->insertNotification($returned_id , $user_to , 'profile_post');
            }

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
            $count = 1; //  number of results posted

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
                    $user_to_obj = new User($this->con, $user_to);
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

                    if($userLoggedin == $added_by) {
                        $delete_button = "<button class='delete_btn' id='post_delete$id'>X</button>";
                    }else{
                        $delete_button = "";
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

                            let target = $(event.target);
                                if(!target.is("a") && !target.is("img") && !target.is("button")) {
                                    if (element.style.display == "block") {
                                        element.style.display = "none";
                                    } else {
                                        element.style.display = "block";

                                    }
                            }
                        }
                    </script>

                    <?php

                    $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id ='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);

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


                    $str .= "<div class='status_post' onClick='javascript:toggle$id()' >
                                <div class='post_profile_pic'>
                                    <a href='$added_by' >
                                        <img src='$profile_pic' width='50'>
                                    </a>
                                </div>
                                
                                <div class='posted_by' style='color:#ACACAC'>
                                    <a href='$added_by' > $first_name  $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                                    $time_msg
                                    $delete_button
                                </div>
                                
                                <div  id='post_body'> 
                                    $body
                                    <br>
                                </div>
                                <br><br><br>
                                <div class='newsfeedPostOptions'>
                                    comments($comments_check_num) &nbsp;&nbsp;&nbsp;&nbsp;
                                    <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                                </div>
                                <div class='post_comment' id='toggleComment$id' style='display:none;'>
                                    <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'  frameborder='0'></iframe>
                                 </div>
                                <hr>
                            </div>";
                }
             ?>
                <script>
                    $(document).ready(function() {
                        $("#post_delete<?php echo $id;?>").on("click" , function () {
                            bootbox.confirm("Are you sure you want to delete this post?" , function (result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>" , {result:result});
                                if(result) {
                                    location.reload();
                                }
                            })
                        });
                    });
                </script>
            <?php
            } // End while loop

            if ($count > $limit) {
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                            <input type='hidden' class='noMorePosts' value='false' >";
            } else {

                $str.= "<input type='hidden' class='noMorePosts' value='true' >
                    <p style='text-align: center'> No more posts to show!</p> ";
            }
        }
        echo $str;
    }


    public function loadProfilePosts($data, $limit)
    {
        $page = $data['page'];
        $profileUsername = $data['profileUsername'];
        //$page = (int)$page;
        $userLoggedin = $this->user_obj->GetUsername();

        if ($page == 1) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $str = ""; // string to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE 
                          deleted='no' AND ((added_by = '$profileUsername'  AND user_to='none') OR user_to='$profileUsername')
                                                            ORDER BY id DESC ");

        if (mysqli_num_rows($data_query) > 1) {

            $num_iterations = 0; // number of results checked. not necessarily posted
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $user_to = $row['user_to'];
                $date_time = $row['date_added'];


                $user_logged_obj = new User($this->con , $userLoggedin);

                    if ($num_iterations++ < $start) {
                        continue;
                    }


                    // Once 10 posts have been loaded , break
                    if ($count > $limit) {
                        break;
                    } else {
                        $count++;
                    }


                    if($userLoggedin == $added_by) {
                        $delete_button = "<button class='delete_btn' id='post_delete$id'>X</button>";
                    }else{
                        $delete_button = "";
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

                            let target = $(event.target);
                            if(!target.is("a") && !target.is("img") && !target.is("button")) {
                                if (element.style.display == "block") {
                                    element.style.display = "none";
                                } else {
                                    element.style.display = "block";

                                }
                            }
                        }
                    </script>

                    <?php

                    $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id ='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);

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


                    $str .= "<div class='status_post' onClick='javascript:toggle$id()' >
                                <div class='post_profile_pic'>
                                    <a href='$added_by' >
                                        <img src='$profile_pic' width='50'>
                                    </a>
                                </div>
                                
                                <div class='posted_by' style='color:#ACACAC'>
                                    <a href='$added_by' >". $first_name ." ". $last_name . "</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                    $time_msg
                                    $delete_button
                                </div>
                                
                                <div  id='post_body'> 
                                    $body
                                    <br>
                                </div>
                                <br><br><br>
                                <div class='newsfeedPostOptions'>
                                    comments($comments_check_num) &nbsp;&nbsp;&nbsp;&nbsp;
                                    <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                                </div>
                                <div class='post_comment' id='toggleComment$id' style='display:none;'>
                                    <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'  frameborder='0'></iframe>
                                 </div>
                                <hr>
                            </div>";

                ?>
                <script>
                    $(document).ready(function() {
                        $("#post_delete<?php echo $id;?>").on("click" , function () {
                            bootbox.confirm("Are you sure you want to delete this post?" , function (result) {
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>" , {result:result});
                                if(result) {
                                    location.reload();
                                }
                            })
                        });
                    });
                </script>
                <?php
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

    public function getSinglePost($post_id)
    {
        $userLoggedin = $this->user_obj->GetUsername();

        $str = ""; // string to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' and id='$post_id' ORDER BY id DESC ");
        $set_opened_query = mysqli_query($this->con , "update notifications set opened='yes' where 
                                                        user_to='$userLoggedin' and link like '%=$post_id'");
        if (mysqli_num_rows($data_query) > 0) {
            $row = mysqli_fetch_array($data_query);
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $user_to = $row['user_to'];
            $date_time = $row['date_added'];

            // Prepare user_to string so it can be included even if it's not posted for a user
            if ($row['user_to'] == 'none') {
                $user_to = "";
            } else {
                $user_to_obj = new User($this->con, $user_to);
                $user_to_name = $user_to_obj->GetFirstLastName();
                $user_to = "to <a href='" . $row["user_to"] . "'>" . $user_to_name . "</a>";
            }

            // check if the user who has posted this, has their account closed or no
            $added_by_obj = new User($this->con, $added_by);
            if ($added_by_obj->isClosed()) {
                return;
            }

            if($userLoggedin == $added_by) {
                $delete_button = "<button class='delete_btn' id='post_delete$id'>X</button>";
            }else{
                $delete_button = "";
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

                    let target = $(event.target);
                    if(!target.is("a") && !target.is("img") && !target.is("button")) {
                        if (element.style.display == "block") {
                            element.style.display = "none";
                        } else {
                            element.style.display = "block";

                        }
                    }
                }
            </script>

            <?php

            $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id ='$id'");
            $comments_check_num = mysqli_num_rows($comments_check);

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


            $str .= "<div class='status_post' onClick='javascript:toggle$id()' >
                        <div class='post_profile_pic'>
                            <a href='$added_by' >
                                <img src='$profile_pic' width='50'>
                            </a>
                        </div>
                        
                        <div class='posted_by' style='color:#ACACAC'>
                            <a href='$added_by' > $first_name  $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                            $time_msg
                            $delete_button
                        </div>
                        
                        <div  id='post_body'> 
                            $body
                            <br>
                        </div>
                        <br><br><br>
                        <div class='newsfeedPostOptions'>
                            comments($comments_check_num) &nbsp;&nbsp;&nbsp;&nbsp;
                            <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                        </div>
                        <div class='post_comment' id='toggleComment$id' style='display:none;'>
                            <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'  frameborder='0'></iframe>
                         </div>
                        <hr>
                    </div>";
        ?>
        <script>
            $(document).ready(function() {
                $("#post_delete<?php echo $id;?>").on("click" , function () {
                    bootbox.confirm("Are you sure you want to delete this post?" , function (result) {
                        $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>" , {result:result});
                        if(result) {
                            location.reload();
                        }
                    })
                });
            });
        </script>
        <?php
        }else {
            echo "No post found. If you clicked a link, it may be broken";
            return;
        }
        echo $str;
    }
}
?>
