<?php
include_once("../../config/config.php");
include_once("../classes/User.php");

$query = $_POST['query'];
$userLoggedin = $_POST['userLoggedin'];

$names = explode(" " , $query);

// If query contains underscore , assume user is searching for username

if(strpos($query , "_") !== false) {
    $usersReturnedQuery = mysqli_query($con , "select * from users where username like '%$query%' and user_closed='no' limit 5");
}
//if there are two words assume they are first and last name respectfully
elseif (count($names) == 2) {
    $usersReturnedQuery = mysqli_query($con , "select * from users where (first_name like '%$names[0]%' and 
                           last_name like '%$names[1]%') and user_closed='no' limit 5");
}
// if query has only one word search for first and last names
else {
    $usersReturnedQuery = mysqli_query($con , "select * from users where (first_name like '%$query%' or last_name
                                                            like '%$query%' ) and user_closed='no' limit 5 ");
}

if($query != "") {
    while($row = mysqli_fetch_array($usersReturnedQuery)) {
        $user = new User($con , $userLoggedin);

        if($row['username'] != $userLoggedin) {
            $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
        }
        else {
            $mutual_friends = "";
        }

        echo "<div class='resultDisplay'>
                <a href='". $row['username'] ."' style='#1485BD'>
                    <div class='liveSearchProfilePic'>
                        <img src='". $row['profile_pic'] ."'>
                    </div>
                    <div class='liveSearchText'>
                        ". $row['first_name'] . " " . $row['last_name'] . "
                        <p>" . $row['username'] ."</p>  
                        <p id='grey'>". $mutual_friends ."</p>  
                    </div>        
                </a>
            </div>";
    }
}