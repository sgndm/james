<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 

      $p_form_name="user_video.php";

      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);

      $l_obj = $dx->getVideos($x_obj);
      $fieldtotal = $l_obj->{"total_records"};

?>
<h4 class='login-box-head'>Videos </h4>
<div class="row">
<div class='span10'>
<table border=2>
<tr>
<th class="span6" >Video</th>
<th class="span4" >Link</th>
</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
         $rec=$l_obj->{"record"}[$idx];
         $l_video  =  $rec["video"];
         $l_id  =  $rec["id"];
         $l_url =   $rec["url"];
      ?>
<tr onclick="callformwithparentkey(<?php echo $ctrl->Q($p_form_name); ?>,<?php echo $l_id; ?>,<?php echo $p_patient_id; ?>)">
<td style="width=50%" > 
 <a href="#"  class="top-create-account"><font color='blue'> <?php echo $l_video; ?></font> </a> </td>
<td style="width=50%" > 
<a href="<?php echo $l_url; ?>"><?php echo $l_url; ?></a></tr>
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
