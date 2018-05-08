<?php require_once("init_setup.php") ?>
<?php
      $l_prefix = "phyactrecord_";
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
      $l_obj = $dx->getPhysicalActivity($x_obj);
      $totalrows = $l_obj->{"total_records"};
      if ( $totalrows == 0 )
      $totalrows=1;

   $data='{}';
   $s_obj=json_decode($data);
   $s_obj->{"isactive"}='Y';
   $ux = UserModel::get(); 
   $ss_obj = $dx->getPhyactList($s_obj);
   $s_fieldtotal = $ss_obj->{"total_records"};

?>
<table id="phyactTable" border=2>
<tr align="center" >
<th >Physical Activity</th>
<th class="span1" >Active</th>
</tr>

<input id="phyactrecord_maxrow"  value="<?php echo $totalrows; ?>" type="hidden" />
<input id="phyactrecord_patient_id" type="hidden" value="<?php echo $p_patient_id; ?>" />
<?php
      for ( $colidx =0; $colidx <$totalrows; $colidx++)
      {
         $rec=$l_obj->{"record"}[$colidx];
         $l_id  =  $rec["id"];
         $l_physicalactivity_id  =  $rec["physical_activity_id"];
         $l_record_id  =  $rec["id"];
         $l_active =   $rec["isactive"];
		 $checked = "";
		 if($l_active == "Y")
		 	$checked = "CHECKED";

    ?>
<input id="phyactrecord_<?php echo $colidx; ?>_id" type="hidden" value="<?php echo $l_id; ?>" />
<tr>
<td style="width:80%" align="left" >
   <select  id="phyactrecord_<?php echo $colidx; ?>_physicalactivity_id" >
<?php
   for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
   {
      $rr_rec=$ss_obj->{"record"}[$s_idx];
      $p_nm=$rr_rec["physical_activity"];
      $p_id=$rr_rec["id"];
      $p_sel="";
      if ( $p_id == $l_physicalactivity_id ) 
         $p_sel="SELECTED";
      echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
   }
?>
  </SELECT>

</td>

<td align="center" style="width:8%" align="left"> <input <?php echo "$checked"; ?> type="checkbox"  id="phyactrecord_<?php echo $colidx; ?>_isactive" /> </td>
</tr>
<?php
}
?>
</table>