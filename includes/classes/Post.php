<?php


class Post
{
    private $user_obj;
    private $con;

    public function __construct($con, $user) {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }


    public function SubmitPost($body, $user_to)
    {
        $body = strip_tags($body);
        $body = mysqli_real_escape_string($this->con, $body);
        $check_empty = preg_replace('/\s+/' ,"", $body);

        if ($check_empty != "") {
            // current date
            $date_added = date("Y-m-d H:i:s");

            // Get username
            $added_by = $this->user_obj->GetUsername();

            // if post is on own profile user to is none
            if ($user_to == $added_by){
                $user_to = "none";
            }

            //insert post
            $query= mysqli_query($this->con , "INSERT INTO posts VALUES ('', '$body', '$added_by','$user_to', '$date_added',
                          'no', 'no', '0')");
            $returned_id = mysqli_insert_id($this->con);

            //Insert Notification

            // Update post count for user
            $num_posts = $this->user_obj->GetNumPosts();
            $num_posts++;
            $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by' ");

        }
    }

}
