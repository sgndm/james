<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $ux = UserModel::get(); 
	  
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
      $user_type= $ctrl->getUserType();
      
      if($user_type == "1")//Admin
      {
          $l_obj = $dx->getClinics($p_obj);
      }
      else if($user_type == "2") // Clinic
      {
          $idString = "SELECT * FROM user WHERE id = $user_id";
		  if($show == "active")
		  	$idString .= " and isactive = 'Y'";
          $l_obj = $ux->getRecords($p_obj, $idString);
      }
      else if($user_type == "3") // Doctor
      {
          $sql= "SELECT clinic_id FROM clinic_doctor WHERE doctor_id = $user_id AND isConnected = 'Y'";
          $clinicIds = $ux->getRecordIds($sql);
          $idString = "SELECT * FROM user WHERE user_type = 2 AND (id = 0 ";
          foreach ($clinicIds as $key => $value) 
          {
              $idString .= "OR id = $value[0] ";
          }
          $idString .= ")";
		  if($show == "active")
		  	$idString .= " and isactive = 'Y'";
          $l_obj = $ux->getRecords($p_obj, $idString);
      }
      else // Care Coordinator
      {
          $sqlClinicId = "SELECT clinic_id FROM user WHERE id = $user_id";
          $clinicId = $ctrl->getRecordID($sqlClinicId);
          $sql= "SELECT * FROM user WHERE clinic_id = $clinicId";
		  if($show == "active")
		  	$sql .= " and isactive = 'Y'";
          $l_obj = $ux->getRecords($p_obj, $sql);
      }

      $fieldtotal = $l_obj->{"total_records"};

      $title="Clinics";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $default_view_rows=100;
      include("include/include_pageindex.php");

?>

<table border=1 style="width:80%">
<tr >
<th style="width:10%" ></th>
<th style="width:30%" >Name</th>
<th style="width:10%" >Phone</th>
<th style="width:60%" >Address</th>
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
         $l_name  =  $rec["first_name"];
         $l_phone  =  $rec["phone"];
         $l_address =   $rec["address1"] . "," .
                        $rec["address2"] . "," .
                        $rec["city"] . "," .
                        $rec["state"] . "," .
                        $rec["zipcode"];
         $l_url  =  $rec["photourl"];
      ?>
<tr bgcolor="white"
    onclick="callform('clinic.php',<?php echo $l_record_id; ?>,'patient')"  >
<td>
<img style="width:50px; height:50px" src="<? echo $l_url ; ?>"   />
</td>
<td >
 <a href="#" class="top-create-account"><font color='blue'> <?php echo $l_name; ?></font> </a> 
</td>
<td > <?php echo $l_phone; ?></td>
<td > <?php echo $l_address; ?></td>
</tr>
      <?php
}
?>
</table>