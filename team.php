<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $p_id=0;
?>
<h4 class='login-box-head'>Team</h4>
<input type="hidden" id="patient_id" value="<?php echo $p_id; ?>" />
<BR/>
<h3> ....work in progress ...</h3>
<BR/>
<BR/>
<?php include("foot.php") ?>
