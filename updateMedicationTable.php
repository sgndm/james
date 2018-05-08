<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
	  
	  $show = $_POST["show"];
	  
      $p_patient_id=0;
      $user_id= $ctrl->getUserID();
      $data='{"user_id":"' . $user_id . '"}';

      $p_obj=json_decode($data);
	  if($show == "active")
	  {
	  	$p_obj->{"isactive"}='Y';
	  }
      $l_obj = $dx->getMedicationList($p_obj);
	  
      $fieldtotal = $l_obj->{"total_records"};
      $title="Medication";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $default_view_rows=100;
      include("include/include_pageindex.php");

?>
<table border=1 style="width:80%">
<tr >
<th style="width=15%" >Category</th>
<th style="width=15%" >Medication</th>
<th style="width=25%" >URL</th>
<th style="width=45%" >Description</th>

</tr>
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
       $c_no=$idx + 1;
       if ( $c_no  < $start_rowid || $c_no > $end_rowid )
       {
          continue;
       }
         $rec=$l_obj->{"record"}[$idx];
         $l_record_id  =  $rec["id"];
         $l_category  =  $rec["category"];
         $l_medication  =  $rec["medication"];
         $l_description  =  $rec["description"];
         $l_url  =  $rec["url"];
      ?>
<tr bgcolor="white"
    onclick="callform('medication.php',<?php echo $l_record_id; ?>,'patient')">
<td style="width:15%" > <?php echo $l_category; ?> </td>
<td style="width:15%" > <?php echo $l_medication; ?> </td>
<td style="width=25%" > <?php echo $l_url; ?></td>
<td style="width=45%" > <?php echo $l_description; ?></td>

</tr>
      <?php
}
?>
</table>