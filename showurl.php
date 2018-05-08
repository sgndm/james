<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $datamodel = DataModel::get();
      $l_user_id= $ctrl->getUserID();
      $l_url = $_GET["param"];

?>

<input type="hidden" id="profile_calledby" value="user" />
<input type="hidden" id="profile_user_id" value="<?php echo $l_user_id; ?>" />

<div style="border-style:solid;border-color:#ff0000 #0000ff;height:400px" class='span12'>
<iframe style="height:100%;width:100%;frameborder:1" src="<?php echo $l_url; ?>"></iframe>
</div>
<?php include("foot.php") ?>
