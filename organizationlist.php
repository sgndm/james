<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_patient_id=0;
      $p_id = $_GET["id"];
      if ( $p_id == null || $p_id == "" )
         $p_id = "0";

      $user_id= $ctrl->getUserID();
      $data='{"user_id":"' . $user_id . '"}';

      $p_obj=json_decode($data);

      $l_obj = $dx->getOrganizations($p_obj);


      $fieldtotal = $l_obj->{"total_records"};
      $title="Organizations";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $default_view_rows=100;
      include("include/include_pageindex.php");

?>
<input type="hidden" id="patient_id" value="<?php echo $p_id; ?>" />
<div style="width:100%">
<table border=1 style="width:80%">
<tr >
<th style="width=30%" >Name</th>
<th style="width=70%" >address</th>
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
         $l_name  =  $rec["name"];
         $l_address =   $rec["address1"] . "," .
                        $rec["address2"] . "," .
                        $rec["city"] . "," .
                        $rec["state"] . "," .
                        $rec["zipcode"];
      ?>
<tr bgcolor="white"
    onclick="callform('organization.php',<?php echo $l_record_id; ?>,'patient')" >
<td style="width:30%" >
 <a href="#" class="top-create-account"><font color='blue'> <?php echo $l_name; ?></font> </a> 
</td>
<td style="width=30%" > <?php echo $l_address; ?></td>
</tr>
      <?php
}
?>
</table>
</div>
<BR/>
			<div class='login-actions'>
<input type="button" onclick="callform('organization.php',0,'patient')" value="New Entry" />
			</div>
<?php include("foot.php") ?>
