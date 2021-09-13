<?php
include_once("../../config/config.php");
include_once("../classes/User.php");

$query = $_POST['query'];
$userLoggedin = $_POST['userLoggedin'];

$names = explode(" " , $query);

if(strpos($query, "_")  !== false) {
    $userReturned = mysqli_query($con , "select username from users where username like '%$query%' and user_closed='no' limit 8 ");
}
elseif(count($names) > 1){
    $userReturned = mysqli_query($con , "select username from users where (first_name like '%$names[0]%' and last_name 
                                                     like '%$names[1]%') and user_closed='no' limit 8 ");
}
else {
    $userReturned = mysqli_query($con , "select * from users where (first_name like '%$names[0]%' or last_name
                                                     like '%$names[0]%' or username like '%$query%') and user_closed='no' limit 8 ");
}

if($query != "") {
    while($row = mysqli_fetch_array($userReturned)) {
        $user = new User($con ,  $userLoggedin);
        $userReturned_obj = new User($con , $row['username']);

        if($row['username'] != $userLoggedin) {
            $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
        }
        else {
            $mutual_friends = "";
        }

        echo "<div class='resultDisplay'>
                <a href='messages.php?u=" . $row['username'] . "' style='color: #000 ' >
                    <div class='liveSearchProfilePic'>
                        <img src='". $userReturned_obj->getProfilePic() ."'>
                    </div>
                    <div class='liveSearchText'>"
                       . $userReturned_obj->GetFirstLastName() .
                        "<p style='margin-bottom: 0'>" . $row['username'] . "</p>" .
                        "<p id='grey'> $mutual_friends</p>" .
                    "</div>
                </a>
              </div>";

    }
}