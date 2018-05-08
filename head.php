<?php require_once("init_setup.php") ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<?php
$ctrl = Controller::get();
$ux = UserModel::get();
$ct = ConstantModel::get();
$title = $ct->getTitle();
$l_flash_error="";
$l_flash_error_show="none";
$l_flash_info="";
$l_flash_info_show="none";
$l_show_login_box_div="none";
$l_show_login_window_div="none";
?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo "$title"; ?></title>
	<meta name="description" content="Description goes here....">
	<meta name="viewport" content="width=device-width">
	<link rel="shortcut icon" href="assets/images/favicon.png"/>

	<link href="assets/css/theme_venera.css" media="all" rel="stylesheet" type="text/css" />
	<link href="css/surgical.css" media="all" rel="stylesheet" type="text/css" />
	<!-- IF DEV -->
		<link rel="stylesheet/less" type="text/css" href="assets/less/screen.less" />
		<script src="assets/js/less-1.3.1.js" type="text/javascript"></script>
	<!-- ELSE IF PROD -->
		<!--link href="assets/css/screen.css" media="all" rel="stylesheet" type="text/css" /-->
	<!-- END -->

	<script src="assets/js/jquery-1.10.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.js" type="text/javascript"></script>
	<script src="assets/js/prettify.js" type="text/javascript"></script>
	<script src="assets/js/lightbox.js" type="text/javascript"></script>
	<script src="js/common.js" type="text/javascript"></script>
	<script src="assets/ckeditor/ckeditor.js"></script>
	<link rel="stylesheet" href="js/telinput/css/intlTelInput.css">
	<script src="js/telinput/js/intlTelInput.js"></script>
<!-- graphp-->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
<!-- graphp-->
</head>

<?php
$fullWidthPage = True;
  $show_contact = 1;

// Special logic for the bread crump highlighter
setlocale(LC_MONETARY, 'en_US');


$ctrl = Controller::get();
$lx= $ctrl->getConfig();

$l_var = $ctrl->getGetParamValue("action");
if ( $l_var == "signout" ) 
{
        $ctrl->session_reset();
        header("Location: login.php");
}

$l_sid = $ctrl->getPostParamValue("sid");
if ( ( ! is_null($l_sid) ) &&  strlen($l_sid) > 0  )  
{
       $l_reply = $ctrl->isvaliduser($l_sid);
       if (  $l_reply == "0" ) 
       {
           echo "<h4> Server Error  ... please contact support  $l_sid $l_reply </h4>";
       }
}
if ( $ctrl->getUserID()  == 0 ) 
{
   header("Location: login.php");
}
?>
<?php  $user_name= $ctrl->getUserName(); 
       $userType = $ctrl->getUserType();
	   $user_id= $ctrl->getUserID();
?>

<input type="hidden" id="user_id" value="<?php echo $user_id; ?>">

<?php
	function onpage($href, $title){
		global $NAV_OVERRIDE;
		return ((!$NAV_OVERRIDE && $_SERVER['SCRIPT_FILENAME'] == (apache_lookup_uri($href)->filename)) || $title == $NAV_OVERRIDE);
	}

	function lnavlink($href, $title){
		$tabActive = onpage($href, $title);
		echo '<li class="'. ($tabActive ? 'active':'') .'">';
		echo '<a href="' . $href . '">'. $title . '</a>';
		echo '</li>';
	}
?>

<body>
	<!--[if lt IE 7]>
	<p class="chromeframe">You are using an outdated browser. <a href="//browsehappy.com/">Upgrade your browser today</a> or <a href="//www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
	<![endif]-->
	
	<script>
window.location.hash="no-back-button";
window.location.hash="Again-No-back-button";
window.onhashchange=function(){window.location.hash="no-back-button";}
</script> 

		<header id='header'>
			<div class='navbar navbar-fixed-top'>
				<div class='navbar-inner'>
					<div class='container'>
						<a class='btn btn-navbar' data-target='.nav-collapse' data-toggle='collapse'>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
						</a>
						<a href="/myhome.php" class="brand"><img style="height: 40px; vertical-align: top;" src="assets/images/logo.png" /></a>
						<div class='nav-collapse subnav-collapse collapse pull-right' id='top-navigation'>
							<ul class='nav nav-pills'>
								<?= lnavlink('myhome.php', 'Home'); ?>
								<?= lnavlink('user.php', 'My Account'); ?>
								<?= lnavlink('contact.php', 'Contact'); ?>
			<li class="dropdown">
            <a class="dropdown-toggle admin" data-toggle="dropdown" 
            href="#"><?php echo "$user_name"; ?><b class="caret"></b></a>
            <ul class="dropdown-menu">
            <!-- links -->
   <?php if($ux->doIHavePermission($userType, "clinic_list")){ ?>
   <li><a href="cliniclist.php" >Clinics</a></li>
   <?php } if($ux->doIHavePermission($userType, "doctor_list")){ ?>
   <li><a href="doctorlist.php" >Doctors</a></li>
   <?php } if($ux->doIHavePermission($userType, "cc_list")){ ?>
   <li><a href="cclist.php" >Care Coordinators</a></li>
   <?php } if($ux->doIHavePermission($userType, "admin_list")){ ?>
   <li><a href="userlist.php" >Admins</a></li>
   <?php } if($ux->doIHavePermission($userType, "diagnosis_list")){ ?>
   <li><a href="diagnosislist.php" >Diagnosis</a></li>
   <?php } if($ux->doIHavePermission($userType, "pushlog_list")){ ?>
   <li><a href="myalert.php" >Push Notification Logs</a></li>
   <?php } if($ux->doIHavePermission($userType, "organization_list")){ ?>
   <li><a href="organizationlist.php" >Organizations</a></li>
   <?php } if($ux->doIHavePermission($userType, "video_list")){ ?>
   <li><a href="videolist.php" >Videos</a></li>
   <?php } if($ux->doIHavePermission($userType, "vital_list")){ ?>
   <li><a href="vitallist.php" >Vitals</a></li>
   <?php } if($ux->doIHavePermission($userType, "symptom_list")){ ?>
   <li><a href="symptomlist.php" >Symptoms</a></li>
   <?php } if($ux->doIHavePermission($userType, "diet_list")){ ?>
   <li><a href="dietlist.php" >Diets</a></li>
   <?php } if($ux->doIHavePermission($userType, "wound_list")){ ?>
   <li><a href="woundcarelist.php" >Wound Care</a></li>
   <?php } if($ux->doIHavePermission($userType, "activity_list")){ ?>
   <li><a href="phyactlist.php" >Physical Activities</a></li>
   <?php } if($ux->doIHavePermission($userType, "medication_list")){ ?>
   <li><a href="medicationlist.php" >Medications</a></li>
   <?php }  ?>
   <li DISABLED>&nbsp;</li>
   <li><a href="#" onclick="call_signout();"  class="top-sign-in">Sign Out</a></li>
            </ul>
            </li>
							</ul>

						</div>
					</div>
				</div>
			</div>
		</header>

<form name="postmeform" action="" method="POST" class="hidden">
  <input type="hidden" name="sid" value="<?php echo $l_sid; ?>">
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


<?php
if ( isset( $_GET["info"] ) ) 
{
    $l_flash_info_show="block";
    $l_flash_info= $_GET["info"];
    //$l_mess = urldecode($_GET["info"]);
}

 //$l_user="useragreement_kumar
 //echo " strpos= " . strpos($l_user,"useragreement_");
?>


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
