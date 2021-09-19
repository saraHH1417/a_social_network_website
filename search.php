<?php
include_once ("includes/header.php");
if(isset($_GET['q'])) {
    $query = $_GET['q'];
}
else {
    $query = "";
}

if(isset($_GET['type'])) {
    $type = $_GET['type'];
}
else {
    $type = "name";
}

?>

<div class="main_column column" id="main_column">
    <?php
       if($query == "")  {
           echo "You must enter something in the search box.";
       }else {

           // If query contains underscore , assume user is searching for username
           if($type == 'username') {
               $usersReturnedQuery = mysqli_query($con , "select * from users where username like '%$query%' 
                                                        and user_closed='no' limit 5");
           }else {
               $names = explode(" " , $query);

               if(count($names) == 3 ){ // 3 with considering middle name
                   $usersReturnedQuery = mysqli_query($con, "select * from users where (first_name like '%$names[0]%' and 
                               last_name like '%$names[2]%') and user_closed='no' limit 5");
               }
               elseif(count($names) == 2 ) {
                   $usersReturnedQuery = mysqli_query($con, "select * from users where (first_name like '%$names[0]%' and 
                               last_name like '%$names[1]%') and user_closed='no' limit 5");
               }
               else {
                       $usersReturnedQuery = mysqli_query($con, "select * from users where (first_name like '%$query%' or last_name
                                                                like '%$query%' ) and user_closed='no' limit 5 ");
               }
           }

           // Check if results were found
           if(mysqli_num_rows($usersReturnedQuery) == "0") {
               echo "We can't find any user with " . $type . " like: " . $query;
           }
           else {
               echo "<h4> " . mysqli_num_rows($usersReturnedQuery) .  " results found: <br></h4>";

               echo "<p id='grey'> Try searching for: </p>";
               echo "<a href='search.php?q=" . $query . "&type=name' >Names</a> ,  
                     <a href='search.php?q=". $query . "&type=username'>Username</a> <hr id='search_result_hr'>";

               $userLoggedin_obj = new User($con , $userLoggedin);

               while($row = mysqli_fetch_array($usersReturnedQuery)) {
                   $button = "";
                   $mutual_friends =  "";
                   $user_found_username =  $row['username'];
                   if($user_found_username != $userLoggedin) {

                       // Generate button based on friendship status
                       if($userLoggedin_obj->isFriend($user_found_username)) {
                           $button = "<input type='submit' name='" . $user_found_username ."' class='danger' value='Remove friend' >";
                       }else if($userLoggedin_obj->didReceiveRequest($user_found_username)) {
                           $button = "<input type='submit' name='" . $user_found_username ."' class='warning' value='Answer friend request'>";
                       }else if($userLoggedin_obj->didSendRequest($user_found_username)) {
                           $button = "<div style='background-color: #665e5e' id='friend_request_pending'> 
                                               Friend request pending
                                      </div>";
                       }else {
                           $button = "<input type='submit' name='" . $user_found_username ."' class='success' value='Add friend'>";
                       }

                       $mutual_friends = $userLoggedin_obj->getMutualFriends($user_found_username);

                       if($mutual_friends == 0 ) {
                           $mutual_friends = "";
                       }elseif($mutual_friends == 1) {
                           $mutual_friends = $mutual_friends . " friend in common";
                       }else {
                           $mutual_friends = $mutual_friends . " friends in common";
                       }

                       //Button form
                       if(isset($_POST[$row['username']])) {
                           if($userLoggedin_obj->isFriend($user_found_username)) {
                               $userLoggedin_obj->removeFriend($user_found_username);
                               header("Location:http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                           }else if($userLoggedin_obj->didReceiveRequest($user_found_username)) {
                               header("Location:requests.php");
                           }else {
                               $userLoggedin_obj->sendFriendRequest($user_found_username);
                               header("Location:http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                           }
                       }

                   }

                   echo "<div class='search_result_page'>
                            <div class='searchPageFriendButton'>
                                <form action='' method='posts'>
                                    " . $button . "
                                    <br>
                                </form>
                            </div>
                            
                            <div class='resultProfilePic'>
                                <a href='" . $user_found_username ."'>
                                    <img src='". $row['profile_pic'] . "' style='height: 100px;'>
                                </a>
                            </div>
                            
                            <a href='" . $user_found_username ."'>" . $row['first_name'] . $row['last_name'] ."
                                <p id='grey'>" . $row['username']."</p>    
                            </a>
                            <br>
                            " . $mutual_friends ." <br>
                         </div>
                         <hr id='search_result_hr'>";

               }// end while
           }
       }
    ?>
</div>

