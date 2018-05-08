<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $p_record_id = $p_patient_id;
      $user_id= $ctrl->getUserID();
      $user_type= $ctrl->getUserType();
      $p_old_patient=$_SESSION["old_patient"];

      $data='{"segment":"pid"}';
      $p_rec = $ux->getPatient($p_record_id);
      $l_photourl = $p_rec["photourl"];

      $l_name = $p_rec["first_name"]. " ".
                $p_rec["last_name"];

      $l_arr =array();
      $l_arr_ctr =0;
      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="First Name" . $dlim ."first_name". $dlim ."text". $dlim ."1" . $dlim . $p_rec["first_name"];
      $l_arr[$l_arr_ctr++]="Last Name" . $dlim ."last_name". $dlim ."text". $dlim ."1" . $dlim . $p_rec["last_name"];
      $l_arr[$l_arr_ctr++]="Email" . $dlim ."email". $dlim ."email". $dlim ."1" . $dlim . $p_rec["email"];
      $l_arr[$l_arr_ctr++]="Reason For Visit" . $dlim ."reason". $dlim ."text". $dlim ."1" . $dlim . $p_rec["reason"];
      //$l_arr[$l_arr_ctr++]="Phone" . $dlim ."phone". $dlim ."text". $dlim ."10" . $dlim . $p_rec["phone"];
      $l_arr[$l_arr_ctr++]="UserName" . $dlim ."username". $dlim ."text". $dlim ."1" . $dlim . $p_rec["username"];
      $l_arr[$l_arr_ctr++]="Password" . $dlim ."password". $dlim ."password". $dlim ."0" . $dlim . "";
      $l_arr[$l_arr_ctr++]="Created" . $dlim ."created_ts". $dlim ."text". $dlim ."0" . $dlim . $p_rec["created_ts_fmt"]. $dlim . "DISABLED";

      $l_arr[$l_arr_ctr++]="Surgical Procedure" . $dlim ."surgical_procedure". $dlim ."text". $dlim ."0" . $dlim . $p_rec["surgical_procedure"];

      $l_arr[$l_arr_ctr++]="Medical Record URL" . $dlim ."medical_record_url". $dlim ."text". $dlim ."0" . $dlim . $p_rec["medical_record_url"];

      $l_arr[$l_arr_ctr++]="Discharge Date " . $dlim ."date_of_discharge". $dlim ."text". $dlim ."0" . $dlim . $p_rec["date_of_discharge_fmt"];

?>
<script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="css/surgical.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/patient.css" media="all" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="js/telinput/css/intlTelInput.css">
	<script src="js/telinput/js/intlTelInput.js"></script>
<script type="text/javascript">
function patientsave()
{
	var cleanNumber = $('#mobile-number').intlTelInput("getCleanNumber");
	var countryData = $("#mobile-number").intlTelInput("getSelectedCountryData");
	var dialCode = countryData["dialCode"]; //does not include +
	var withoutDialCode = cleanNumber.replace("+" + dialCode, "");
	$("#record_phone").val(withoutDialCode);
	$("#record_dial_code").val(dialCode);
	
   callme('record,savepatient,');
}
function localsavepatient(pdata)
{
  kobj = JSON.parse(pdata);

  if ( kobj.success == "false" )
  {
     show_error  ( kobj.message);
  }
  else if ( kobj.success == "true" )
  {
     show_info("successfully updated")
     for ( var idx=0; idx<kobj.INSERTED_ROWS;idx++)
     {
        l_x  = kobj.INSERTED_DATA[idx];
        l_name  = "#"+l_x.fld_name;
        l_val  = l_x.fld_value;
        $(l_name).val(l_val);
     }
<?php if ( $p_old_patient == "0" ) { ?>
     parent.resettab("md");
<?php } ?>
  }
}
</script>
<input type="hidden" id="record_id" value="<?php echo $p_record_id; ?>" />
<input type="hidden" id="record_copied_file" value="" />
<input type="hidden" id="record_formname" value="patient" />
<input type="hidden" id="record_file_type" value="profile" />
<?php
      for ( $idx =0; $idx <$l_arr_ctr; $idx++)
{
      $rec=$l_arr[$idx];

      $lfld = explode($dlim,$rec);
      $l_fld_title=  $lfld[0];
      $l_fld_id=  $lfld[1];
      if ( $l_fld_id == "username" ) 
      {
?>

<div class="row">
  <div class='span6'>
  <label>Discharge Diagnosis</label><select  class="span5" id="record_discharge_diagnosis">
<?php
     $l_diagnosis_id  = $p_rec["discharge_diagnosis"];
     $ct = ConstantModel::get(); 
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get();
     
         $sql = "SELECT * FROM diagnosis_list";
         $ss_obj = $dx->getRecords($p_obj, $sql);
         
    $s_fieldtotal = $ss_obj->{"total_records"};
     for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
     {
        $rr_rec=$ss_obj->{"record"}[$s_idx];
        $p_nm=$rr_rec["diagnosis"];
        $p_id=$rr_rec["id"];
        $p_sel="";
        if ( $p_id == $l_diagnosis_id ) 
           $p_sel="SELECTED";
        echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
     }
     
?>
  </select>
  </div>
  </div>

<input type="hidden" id="record_phone" value="<?php echo $p_rec["phone"]; ?>" />
<input type="hidden" id="record_dial_code" value="<?php echo $p_rec["dial_code"]; ?>" />
  <div class="row">
<div class='span6'>
	<label>Phone Number</label>
	<input type="text" id="mobile-number" class="form-control span5" />
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


  <div class="row">
  <div class='span6'>
  <label>Phone Carrier</label><select  class="span5" id="record_phone_carrier">
<?php
     $l_phone_carrier  = $p_rec["phone_carrier"];
     $ct = ConstantModel::get(); 
     $str = $ct->getsmsgatewayaddress();
     $sms_ar = explode(";_:",$str);
     for ( $p_idx=0; $p_idx< count($sms_ar); $p_idx++)
     {
         $l_s1 = $sms_ar[$p_idx];
         $l_fld_ar = explode(",",$l_s1);

         $l_nm=$l_fld_ar[0];
         $l_val=$l_fld_ar[1];
         $lselected = "";
          if ( $l_phone_carrier  == $l_val )
            $lselected = "SELECTED";
       echo "<option value=\"$l_val\" $lselected >$l_nm</option>";
      }
?>
  </select>
  </div>
  </div>

  <div class="row">
  <div class='span6'>
  <label>Clinic</label><select  class="span5" id="record_clinic_id">
<?php
     $l_clinic_id  = $p_rec["clinic_id"];
     $ct = ConstantModel::get(); 
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get();
     
     if($user_type == "2")//Clinic
     {
         $sql = "SELECT * FROM user WHERE id = $user_id";
         $ss_obj = $dx->getRecords($p_obj, $sql);
     } 
     else if($user_type == "3")//Doctor
     {
         $sqlClinicIds = "SELECT clinic_id FROM clinic_doctor WHERE doctor_id = $user_id AND isConnected = 'Y'";
         $clinicIds = $ctrl->getRecordIDs($sqlClinicIds);
         $sql = "SELECT * FROM user WHERE id = 0";
         foreach ($clinicIds as $key => $value) {
             $sql .= " OR id = $value[0] ";
         }
         $ss_obj = $dx->getRecords($p_obj, $sql);
     }
     else if($user_type == "4") //Care Coordinator
     {
         $sqlClinicId = "SELECT clinic_id FROM user WHERE id = $user_id";
         $clinicId = $ctrl->getRecordID($sqlClinicId);
         $sql = "SELECT * FROM user WHERE id = $clinicId";
         $ss_obj = $dx->getRecords($p_obj, $sql);
     }
     else //Admin
    {
        $ss_obj = $dx->getClinics($p_obj);
    }
     
     $s_fieldtotal = $ss_obj->{"total_records"};
     for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
     {
        $rr_rec=$ss_obj->{"record"}[$s_idx];
        $p_nm=$rr_rec["first_name"];
        $p_id=$rr_rec["id"];
        $p_sel="";
        if ( $p_id == $l_clinic_id ) 
           $p_sel="SELECTED";
        echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
     }
?>
  </select>
  </div>
  </div>



  <div class="row">
  <div class='span6'>
  <label>Doctor</label><select  class="span5" id="record_doctor_id">
<?php
     $l_doctor_id  = $p_rec["doctor_id"];
     $ct = ConstantModel::get(); 
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get(); 
     if($user_type == "2")//Clinic
     {
         $sqlDoctorIds = "SELECT doctor_id FROM clinic_doctor WHERE clinic_id = $user_id AND isConnected = 'Y'";
         $doctorIds = $ctrl->getRecordIDs($sqlDoctorIds);
         $sql = "SELECT * FROM user WHERE id = 0";
         foreach ($doctorIds as $key => $value) {
             $sql .= " OR id = $value[0] ";
         }
         $ss_obj = $dx->getRecords($p_obj, $sql);
     }
     else if($user_type == "3")//Doctor
     {
         $sql = "SELECT * FROM user WHERE id = $user_id";
         $ss_obj = $dx->getRecords($p_obj, $sql);
     }
     else if($user_type == "4") //Care Coordinator
     {
         $sqlClinicId = "SELECT clinic_id FROM user WHERE id = $user_id";
         $clinicId = $ctrl->getRecordID($sqlClinicId);
         $sqlDoctorIds = "SELECT doctor_id FROM clinic_doctor WHERE clinic_id = $clinicId AND isConnected = 'Y'";
         $doctorIds = $ctrl->getRecordIDs($sqlDoctorIds);
         $sql = "SELECT * FROM user WHERE id = 0 ";
         foreach ($doctorIds as $key => $value) {
             $sql .= "OR id = $value[0] ";
         }
         $ss_obj = $dx->getRecords($p_obj, $sql);
     }
    else //Admin
    {
        $ss_obj = $dx->getDoctors($p_obj);
    }
     
     
     $s_fieldtotal = $ss_obj->{"total_records"};
     for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
     {
        $rr_rec=$ss_obj->{"record"}[$s_idx];
        $p_nm=$rr_rec["first_name"];
        $p_id=$rr_rec["id"];
        $p_sel="";
        if ( $p_id == $l_doctor_id ) 
           $p_sel="SELECTED";
        echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
     }
?>
  </select>
  </div>
  </div>


<?php
      }
      $l_fld_type=  $lfld[2];
      $l_fld_minilength="".$lfld[3];
      $l_fld_value=  $lfld[4];
      $l_fld_disabled=  "";
      if ( count($lfld) > 5 )
         $l_fld_disabled=  $lfld[5];



?>
<div class="row">
<div class='span6'>
<label><?php echo $l_fld_title; ?></label>
<input <?php echo $l_fld_disabled; ?> 
minlength="<?php echo $l_fld_minilength; ?>" 
id="record_<?php echo $l_fld_id; ?>" 
value="<?php echo $l_fld_value; ?>" 
title="<?php echo $l_fld_title; ?>" 
class='span5' placeholder='<?php $l_fld_title; ?>...' 
type="<?php echo $l_fld_type; ?>"
datatype="<?php echo $l_fld_type; ?>" >

</div>
</div>
<?php 
}
?>




<div class="row">
<div class='span6'>
<label>Image</label>
<input   onChange="call_image()" id="profileimage" class='span5' placeholder='Select image ...' type='file'/>
  <BR/>
  <p>
<?php
  $l_ignore = $p_rec["ignore_execptions"];
  $l_checked="";
  if ( $l_ignore == "Y" )
      $l_checked="CHECKED=1";
?>
  <input <?php echo $l_checked; ?> type="checkbox"  id="record_ignore" />
  Ignore Exceptions
  </p>
  <BR/>
  <p>
<?php
  $l_isactive = $p_rec["isactive"];
  $l_checked="";
  if ( $l_isactive == "Y" )
      $l_checked="CHECKED=1";
?>
  <input <?php echo $l_checked; ?> type="checkbox"  id="record_isactive" />
  Is Active
  </p>
  <BR/>
</div>
</div>

<?php if ( $p_old_patient == "1" ) {  ?>
<div class="row">
<div class='span10'>
<input type="button" onClick="patientsave()" value="Save" />
</div>
</div>
<?php } else {  ?>
<div class="row">
<div class='span2'>
     <input type="button" onClick="goto_patient('profile')" value="Cancel" />
</div>
<div class='span6'>
</div>
<div class='span2'>
     <img src="assets/images/right_md.png" onClick='patientsave()' />
</div>
</div>
<?php }  ?>

  <script>
  $(function() {
    $( "#record_date_of_discharge" ).datepicker();
  });
    </script>
