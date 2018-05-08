<?php require_once("init_setup.php") ?>
<?php
      $l_prefix = "vitalrecord_";
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
      $l_obj = $dx->getMyVital($x_obj);
      $totalrows = $l_obj->{"total_records"};
      if ( $totalrows == 0 )
      $totalrows=1;

   $data='{}';
   $s_obj=json_decode($data);
   $s_obj->{"isactive"}='Y';
   $ux = UserModel::get(); 
   $p_frequency = $dx->getFrequency($x_obj);
   $ftotalrows = $p_frequency->{"total_records"};
   $ss_obj = $dx->getVital_list($s_obj);
   $s_fieldtotal = $ss_obj->{"total_records"};
?>
<table id="vitalTable" border=2>
<tr align="center" >
<th >Vital</th>
<th>Frequency</th>
<th>Low Red</th>
<th>Low Yellow</th>
<th>High Yellow</th>
<th>High Red</th>
<th class="span1" >Active</th>
</tr>

<input id="vitalrecord_maxrow"  value="<?php echo $totalrows; ?>" type="hidden" />
<input id="vitalrecord_patient_id" type="hidden" value="<?php echo $p_patient_id; ?>" />
<?php
      for ( $colidx =0; $colidx <$totalrows; $colidx++)
      {
         $rec=$l_obj->{"record"}[$colidx];
         $l_id  =  $rec["id"];
         $l_vital_id  =  $rec["vital_id"];
		 $lowred  =  $rec["low_alert"];
		 $lowyellow  =  $rec["low_warning"];
		 $highyellow  =  $rec["high_warning"];
		 $highred  =  $rec["high_alert"];
		 $l_frequency_id  =  $rec["frequency_id"];
         $l_record_id  =  $rec["id"];
         $l_active =   $rec["isactive"];
		 $checked = "";
		 if($l_active == "Y")
		 	$checked = "CHECKED";

    ?>
<input id="vitalrecord_<?php echo $colidx; ?>_id" type="hidden" value="<?php echo $l_id; ?>" />
<tr>
<td style="width:10%" align="left" >
   <select  id="vitalrecord_<?php echo $colidx; ?>_vital_id" >
<?php
   for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
   {
      $rr_rec=$ss_obj->{"record"}[$s_idx];
      $p_nm=$rr_rec["vital"];
      $p_id=$rr_rec["id"];
      $p_sel="";
      if ( $p_id == $l_vital_id ) 
         $p_sel="SELECTED";
      echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
   }
?>
  </SELECT>
</td>

<td style="width:20%" >
   <select style="width:100%" id="vitalrecord_<?php echo $colidx; ?>_frequency" >
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

<td style="width:15%">
	<input type="number" step="any" class="span2" id="vitalrecord_<?php echo $colidx; ?>_lowred" value="<?php echo "$lowred"; ?>" placeholder="LR" />
</td>
<td style="width:15%">
	<input type="number" step="any" class="span2" id="vitalrecord_<?php echo $colidx; ?>_lowyellow" value="<?php echo "$lowyellow"; ?>" placeholder="LY" />
</td>
<td style="width:15%">
	<input type="number" step="any" class="span2" id="vitalrecord_<?php echo $colidx; ?>_highyellow" value="<?php echo "$highyellow"; ?>" placeholder="HY" />
</td>
<td style="width:15%">
	<input type="number" step="any" class="span2" id="vitalrecord_<?php echo $colidx; ?>_highred" value="<?php echo "$highred"; ?>" placeholder="HR" />
</td>



<td align="center" style="width:8%" align="left"> <input <?php echo "$checked"; ?> type="checkbox"  id="vitalrecord_<?php echo $colidx; ?>_isactive" /> </td>
</tr>
<?php
}
?>
</table>