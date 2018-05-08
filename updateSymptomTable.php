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
      $l_obj = $dx->getSymptom_list($p_obj);


      $fieldtotal = $l_obj->{"total_records"};
      $title="Symptoms";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $default_view_rows=100;
      include("include/include_pageindex.php");

?>
<div style="width:100%">
<table border=1 style="width:80%">
<tr >
<th style="width=30%" >Symptom</th>
<th style="width=30%" >URL</th>
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
         $l_symptom  =  $rec["symptom"];
         $l_url  =  $rec["url"];
      ?>
<tr bgcolor="white"
    onclick="callform('symptom.php',<?php echo $l_record_id; ?>,'patient')">
<td style="width:30%" >
 <a href="#" class="top-create-account"><font color='blue'> <?php echo $l_symptom; ?></font> </a> 
</td>
<td style="width=30%" > <?php echo $l_url; ?></td>
</tr>
      <?php
}
?>
</table>