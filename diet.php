<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $p_record_id = $_POST["record_id"];
      if ( $p_record_id == null || $p_record_id == "" )
         $p_record_id = "0";
      $user_id= $ctrl->getUserID();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
      $l_obj = $ux->getTableRecord($p_obj,"diet_list","id",$p_record_id);

      $fieldtotal = $l_obj->{"total_records"};

      $p_rec =array();
      if ( $l_obj->{"total_records"} == 1 )
          $p_rec=$l_obj->{"record"}[0];

      $l_arr =array();
      $l_arr_ctr =0;

      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="Diet" . $dlim ."diet". $dlim ." text". $dlim ."1" . $dlim . $p_rec["diet"];
      $l_arr[$l_arr_ctr++]="URL" . $dlim ."url". $dlim ." text". $dlim ."0" . $dlim . $p_rec["url"];
      $fieldtotal = $l_arr_ctr;

?>
<h4 class='login-box-head'>Diet</h4>
<input type="hidden" id="record_id" value="<?php echo $p_record_id; ?>" />
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
{
      $rec=$l_arr[$idx];

      $lfld = explode($dlim,$rec);
      $l_fld_title=  $ctrl->mytrim($lfld[0]);
      $l_fld_id=  $ctrl->mytrim($lfld[1]);
      $l_fld_type=  $ctrl->mytrim($lfld[2]);
      $l_fld_minlength=  $ctrl->mytrim($lfld[3]);
      $l_fld_value=  $ctrl->mytrim($lfld[4]);
?>
<div class="row">
<div class='span6'>
<label><?php echo $l_fld_title; ?></label>
<input id="record_<?php echo $l_fld_id; ?>" value="<?php echo $l_fld_value; ?>" 
       minlength="<?php echo $l_fld_minlength; ?>" 
  title="<?php echo $l_fld_title; ?>" class='span6' placeholder='<?php $l_fld_title; ?>...' type='text'>
</div>
</div>
<?php 
   }
?>
  <p>
  <input CHECKED=1 type="checkbox"  id="record_isactive" />
  &nbsp;&nbsp;Is Active
  </p>

			<div class='login-actions'>
				<input type="button"  onClick="callme('record,savedietlist,')" value="Save" />
<input type="button"  onClick="callform('dietlist.php')" value="Go Back" />
			</div>
</div>
<?php include("foot.php") ?>
