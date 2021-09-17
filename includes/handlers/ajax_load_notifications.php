<?php

include_once("../../config/config.php");
include_once("../classes/User.php");
include_once("../classes/Message.php");
include_once("../classes/Notification.php");



$limit = 7;  // Number of messages to load

$notification = new Notification($con, $_REQUEST['userLoggedin']);

echo $notification->getNotifications($_REQUEST , $limit);