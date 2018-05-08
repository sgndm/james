<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $l_unique_id= date('YmdHis').$ctrl->createUniqueID(8);
      $p_record_id = $ctrl->getPostParamValue("record_id");
      if ( $p_record_id == null || $p_record_id == "" )
         $p_record_id = "0";


      $user_id= $ctrl->getUserID();
      $user_type = $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
      
      $l_obj = $ux->getTableRecord($p_obj,"user","id",$p_record_id);

      $fieldtotal = $l_obj->{"total_records"};

      $p_rec =array();
      if ( $l_obj->{"total_records"} == 1 )
          $p_rec=$l_obj->{"record"}[0];
      $l_arr =array();
      $l_arr_ctr =0;
      
      $allClinics = $ux->getAllFromUserType(2); //two is for clinics.

      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="Name" . $dlim ."first_name". $dlim ." text". $dlim ."1" . $dlim . $p_rec["first_name"];
      $l_arr[$l_arr_ctr++]="User Name" . $dlim ."username". $dlim ." text". $dlim ."1" . $dlim . $p_rec["username"];
      $l_arr[$l_arr_ctr++]="Password" . $dlim ."password". $dlim ." text". $dlim ."0" . $dlim . "";
	  $l_arr[$l_arr_ctr++]="Email" . $dlim ."email". $dlim ."text". $dlim ."1" . $dlim . $p_rec["email"];
      $l_arr[$l_arr_ctr++]="Designation" . $dlim ."designation". $dlim ." text". $dlim ."1" . $dlim . $p_rec["designation"];
      $l_arr[$l_arr_ctr++]="Hours" . $dlim ."operation_hours". $dlim ." text". $dlim ."1" . $dlim . $p_rec["operation_hours"];
      //$l_arr[$l_arr_ctr++]="Phone" . $dlim ."phone". $dlim ."tel". $dlim ."1" . $dlim . $p_rec["phone"];
      $fieldtotal = $l_arr_ctr;
      $l_photourl = $p_rec["photourl"];

?>
<script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="js/telinput/css/intlTelInput.css">
	<script src="js/telinput/js/intlTelInput.js"></script>
<script type="text/javascript">
function doctorsave()
{
	var cleanNumber = $('#mobile-number').intlTelInput("getCleanNumber");
	var countryData = $("#mobile-number").intlTelInput("getSelectedCountryData");
	var dialCode = countryData["dialCode"]; //does not include +
	var withoutDialCode = cleanNumber.replace("+" + dialCode, "");
	$("#record_phone").val(withoutDialCode);
	$("#record_dial_code").val(dialCode);
	
   callme('record,doctor,');
}
</script>
<h4 class='login-box-head'>Doctor</h4>

<div class="row">
<div class='span6'>
<input type="hidden" id="record_formname" value="doctor" />
<input type="hidden" id="record_copied_file" value="" />
<input type="hidden" id="record_uniqueid" value="<?php echo $l_unique_id; ?>" />
<input type="hidden" id="record_filecreated" value="0" />
<input type="hidden" id="record_user_type" value="3" />
<input type="hidden" id="record_file_type" value="profile" />
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
   }?>
   
   <input type="hidden" id="record_phone" value="<?php echo $p_rec["phone"]; ?>" />
<input type="hidden" id="record_dial_code" value="<?php echo $p_rec["dial_code"]; ?>" />
  <div class="row">
<div class='span6'>
	<label>Phone Number</label>
	<input type="text" id="mobile-number" class="form-control span6" />
	<script type="text/javascript">
  		$(document).ready(function() 
    	{ 
    		callTel();
    		var phone = $("#record_phone").val();
    		var dial = $("#record_dial_code").val();
    		var stringToAddToMobileNumber = "+" + dial + phone;
    		$("#mobile-number").intlTelInput("setNumber", stringToAddToMobileNumber);
    		
    	});
	</script>
</div>
</div>
   
   <label>Clinics</label>
<?php
   foreach ($allClinics as $key => $value) 
   {
       $v=$value[0];
       $sql = "SELECT first_name FROM user WHERE id = $v";
       $clinicName = $ctrl->getRecordID($sql);
       $sql = "SELECT isConnected FROM clinic_doctor WHERE clinic_id = $v AND doctor_id = $p_record_id";
       $connected = $ctrl->getRecordID($sql);
       
?>

<div class="row">
<div class='span6'>
    <p>
        <input type="checkbox" id="record_<?php echo $v; ?>" name="record_<?php echo $v; ?>" value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        &nbsp;&nbsp;<?php echo $clinicName; ?>
    </p>
</div>
</div>
<?php 
   }
?>
<div class="row">
<div class='span6'>
<label>Image</label>
<input   onChange="call_image()" id="profileimage" class='span4' placeholder='Select image ...' type='file'/>
</div>
</div>
  <BR/>
  <BR/>
  <p>
  <input CHECKED=1 type="checkbox"  id="record_isactive" />
  &nbsp;&nbsp;Is Active
  </p>
  <BR/>

</div>
<div class='span6'>
<img style="width:30%; height:30%" src="<? echo $l_photourl ; ?>"  />
</div>
</div>

			<div class='login-actions'>
<?php if($ux->doIHavePermission($user_type, "save_doctor")) { ?>			    
<input type="button" onClick="doctorsave()" value="Save" />
<?php } ?>
<input type="button" onClick="callform('doctorlist.php')" value="Go Back" />
<?php
if ( $p_record_id > 0 ) 
{
?>
<input type="button" onclick="callform('doctor.php',<?php echo $p_record_id; ?>,'')" value="Refresh " />
<?php } ?>
			</div>
</div>
<?php include("foot.php") ?>
