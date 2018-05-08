<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 

      $p_form_name="user_symptom.php";

      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);

      $l_obj = $dx->getSymptoms($x_obj);
      $fieldtotal = $l_obj->{"total_records"};

?>
<h4 class='login-box-head'>Symptoms</h4>
<div class="row">
<div class='span10'>
<table border=2>
<tr align="center">
<th class="span6" >Symptoms</th>
<th class="span4" >URL</th>
<th class="span1" >Start Date</th>
<th class="span1" >End Date</th>
</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
         $rec=$l_obj->{"record"}[$idx];
         $l_symptoms  =  $rec["symptom"];
         $l_url  =  $rec["url"];
         $l_id  =  $rec["id"];
         $l_start_date =   $rec["start_date_fmt"];
         $l_end_date =   $rec["end_date_fmt"];
      ?>
<tr onclick="callformwithparentkey(<?php echo $ctrl->Q($p_form_name); ?>,<?php echo $l_id; ?>,<?php echo $p_patient_id; ?>)">
<td><font color='blue'> <?php echo $l_symptoms; ?></font></td>
<td> <?php echo $l_url; ?></font></td>
<td align="center"> <?php echo $l_start_date; ?></font></td>
<td align="center"> <?php echo $l_end_date; ?></font></td>
</tr>
      <?php
}
?>
</table>
<BR/>
			<div class='login-actions'>
<button onclick="callformwithparentkey(<?php echo $ctrl->Q($p_form_name); ?>,'0',<?php echo $p_patient_id; ?>)"  class="top-create-account">New Entry</button>
<input type="text" placeholder="Push Message"  id="record_pushmessage" />
<button onclick="callme('record,pushsymptom,null')"  class="top-create-account">Send Push Notification</button>
			</div>

<BR/>
<h4>User Response</h4>
<?php
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $l_obj = $dx->getSymptomsUserResponse($x_obj);
      $fieldtotal = $l_obj->{"total_records"};
?>
<table border=2>
<tr align="center">
<th class="span6" >Symptoms</th>
<th class="span2" >Date</th>
<th class="span2" >Response</th>
</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
         $rec=$l_obj->{"record"}[$idx];
         $l_symptoms  =  $rec["symptom"];
         $l_date  =  $rec["created_ts"];
         $l_response  =  $rec["response"];
      ?>
<tr>
<td align="left"><a href="#"  class="top-create-account"><font color='blue'> <?php echo $l_symptoms; ?></font> </a> </td>
<td align="center"><a href="#"  class="top-create-account"><font color='blue'> <?php echo $l_date; ?></font> </a> </td>
<td align="center"><a href="#"  class="top-create-account"><font color='blue'> <?php echo $l_response; ?></font> </a> </td>
</tr>
      <?php
}
?>
</table>
</div>
</div>
