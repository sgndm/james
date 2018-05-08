<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
	  $ux = UserModel::get(); 
	  
	  $show = $_POST["show"];
	  
      $p_patient_id=0;
      $user_id= $ctrl->getUserID();
      $user_type= $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';

      $p_obj=json_decode($data);
	  if($show == "active")
	  {
	  	$p_obj->{"isactive"}='Y';
	  }

      $l_obj = $dx->getDiagnosisList($p_obj);

      $fieldtotal = $l_obj->{"total_records"};
      $title="Diagnosis";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $default_view_rows=100;
      include("include/include_pageindex.php");
	  
	  

?>
<table border=1 style="width:80%">
<tr >
<th style="width=20%" >Diagnosis</th>
<?php if($ux->doIHavePermission($user_type, "defaults")){ ?>
<th style="width=10%" >Default</th>
<?php } ?>
<th style="width=20%" >URL</th>
<th style="width=50%" >Description</th>

</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
       $c_no=$idx + 1;
       if ( $c_no  < $start_rowid || $c_no > $end_rowid )
       {
          continue;
       }
         $rec=$p_obj->{"record"}[$idx];
         $l_record_id  =  $rec["id"];
         $l_diagnosis  =  $rec["diagnosis"];
         $l_url  =  $rec["url"];
         $l_description  =  $rec["description"];
         $l_description = htmlentities($l_description);
         $small = substr($l_description, 0, 300);
         $l_description_small = $small . "...";
      ?>
<tr bgcolor="white" >
<td style="width:20%" onclick="callform('diagnosis.php',<?php echo $l_record_id; ?>,'patient')"> <?php echo $l_diagnosis; ?> </td>
<?php if($ux->doIHavePermission($user_type, "defaults")){ ?>
		<td style="width:10%; text-align: center;" onclick="callform('default.php',<?php echo $l_record_id; ?>,'patient')" > <a>Edit</a> </td>
<?php } ?>
<td style="width:20%" onclick="callform('diagnosis.php',<?php echo $l_record_id; ?>,'patient')"> <?php echo $l_url; ?> </td>
<td style="width=50%" onclick="callform('diagnosis.php',<?php echo $l_record_id; ?>,'patient')"> <?php echo $l_description_small; ?></td>

</tr>
      <?php
}
?>
</table>