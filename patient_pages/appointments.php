<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $p_form_name="user_appointment.php";

      $l_obj = $dx->getAppointments($x_obj);
      $fieldtotal = $l_obj->{"total_records"};

?>
<h4 class='login-box-head'>Appointments</h4>
<div class="row">
<div class='span10'>
<table border=2>
<tr>
<th class="span3" >Time</th>
<th class="span2" >Doctor</th>
<th class="span2" >Clinic</th>
<th class="span3" >Address</th>
</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
         $rec=$l_obj->{"record"}[$idx];
         $l_time  =  $rec["appointment_ts_fmt"];
         $l_id  =  $rec["id"];
         $l_doctor  =  $rec["doctor_id"];
         $l_clinic  =  $rec["clinic_name"];
         $l_address  =  $rec["address1"] . " " . 
                        $rec["address2"]  . " ".
                        $rec["city"] . " ". 
                        $rec["state"] . " ".
                        $rec["zipcode"] . " ";
                        
        $l_doctorName = $dx->getDoctorName($l_doctor);
      ?>
<tr onclick="callformwithparentkey(<?php echo $ctrl->Q($p_form_name); ?>,<?php echo $l_id; ?>,<?php echo $p_patient_id; ?>)">
<td > 
 <font color='blue'> <?php echo $l_time; ?></font>
</td>
<td > <?php echo $l_doctorName; ?></td>
<td > <?php echo $l_clinic; ?></td>
<td > <?php echo $l_address; ?></td>
</tr>
      <?php
}
?>
</table>
</div>
</div>
<BR/>
			<div class='login-actions'>
<button onclick="callformwithparentkey(<?php echo $ctrl->Q($p_form_name); ?>,'0',<?php echo $p_patient_id; ?>)"  class="top-create-account">New Entry</button>
			</div>