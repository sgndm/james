<?php require_once("init_setup.php") ?>

<?php
$ctrl = Controller::get();
if ( $ctrl->getUserID()  == 0 ) 
{
   header("Location: login.php");
}
?>
<html >
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>MobiMD</title>
	<meta name="description" content="Description goes here....">
	<link rel="shortcut icon" href="assets/images/favicon.png"/>
	<link href="assets/css/theme_venera.css#iefix" media="all" rel="stylesheet" type="text/css" />
	<link href="css/surgical.css#iefix" media="all" rel="stylesheet" type="text/css" />

		<link rel="stylesheet/less" type="text/css" href="assets/less/screen.less" />
		<script src="assets/js/less-1.3.1.js" type="text/javascript"></script>
	<link href="//fonts.googleapis.com/css?family=Abel:400|Oswald:300,400,700" media="all" rel="stylesheet" type="text/css" />

	<script src="assets/js/jquery-1.10.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.js" type="text/javascript"></script>
	<script src="assets/js/prettify.js" type="text/javascript"></script>
	<script src="assets/js/lightbox.js" type="text/javascript"></script>
	<script src="js/common.js" type="text/javascript"></script>

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>

</head> 

<form name="postmeform" action="" method="POST" class="hidden">
  <input type="hidden" name="param" value="none">
  <input type="hidden" name="user_id" value="none">
  <input type="hidden" name="patient_id" value="none">
  <input type="hidden" name="record_id" value="none">
  <input type="hidden" name="form_name" value="none">
  <input type="hidden" name="current_tab" value="none">
  <input type="hidden" name="called_by" value="none">
  <input type="hidden" name="param1" value="none">
  <input type="hidden" name="param2" value="none">
  <input type="hidden" name="param3" value="none">
  <input type="hidden" name="param4" value="none">
  <input type="hidden" name="param5" value="none">
  <input type="hidden" name="param6" value="none">
  <input type="hidden" name="param7" value="none">
</form>
<script>
window.location.hash="no-back-button";
window.location.hash="Again-No-back-button";
window.onhashchange=function(){window.location.hash="no-back-button";}
</script> 
<?php

$ctrl = Controller::get();
$l_flash_error="";
$l_flash_error_show="none";
$l_flash_info="";
$l_flash_info_show="none";
$l_show_login_box_div="none";
$l_show_login_window_div="none";
?>
<div class="row">
<div class="span12">
<div style="display:<?php echo $l_flash_info_show; ?>" id="alert_info_div" class="span10 alert fade in">
<strong id="alert_info_message" ><?php echo $l_flash_info; ?></strong>
<button type="button" class="close" onclick="hide_div('#alert_info_div')">×</button>
</div>
<div style="display:<?php echo $l_flash_error_show; ?>" id="alert_error_div" class="span10 alert alert-error fade in">
<strong id="alert_error_message" ><?php echo $l_flash_error; ?></strong>
<button type="button" class="close" onclick="hide_div('#alert_error_div')">×</button>
</div>
</div>
</div>
