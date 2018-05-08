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
      $l_obj = $ux->getTableRecord($p_obj,"diagnosis_list","id",$p_record_id);

      $fieldtotal = $l_obj->{"total_records"};

      $p_rec =array();
      if ( $l_obj->{"total_records"} == 1 )
          $p_rec=$l_obj->{"record"}[0];

      $l_arr =array();
      $l_arr_ctr =0;
      $description = $p_rec["description"];
      $description = stripslashes( $description );
      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="Diagnosis" . $dlim ."diagnosis". $dlim ." text". $dlim ."1" . $dlim . $p_rec["diagnosis"];
      //$l_arr[$l_arr_ctr++]="Description" . $dlim ."description". $dlim ." text". $dlim ."0" . $dlim . $p_rec["description"];
      $l_arr[$l_arr_ctr++]="URL" . $dlim ."url". $dlim ." text". $dlim ."0" . $dlim . $p_rec["url"];
      $fieldtotal = $l_arr_ctr;

?>
<h4 class='login-box-head'>Diagnosis</h4>
<input type="hidden" id="record_id" value="<?php echo $p_record_id; ?>" />

<script type="text/javascript">
function diagnosissave()
{
	var editor_data = CKEDITOR.instances.record_description.getData();
	var editor_data = editor_data.replace(/&quot;/g,'"');
	var editor_data = editor_data.replace(/&#39;/g," "); //This is not working to get single quotes back.
	var editor_data = editor_data.replace(/&nbsp;/g,'\n');
	$("#record_description").val(editor_data);
    callme('record,savediagnosislist,');
}
</script>
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

<div class="row">
<div class='span6'>
<label>Description</label>
<textarea id="record_description" 
       minlength="0" 
  title="Description" class='span6'><?php echo $description; ?></textarea>
  <script type="text/javascript">
        CKEDITOR.replace( 'record_description' );
      </script>
</div>
</div>
<BR/>
  <p>
  <input CHECKED=1 type="checkbox"  id="record_isactive" />
  &nbsp;&nbsp;Is Active
  </p>
  <BR/>


            <div class='login-actions'>
                <input type="button"  onClick="diagnosissave()" value="Save" />
<input type="button"  onClick="callform('diagnosislist.php')" value="Go Back" />
            </div>

<?php include("foot.php") ?>
