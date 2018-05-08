<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $ux = UserModel::get(); 
      $p_record_id = $_POST["record_id"];
      $p_patient_id = $_POST["patient_id"];
      if ( $p_record_id == null || $p_record_id == "" )
         $p_record_id = "0";

      $user_id= $ctrl->getUserID();
      $user_type= $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
      $l_obj = $dx->getAppointment($p_obj,$p_record_id);
      $fieldtotal = $l_obj->{"total_records"};

      $p_rec =array();
      if ( $l_obj->{"total_records"} == 1 )
          $p_rec=$l_obj->{"record"}[0];

      $l_arr =array();
      $l_arr_ctr =0;

      $l_dt  = $p_rec["appointment_ts_mmddyyy"];
      $l_d1  = $p_rec["appointment_ts"];

      $l_time  = $p_rec["appointment_ts_time"];
      $l_doctor_id  = $p_rec["doctor_id"];
      $l_clinic_id  = $p_rec["clinic_id"];
      $l_doctor  = $p_rec["doctor_name"];

      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="Date" . $dlim ."date". $dlim ." text". $dlim ."1" . $dlim . $l_dt;
      $l_arr[$l_arr_ctr++]="Time" . $dlim ."time". $dlim ." text". $dlim ."1" . $dlim . $l_time;
      $fieldtotal = $l_arr_ctr;

?>
<h4 class='login-box-head'>Appointment</h4>
<input type="hidden" id="record_id" value="<?php echo $p_record_id; ?>" />
<input type="hidden" id="record_patient_id" value="<?php echo $p_patient_id; ?>" />
<?php
   $data='{"user_id":"' . $user_id . '"}';
   $s_obj=json_decode($data);
   $s_obj = $dx->getDoctors($p_obj);
   $s_fieldtotal = $s_obj->{"total_records"};
?>
   <p>
   Doctor 
   <BR/>
   <select  id="record_doctor_id" >
<?php
   $l_doctor_id  = $p_rec["doctor_id"];
     $ct = ConstantModel::get(); 
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get(); 
     if($user_type == "1")//Admin
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
         $sql = "SELECT * FROM user WHERE user_type = 3";
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
  </SELECT>
   </p>
<?php
   $data='{"user_id":"' . $user_id . '"}';
   $s_obj=json_decode($data);
   $s_obj = $dx->getClinics($p_obj);
   $s_fieldtotal = $s_obj->{"total_records"};
?>
   <p>
   Clinic
   <BR/>
   <select  id="record_clinic_id" >
<?php
   $l_clinic_id  = $p_rec["clinic_id"];
     $ct = ConstantModel::get(); 
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get();
     if($user_type == "1")//Admin
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
  </SELECT>
   </p>

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
  <BR/>
  <p>
  <input CHECKED=1 type="checkbox"  id="record_isactive" />
  &nbsp;&nbsp;Is Active
  </p>
  <BR/>
<div class='login-actions'>
<input type="button" onClick="callme('record,userappt,')" value="Save" />
<input type="button" onClick="goto_patient('appt')" value="Go Back" />
</div>
</div>
<?php include("foot.php") ?>
  <script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="js/jquery.timepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="css/jquery.timepicker.css" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script>
  $(function() {
    $('#record_time').timepicker(({ 'timeFormat': 'H:i' , 'step': 5 }));
    $( "#record_date" ).datepicker();
  });
    </script>
