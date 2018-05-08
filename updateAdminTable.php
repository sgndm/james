<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get();
	  
	  $show = $_POST["show"];
	   
      $p_patient_id=0;
      $p_id = $_GET["id"];
      if ( $p_id == null || $p_id == "" )
         $p_id = "0";

      $user_id= $ctrl->getUserID();
      $data='{"user_id":"' . $user_id . '"}';

      $p_obj=json_decode($data);
	  if($show == "active")
	  {
	  	$p_obj->{"isactive"}='Y';
	  }

      $l_obj = $dx->getAdmins($p_obj);
      $fieldtotal = $l_obj->{"total_records"};
?>
<?php
      $title="Admin User";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $default_view_rows=100;
      include("include/include_pageindex.php");
?>
<table border=1 style="width:80%">
<tr >
<th style="width=20%" >Name</th>
<th style="width=20%" >Email</th>
<th style="width=20%" >Phone</th>
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
         $l_first_name  =  $rec["first_name"];
         $l_last_name  =  $rec["last_name"];
         $l_email  =  $rec["email"];
         $l_phone  =  $rec["phone"];
      ?>
<tr bgcolor="white"
    onclick="callform('user.php',<?php echo $l_record_id; ?>,'patient')">
<td > <?php echo $l_first_name; ?></td>
<td > <?php echo $l_email; ?></td>
<td > <?php echo $l_phone; ?></td>
</tr>
      <?php
}
?>
</table>