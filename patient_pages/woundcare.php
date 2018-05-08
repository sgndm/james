<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $p_form_name="user_woundcare.php";

      $l_obj = $dx->getWoundCare($x_obj);
      $fieldtotal = $l_obj->{"total_records"};
      $fieldtotal = $l_obj->{"total_records"};

?>
    <h4>Wound Care</h4>

<div class="row">
<div class='span10'>
<table border=2>
<tr>
<th class="span5" >Description</th>
</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
         $rec=$l_obj->{"record"}[$idx];
         $l_description  =  $rec["description"];
         $l_record_id  =  $rec["id"];
      ?>

<tr onclick="callformwithparentkey(<?php echo $ctrl->Q($p_form_name); ?>,<?php echo $l_record_id; ?>,<?php echo $p_patient_id; ?>)">
<td><a href="#" class="top-create-account"><font color='blue'> <?php echo $l_description; ?></font> </a> </td>
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
