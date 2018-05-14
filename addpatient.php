<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get();
      $ux = UserModel::get();
      $l_unique_id= date('YmdHis').$ctrl->createUniqueID(8);
      $user_id= $ctrl->getUserID();
      $user_type= $ctrl->getUserType();
      $p_patient_id=0;
      $p_id = 0;
      if ( $p_id == null || $p_id == "" )
         $p_id = "0";

      $l_arr =array();
      $l_arr_ctr =0;

      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="First Name" . $dlim ."first_name". $dlim ." text". $dlim ."1" . $dlim . "";
      $l_arr[$l_arr_ctr++]="Last Name" . $dlim ."last_name". $dlim ."text". $dlim ."1" . $dlim . "";
      $l_arr[$l_arr_ctr++]="Email" . $dlim ."email". $dlim ."email". $dlim ."1" . $dlim . "";
      $l_arr[$l_arr_ctr++]="Reason for Visit" . $dlim ."reason". $dlim ."text". $dlim ."1" . $dlim . "";
      //$l_arr[$l_arr_ctr++]="Phone" . $dlim ."phone". $dlim ."text". $dlim ."1" . $dlim . "";
      $l_arr[$l_arr_ctr++]="UserName" . $dlim ."username". $dlim ."text". $dlim ."1" . $dlim . "";
      $l_arr[$l_arr_ctr++]="Password" . $dlim ."password". $dlim ."password". $dlim ."0" . $dlim . "";
      //$l_arr[$l_arr_ctr++]="Discharge Diagnosis" . $dlim ."discharge_diagnosis". $dlim."text". $dlim ."0". $dlim . "";

      $l_arr[$l_arr_ctr++]="Surgical Procedure" . $dlim ."surgical_procedure". $dlim ."text". $dlim ."0" .$dlim . "";

      $l_arr[$l_arr_ctr++]="Medical Record URL" . $dlim ."medical_record_url". $dlim ."text". $dlim ."0" .$dlim . "";

      $l_arr[$l_arr_ctr++]="Discharge Date " . $dlim ."date_of_discharge". $dlim ."text". $dlim ."0" . $dlim . "";

      $fieldtotal = $l_arr_ctr;

?>

<script type="text/javascript">
function patientsave()
{
  // alert('patient save');
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
  // console.log(pdata);
  // alert('local save patient');
  kobj = JSON.parse(pdata);
  if ( kobj.success == "false" )
  {
    // alert('eror');
     show_error  ( kobj.message);
  }
  else if ( kobj.success == "true" )
  {
    // alert('success');
     show_info("Successfully updated")
     for ( var idx=0; idx<kobj.INSERTED_ROWS;idx++)
     {
        l_x  = kobj.INSERTED_DATA[idx];
        l_name  = "#"+l_x.fld_name;
        l_val  = l_x.fld_value;
        $(l_name).val(l_val);
     }
     var recordID = $("#record_id").val();
     //alert(recordID);
     callpatientform('patient.php',recordID,'profile','');
     //window.location.href = 'myhome.php';
  }
}
</script>
<h4 class='login-box-head'>New Patient</h4>
<input type="hidden" id="patient_id" value="<?php echo $p_id; ?>" />
<input type="hidden" id="record_id" value="0" />
<input type="hidden" id="record_uniqueid" value="<?php echo $l_unique_id; ?>" />
<input type="hidden" id="record_formname" value="patient" />
<input type="hidden" id="record_copied_file" value="" />
<input type="hidden" id="record_filecreated" value="0" />
<input type="hidden" id="record_file_type" value="profile" />
<input type="hidden" name="record_isactive " value="false">
<input type="hidden" name="record_ignore" value="false">
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
      if ( $l_fld_id == "username" )
      {
         $l_phone_carrier  = "";
?>
<div class="row">
  <div class='span6'>
  <label>Discharge Diagnosis</label><select  class="span5" id="record_discharge_diagnosis">
    <option value="0">Select Diagnosis</option>
    <option value="diagnosis">diagnosis</option>
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


<input type="hidden" id="record_phone" value="" />
<input type="hidden" id="record_dial_code" value="" />
  <div class="row">
<div class='span6'>
	<label>Phone Number</label>
	<input type="text" id="mobile-number" class="form-control span5" />
</div>
</div>


  <div class="row">
  <div class='span6'>
  <label>Phone Carrier</label><select  class="span5" id="record_phone_carrier">
    <option value="0">Select Phone Carrier</option>
    <option value="pc_01">Phone carrier 01</option>
<?php
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
         if ( $p_idx == 0 )
         {
            $l_phone_carrier  = $l_val;
            $lselected = "SELECTED";
          }
       echo "<option value=\"$l_val\" $lselected >$l_nm</option>";
      }
?>
  </select>
  </div>
  </div>

  <div class="row">
  <div class='span6'>
  <label>Clinic</label><select  class="span5" id="record_clinic_id">
    <option value="0">Select clinic</option>
    <option value="3">Clinic 1</option>
<?php
     $l_clinic_id  = $p_rec["clinic_id"];
     $ct = ConstantModel::get();
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get();
     if($user_type == "1")
     {
         $sql = "SELECT * FROM user WHERE user_type = 2";
         $ss_obj = $dx->getRecords($p_obj, $sql);
     }
     else if($user_type == "2")//Clinic
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
     else //Care Coordinator
     {
         $sqlClinicId = "SELECT clinic_id FROM user WHERE id = $user_id";
         $clinicId = $ctrl->getRecordID($sqlClinicId);
         $sql = "SELECT * FROM user WHERE id = $clinicId";
         $ss_obj = $dx->getRecords($p_obj, $sql);
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
    <option value="0">Select D</option>
    <option value="2">D 1</option>
<?php
     $l_doctor_id  = $p_rec["doctor_id"];
     $ct = ConstantModel::get();
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get();
     if($user_type == "1")
     {
         $sql = "SELECT * FROM user WHERE user_type = 3";
         $ss_obj = $dx->getRecords($p_obj, $sql);
     }
     else if($user_type == "2")//Clinic
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
     else //Care Coordinator
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
?>
<div class="row">
<div class='span6'>
<label><?php echo $l_fld_title; ?></label>
<input id="record_<?php echo $l_fld_id; ?>" value="<?php echo $l_fld_value; ?>"
       minlength="<?php echo $l_fld_minlength; ?>"
  title="<?php echo $l_fld_title; ?>" class='span5' placeholder='<?php $l_fld_title; ?>...' type='<?php echo $l_fld_type; ?>' datatype='<?php echo $l_fld_type; ?>'>
</div>
</div>
<?php
   }
?>
<div class="row">
<div class='span6'>
<label>Image</label>
<input   onChange="call_image()" id="profileimage" class='span5' placeholder='Select image ...' type='file'/>
</div>
</div>
  <BR/>
<div class="row">
<div class='span2'>
				<input type="button" onClick="call_home()" value="Go Back" />
				<input type="button" value="Save" onClick="patientsave()" />
</div>
<div class='span2'>


</div>


</div>
</div>
<?php include("foot.php") ?>
  <script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <link rel="stylesheet" href="js/telinput/css/intlTelInput.css">
	<script src="js/telinput/js/intlTelInput.js"></script>
  <script>
  $(function() {
    $( "#record_date_of_discharge" ).datepicker();
    callTel();
    var phone = $("#record_phone").val();
    var dial = $("#record_dial_code").val();
    var stringToAddToMobileNumber = "+" + dial + phone;
    $("#mobile-number").intlTelInput("setNumber", stringToAddToMobileNumber);


  });
    </script>
