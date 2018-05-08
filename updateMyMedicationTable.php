<?php require_once("init_setup.php") ?>
<?php
      $l_prefix = "medicationrecord_";
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
	  
	  $show = $_POST["show"];
	  
      $p_old_patient=$_SESSION["old_patient"];

      $p_patient_id = $_SESSION["view_patient_id"];
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
	  if($show == "active")
	  {
	  	$x_obj->{"isactive"}='Y';
	  }
      $p_frequency = $dx->getFrequency($x_obj);
      $ftotalrows = $p_frequency->{"total_records"};
      $med_obj = $dx->getMedications($x_obj);
      $medtotalrows = $med_obj->{"total_records"};
      if ( $medtotalrows == 0 )
      $medtotalrows=1;

   $data='{}';
   $s_obj=json_decode($data);
   $s_obj->{"isactive"}='Y';
   $ux = UserModel::get(); 
   $ss_obj = $dx->getMedicationList($s_obj);
   $s_fieldtotal = $ss_obj->{"total_records"};

?>
<table id="medicationTable" border=2>
<thead>
<tr align="center" >
<th class="span1">Medication</th>
<th class="span1">Frequency</th>
<th class="span3">Start Date</th>
<th class="span3">End Date</th>
<th >Active</th>
</tr>
</thead>
<tbody>

<input id="medicationrecord_maxrow"  value="<?php echo $medtotalrows; ?>" type="hidden" />
<input id="medicationrecord_patient_id" type="hidden" value="<?php echo $p_patient_id; ?>" />
<?php
      for ( $colidx =0; $colidx <$medtotalrows; $colidx++)
      {
         $rec=$med_obj->{"record"}[$colidx];
         $l_id  =  $rec["id"];
         $l_medication_id  =  $rec["medication_id"];
         $l_record_id  =  $rec["id"];
         $l_start_date_fldid =   "medicationrecord_".$colidx ."_start_date";
         $l_start_date =   $rec["start_date_fmt"];
         $l_end_date =   $rec["end_date_fmt"];
         $l_end_date_fldid =   "medicationrecord_".$colidx ."_end_date";
         $l_active =   $rec["isactive"];
		 $checked = "";
		 if($l_active == "Y")
		 	$checked = "CHECKED";
         $l_frequency_id = $rec["frequency"];

    ?>
<input id="medicationrecord_<?php echo $colidx; ?>_id" type="hidden" value="<?php echo $l_id; ?>" />
<tr>
<td class="span1" align="left" >
   <select  id="medicationrecord_<?php echo $colidx; ?>_medication_id" >
<?php
   $l_st_category="";
   for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
   {
      $rr_rec=$ss_obj->{"record"}[$s_idx];
      $p_nm=$rr_rec["medication"];
      $p_category=$rr_rec["category"];
      $p_id=$rr_rec["id"];
      $p_sel="";
      if (  $l_st_category != $p_category ) 
         echo "<OPTION DISABLED value=''><font color='#B50128'> $p_category</font></OPTION>";
      $l_st_category = $p_category;
      if ( $p_id == $l_medication_id ) 
         $p_sel="SELECTED";
      echo "<OPTION $p_sel value='". $p_id . "'>&nbsp;&nbsp;&nbsp;&nbsp;$p_nm</OPTION>";
   }
?>
  </SELECT>


</td>



<td class="span1" align="left" >
   <select  id="medicationrecord_<?php echo $colidx; ?>_medication_frequency" >
<?php
   for ( $i =0; $i <$ftotalrows; $i++)
   {
      $rr_rec=$p_frequency->{"record"}[$i];
      $p_nm=$rr_rec["name"];
      $p_id=$rr_rec["id"];
      $p_sel="";
      if ( $p_id == $l_frequency_id ) 
         $p_sel="SELECTED";
      echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
   }
?>
  </SELECT>
</td>



<td  align="left" > 
<input id="<?php  echo $l_start_date_fldid; ?>" style="font-size: 90%" type="text" value="<?php echo $l_start_date; ?>" /> </td>

<td  align="left" > <input  id="<?php  echo $l_end_date_fldid; ?>" style="font-size: 90%" type="text" value="<?php echo $l_end_date; ?>" /> </td>

<script>
  $(function() {
    $( "#<?php echo $l_start_date_fldid; ?>" ).datepicker();
    $( "#<?php echo $l_end_date_fldid; ?>" ).datepicker();
  });
</script>
<td align="center" class="span1" align="left"> <input <?php echo "$checked"; ?> type="checkbox"  id="medicationrecord_<?php echo $colidx; ?>_isactive" /> </td>
</tr>
<?php
}
?>
</tbody>
</table>