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
      $l_obj = $ux->getTableRecord($p_obj,"vitals_list","id",$p_record_id);

      $fieldtotal = $l_obj->{"total_records"};

      $p_rec =array();
      if ( $l_obj->{"total_records"} == 1 )
          $p_rec=$l_obj->{"record"}[0];

      $l_arr =array();
      $l_arr_ctr =0;

      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="Vital" . $dlim ."vital". $dlim ." text". $dlim ."1" . $dlim . $p_rec["vital"];
      $l_arr[$l_arr_ctr++]="Graph Min Value" . $dlim ."graph_min". $dlim ." number". $dlim ."1" . $dlim . $p_rec["graph_min"];
      $l_arr[$l_arr_ctr++]="Graph Max Value" . $dlim ."graph_max". $dlim ." number". $dlim ."1" . $dlim . $p_rec["graph_max"];
      $fieldtotal = $l_arr_ctr;

?>
<h4 class='login-box-head'>Vital</h4>
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
       minlength="<?php echo $l_fld_minlength; ?>" type="<?php echo $l_fld_type; ?>" 
  title="<?php echo $l_fld_title; ?>" class='span6' placeholder='<?php $l_fld_title; ?>...' 
  <?php
  if($l_fld_type == "number")
  {
  	echo " step='any' ";
  } ?> >
</div>
</div>
<?php 
   }
?>
</BR>
<div class="row">
  <div class='span6'>
  <label>Graph Type</label><select  class="span5" id="record_graph_type">
<?php
     $l_graph_type  = $p_rec["graph_type"];
     $ct = ConstantModel::get(); 
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get();
     
         $sql = "SELECT * FROM vitals_graph_types";
         $ss_obj = $dx->getOnlyRecords($sql);
		          
    $s_fieldtotal = $ss_obj->{"total_records"};
     for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
     {
        $rr_rec=$ss_obj->{"record"}[$s_idx];
        $p_nm=$rr_rec["graph_type"];
        $p_sel="";
        if ( $p_nm == $l_graph_type ) 
           $p_sel="SELECTED";
        echo "<OPTION $p_sel value='". $p_nm . "'>$p_nm</OPTION>";
     }
     
?>
  </select>
  </div>
  </div>
<BR/>
  <p>
  <input CHECKED=1 type="checkbox"  id="record_isactive" />
  &nbsp;&nbsp;Is Active
  </p>
  <BR/>

			<div class='login-actions'>
				<input type="button"  onClick="callme('record,vital,')" value="Save" />
<input type="button"  onClick="callform('vitallist.php')" value="Go Back" />
			</div>
</div>
<?php include("foot.php") ?>
