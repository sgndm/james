<?php require_once("init_setup.php") ?>
<?php
      $l_prefix = "videorecord_";
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
      $ls_obj = $dx->getvideos($x_obj);
      $videototalrows = $ls_obj->{"total_records"};
      if ( $videototalrows == 0 )
           $videototalrows=1;
?>
<table class="span10" id="videoTable" border=2>
<tr align="center" >
<th >Video</th>
<th >Active</th>
</tr>

<input id="videorecord_maxrow"  value="<?php echo $videototalrows; ?>" type="hidden" />
<input id="videorecord_patient_id" type="hidden" value="<?php echo $p_patient_id; ?>" />
<?php
   $data='{}';
   $s_obj=json_decode($data);
   $s_obj->{"isactive"}='Y';
   $ux = UserModel::get(); 
   $ss_obj = $dx->getVideo_list($s_obj);
   $s_fieldtotal = $ss_obj->{"total_records"};

      for ( $colidx =0; $colidx <$videototalrows; $colidx++)
      {
         $krec=$ls_obj->{"record"}[$colidx];
         $l_id  =  $krec["id"];
         $l_video_id  =  $krec["video_id"];
         $l_record_id  =  $krec["id"];
         $l_active =   $krec["isactive"];
		 $checked = "";
		 if($l_active == "Y")
		 	$checked = "CHECKED";
    ?>

<input id="videorecord_<?php echo $colidx; ?>_id" type="hidden" value="<?php echo $l_id; ?>" />
<tr>
<td style="width:50%" align="left" >
   <select  id="videorecord_<?php echo $colidx; ?>_video_id" >
<?php
   for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
   {
      $rr_rec=$ss_obj->{"record"}[$s_idx];
      $p_nm=$rr_rec["video"];
      $p_id=$rr_rec["id"];
      $p_sel="";
      if ( $p_id == $l_video_id ) 
         $p_sel="SELECTED";
      echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
   }
?>
  </SELECT>
</td>
<td align="center" style="width:8%" align="left"> <input <?php echo "$checked"; ?> type="checkbox"  id="videorecord_<?php echo $colidx; ?>_isactive" /> </td>
</tr>
<?php
    }
?>
</table>
