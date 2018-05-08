<?php require_once("init_setup.php") ?>
<?php
      $l_prefix = "taskrecord_";
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
      $task_obj = $dx->getTasks($x_obj);
      $medtotalrows = $task_obj->{"total_records"};
      if ( $medtotalrows == 0 )
      $medtotalrows=1;

   $data='{}';
   $s_obj=json_decode($data);
   $s_obj->{"isactive"}='Y';
   $ux = UserModel::get(); 
   

?>
<table id="taskTable" border=2>
<thead>
<tr align="center" >
<th class="span3">Task</th>
<th class="span1">Frequency</th>
<th class="span3">Start Date</th>
<th class="span3">End Date</th>
<th >Active</th>
</tr>
</thead>
<tbody>

<input id="taskrecord_maxrow"  value="<?php echo $medtotalrows; ?>" type="hidden" />
<input id="taskrecord_patient_id" type="hidden" value="<?php echo $p_patient_id; ?>" />
<?php
      for ( $colidx =0; $colidx <$medtotalrows; $colidx++)
      {
         $rec=$task_obj->{"record"}[$colidx];
         $l_id  =  $rec["id"];
         $l_task  =  $rec["task"];
		 $l_start_date_fldid =   "taskrecord_".$colidx ."_start_date";
         $l_start_date =   $rec["start_date_fmt"];
         $l_end_date =   $rec["end_date_fmt"];
         $l_end_date_fldid =   "taskrecord_".$colidx ."_end_date";
         $l_active =   $rec["isactive"];
		 $checked = "";
		 if($l_active == "Y")
		 	$checked = "CHECKED";
         $l_frequency_id = $rec["frequency_id"];

    ?>
<input id="taskrecord_<?php echo $colidx; ?>_id" type="hidden" value="<?php echo $l_id; ?>" />
<tr>
<td class="span5" align="left" >
   <input type="text" class="" id="taskrecord_<?php echo $colidx; ?>_name" 
   value="<?php echo $l_task; ?>" />


</td>



<td class="span1" align="left" >
   <select  id="taskrecord_<?php echo $colidx; ?>_task_frequency" >
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

<td align="center" class="span1" align="left"> <input <?php echo "$checked"; ?> type="checkbox"  id="taskrecord_<?php echo $colidx; ?>_isactive" /> </td>
</tr>
<?php
}
?>
</tbody>
</table>