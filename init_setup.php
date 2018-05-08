<?php 
session_name("MobiMD");
session_start(); 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    header("Location: login.php");
}
$_SESSION['LAST_ACTIVITY'] = time();
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 600) {
    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
    $_SESSION['CREATED'] = time();  // update creation time
}

require_once("Controller.php");
require_once("model/Base.php");
require_once("model/UserModel.php");
require_once("model/DataModel.php");
require_once("model/Config.php");
require_once("model/CodeModel.php");
require_once("model/ContactModel.php");
require_once("model/ConstantModel.php");

require("assets/twilio/twilio-twilio-php-3ec47c0/Services/Twilio.php");

?>
<?php
$default_view_3_rows=3;
$default_view_5_rows=5;
$default_view_10_rows=10;
$default_view_15_rows=15;
$default_view_20_rows=20;
$default_view_50_rows=50;
$default_view_100_rows=100;
$default_view_rows=$default_view_100_rows;


$g_upload_dirname="/var/www/mobimd/uploads";
$g_data_dirname="/var/www/mobimd/datadir";
$tm =  date('Y-m-d H:i:s');
$show_pageno=1;
?>
