<?php require_once("init_setup.php") ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<?php
	$fullWidthPage = True;
?>
<?php
        $show_contact = 0;
        session_destroy();
		$ct = ConstantModel::get();
		$title = $ct->getTitle();
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
$user_id=0;
?>

<input type="hidden" id="user_id" value="<?php echo $user_id; ?>">

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
<div class="row">
&nbsp;
<div class="span6">
									<h4 class='login-box-head'>Login</h4>
									<div class='control-group'>
										<label>UserName</label>
										<input datatype="text" minlength=1 title="Email" value="" id="login_email" class='span4' placeholder='Input Username...' type='text'>
									</div>
									<div class='control-group'>
										<label>Password</label>
										<input minlength=1 onChange="callme('login,authenticate,login_email')" value="" title="Password" id="login_password" class='span4' placeholder='Input password...' type='password'>
									</div>
									<div class='login-actions'>
<input type="button"  onClick="callme('login,authenticate,login_email')" value="Log Me In" />
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


