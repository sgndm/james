
<?php 
require_once("init_setup.php") ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<?php
$ct = ConstantModel::get();
$title = "MobiMD Password Reset";
$l_flash_error="";
$l_flash_error_show="none";
$l_flash_info="";
$l_flash_info_show="none";
 ?>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo "$title"; ?></title>
	<meta name="description" content="Description">
	<meta name="viewport" content="width=device-width">
	<link rel="shortcut icon" href="assets/images/favicon.png"/>

	<link href="assets/css/theme_venera.css" media="all" rel="stylesheet" type="text/css" />
	<!-- IF DEV -->
		<link rel="stylesheet/less" type="text/css" href="assets/less/screen.less" />
		<script src="assets/js/less-1.3.1.js" type="text/javascript"></script>
	<!-- ELSE IF PROD -->
		<!--link href="assets/css/screen.css" media="all" rel="stylesheet" type="text/css" /-->
	<!-- END -->
	<link href="//fonts.googleapis.com/css?family=Abel:400|Oswald:300,400,700" media="all" rel="stylesheet" type="text/css" />

	<script src="assets/js/jquery-1.10.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.js" type="text/javascript"></script>
	<script src="assets/js/prettify.js" type="text/javascript"></script>
	<script src="assets/js/lightbox.js" type="text/javascript"></script>
	<script src="js/common.js" type="text/javascript"></script>
</head>

<?php
$ctrl = Controller::get();
$patientID= $ctrl->getGetParamValue("id");
$oldpassword = $ctrl->getGetParamValue("pass");
?>

<input type="hidden" id="login_patient_id" value="<?php echo $patientID; ?>">

<body>
		<header id='header'>
			<div class='navbar navbar-fixed-top'>
				<div class='navbar-inner'>
					<div class='container'>
						<a class='btn btn-navbar' data-target='.nav-collapse' data-toggle='collapse'>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
						</a>
<a href="/" class="brand"><img style="height: 40px; vertical-align: top;" src="assets/images/logo.png" /></a>
<BR/>
<div id="mymessagegroup" class="container">
	<section class="section-wrapper">
		<div class="row">
		<div class="span12">
			<div class="alert fade in" style="display:none" id="alert_info_contact_url">
                   <a href='contact.php' ><strong>Please contact us to request access to our system - please click here to submit your request</strong></a><BR/><BR/>
                        </div>
		</div>
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
		</br>
<div class="row">
&nbsp;
<div class="span6 offset1">
									<h4 class='login-box-head'>Reset Password</h4>
									<div class='control-group'>
										<label>Old Password</label>
										<input datatype="text" minlength=1 title="Old Password" value="<?php echo "$oldpassword"; ?>" id="login_old" class='span4' placeholder='Input old password...' type='password'>
									</div>
									<div class='control-group'>
										<label>New Password</label>
										<input minlength=1 value="" title="Password" id="login_new" class='span4' placeholder='Input new password...' type='password'>
									</div>
									<div class='control-group'>
										<label>Confirm New Password</label>
										<input minlength=1 value="" title="Password" id="login_confirm" class='span4' placeholder='Confirm new password...' type='password'>
									</div>
									<div class='login-actions'>
<input type="button"  onClick="callme('login,resetpassword,')" value="Change Password" />
									</div>
									
</div>
</div>
</div>
</div>


<form name="postmeform" action="" method="POST" class="hidden">
  <input type="hidden" name="sid" value=""/>
  <input type="hidden" name="param" value="none">
  <input type="hidden" name="param1" value="none">
  <input type="hidden" name="param2" value="none">
  <input type="hidden" name="param3" value="none">
  <input type="hidden" name="param4" value="none">
  <input type="hidden" name="param5" value="none">
  <input type="hidden" name="param6" value="none">
  <input type="hidden" name="param7" value="none">
</form>





