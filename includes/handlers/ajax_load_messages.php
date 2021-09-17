<?php
include_once("../../config/config.php");
include_once("../classes/User.php");
include_once("../classes/Message.php");


$limit = 7;  // Number of messages to load

$message = new Message($con , $_REQUEST['userLoggedin']);

echo $message->getConversationsDropdown($_REQUEST, $limit);