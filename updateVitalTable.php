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
      $l_obj = $dx->getVital_list($p_obj);


      $fieldtotal = $l_obj->{"total_records"};
      $title="Vitals";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $default_view_rows=100;
      include("include/include_pageindex.php");

?>
<div style="width:100%">
<table border=1 style="width:80%">
<tr >
<th style="width=30%" >Vital</th>
<th style="width=15%" >Graph Min</th>
<th style="width=15%" >Graph Max</th>
<th style="width=20%" >Graph Type</th>
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
         $l_vital  =  $rec["vital"];
		 $l_graph_type  =  $rec["graph_type"];
		 $l_graph_min  =  $rec["graph_min"];
		 $l_graph_max  =  $rec["graph_max"];
      ?>
<tr bgcolor="white"
    onclick="callform('vital.php',<?php echo $l_record_id; ?>,'patient')">
<td style="width:30%" >
 <a href="#" class="top-create-account"><font color='blue'> <?php echo $l_vital; ?></font> </a> 
</td>
<td style="width:15%" >
  <?php echo $l_graph_min; ?>
</td>
<td style="width:15%" >
  <?php echo $l_graph_max; ?>
</td>
<td style="width:20%" >
  <?php echo $l_graph_type; ?>
</td>
</tr>
      <?php
}
?>
</table>