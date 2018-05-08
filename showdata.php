<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $datamodel = DataModel::get();
      $l_user_id= $ctrl->getUserID();

      $l_id=$_GET["segment"];
      if ( $l_id == "obx" ) 
      {
         $l_title="Lab Results";
         $data='{"segment":"obx"}';
      }
      if ( $l_id == "rxo" ) 
      {
         $l_title="My Prescription";
         $data='{"segment":"rxo"}';
      }

      $p_obj=json_decode($data);
      $p_obj = $datamodel->getHl7Data($p_obj);

      $fieldtotal = $p_obj->{"total_fields"};
      $recordtotal = $p_obj->{"total_records"};
?>

<h4 class='login-box-head'><?php echo $l_title; ?></h4>

<input type="hidden" id="profile_calledby" value="user" />
<input type="hidden" id="profile_user_id" value="<?php echo $l_user_id; ?>" />
<?php
for ( $r_idx =0; $r_idx <$recordtotal; $r_idx++)
{
  $firstrecord=1;
for ( $idx =0; $idx <$fieldtotal; $idx++)
{
      $lx = "fieldname_" . ( $idx + 1);
      $l_fld_name=  $p_obj->{$lx};
      $lx = "fieldvalue_" . $r_idx . "_" . ($idx+1);
      $l_fld_value=  $p_obj->{$lx};


      if ( $l_fld_value == '""'   )
         continue;

      if ( $l_fld_value == "^^^"   )
         continue;

      if ( $l_fld_value == ""   )
         continue;

      $l_color="black";
      if ( $firstrecord == 1 ) 
      {
         $l_color="#B50128";
         $firstrecord = 0;
      }
 
?>
<div class="row">
<div class='span6'>
<label style="color:<?php echo $l_color; ?>"> <?php echo $l_fld_name; ?></label>
<input DISABLED=1 minlength=0 id="profile_<?php echo $l_fld_pid; ?>" value="<?php echo $l_fld_value; ?>" 
  title="<?php $l_fld_name; ?>" class='span6' placeholder='<?php $l_fld_name; ?>...' type='text'>
</div>
</div>
<?php 
}
}

?>

</div>
<?php include("foot.php") ?>
