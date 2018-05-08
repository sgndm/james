<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 

      $p_form_name="user_phyact.php";

      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);

      $l_obj = $dx->getPhysicalActivity($x_obj);
      $fieldtotal = $l_obj->{"total_records"};
?>
<h4 class='login-box-head'>Physical Activity</h4>
<div class="row">
<div class='span10'>
<table border=2>
<tr>
<th class="span10" >Physical Activity</th>
</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
         $rec=$l_obj->{"record"}[$idx];
         $l_physicalactivity  =  $rec["physicalactivity"];
         $l_id  =  $rec["id"];
      ?>
<tr onclick="callformwithparentkey(<?php echo $ctrl->Q($p_form_name); ?>,<?php echo $l_id; ?>,<?php echo $p_patient_id; ?>)">
<td><a href="#"  class="top-create-account"><font color='blue'> <?php echo $l_physicalactivity; ?></font> </a> </td>
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
