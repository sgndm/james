<?php require_once("init_setup.php") ?>
<?php
      $l_prefix = "symptomrecord_";
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
      $ls_obj = $dx->getSymptoms($x_obj);
      $symtotalrows = $ls_obj->{"total_records"};
      if ( $symtotalrows == 0 )
      $symtotalrows=1;

?>
<table id="symptomTable" border=2>
<tr align="center" >
<th class="span1">Symptom</th>
<th class="span3">Start Date</th>
<th class="span3">End Date</th>
<th class="span1">Active</th>
</tr>

<input id="symptomrecord_maxrow"  value="<?php echo $symtotalrows; ?>" type="hidden" />
<input id="symptomrecord_patient_id" type="hidden" value="<?php echo $p_patient_id; ?>" />
<?php

   $data='{}';
   $s_obj=json_decode($data);
   $s_obj->{"isactive"}='Y';
   $ux = UserModel::get(); 
   $ss_obj = $dx->getSymptomsList($s_obj);
   $s_fieldtotal = $ss_obj->{"total_records"};

      for ( $colidx =0; $colidx <$symtotalrows; $colidx++)
      {
         $krec=$ls_obj->{"record"}[$colidx];
         $l_id  =  $krec["id"];
         $l_symptom_id  =  $krec["symptom_id"];
         $l_record_id  =  $krec["id"];
         $l_start_date_fldid =   "symptomrecord_".$colidx ."_start_date";
         $l_start_date =   $krec["start_date_fmt"];
         $l_end_date =   $krec["end_date_fmt"];
         $l_end_date_fldid =   "symptomrecord_".$colidx ."_end_date";
         $l_active =   $krec["isactive"];
		 $checked = "";
		 if($l_active == "Y")
		 	$checked = "CHECKED";
    ?>

<input id="symptomrecord_<?php echo $colidx; ?>_id" type="hidden" value="<?php echo $l_id; ?>" />
<tr>
<td  align="left" >
   <select  id="symptomrecord_<?php echo $colidx; ?>_symptom_id" >
<?php
   for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
   {
      $rr_rec=$ss_obj->{"record"}[$s_idx];
      $p_nm=$rr_rec["symptom"];
      $p_id=$rr_rec["id"];
      $p_sel="";
      if ( $p_id == $l_symptom_id ) 
         $p_sel="SELECTED";
      echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
   }
?>
  </SELECT>
</td>
<td  align="left" > 
<input id="<?php  echo $l_start_date_fldid; ?>"  type="text" value="<?php echo $l_start_date; ?>" /> </td>

<td  align="left" > <input  id="<?php  echo $l_end_date_fldid; ?>"  type="text" value="<?php echo $l_end_date; ?>" /> </td>

<script>
  $(function() {
    $( "#<?php echo $l_start_date_fldid; ?>" ).datepicker();
    $( "#<?php echo $l_end_date_fldid; ?>" ).datepicker();
  });
</script>

<td align="center"  align="left"> <input <?php echo "$checked"; ?> type="checkbox"  id="symptomrecord_<?php echo $colidx; ?>_isactive" /> </td>
</tr>
<?php
    }
?>
</table>
