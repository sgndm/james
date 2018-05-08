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
          $l_obj = $dx->getDoctors($p_obj);
      }
      else if($user_type == "2") // Clinic
      {
          $sql= "SELECT doctor_id FROM clinic_doctor WHERE clinic_id = $user_id AND isConnected = 'Y'";
          $doc_ids = $ux->getRecordIds($sql);
          $idString = "SELECT * FROM user WHERE user_type = 3 AND (id = 0 ";
          foreach ($doc_ids as $key => $value) 
          {
              $idString .= "OR id = $value[0] ";
          }
          $idString .= ")";
		  if($show == "active")
		  	$idString .= " and isactive = 'Y'";
          $l_obj = $ux->getRecords($p_obj, $idString);
      }
      else if($user_type == "3") // Doctor
      {
          
          $sqlClinicIds= "SELECT clinic_id FROM clinic_doctor WHERE doctor_id = $user_id AND isConnected = 'Y'";
          $clinicIds = $ux->getRecordIds($sqlClinicIds);
          $sqlDocIds = "SELECT doctor_id FROM clinic_doctor WHERE isConnected = 'Y' AND (clinic_id = 0 ";
          foreach ($clinicIds as $key => $value) 
          {
              $sqlDocIds .= "OR clinic_id = $value[0] ";
          }
          $sqlDocIds .= ")";
          $doc_ids = $ux->getRecordIds($sqlDocIds);
          $idString = "SELECT * FROM user WHERE user_type = 3 AND (id = 0 ";
          foreach ($doc_ids as $key => $value) 
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
          $sql= "SELECT doctor_id FROM clinic_doctor WHERE clinic_id = $user_id AND isConnected = 'Y'";
          $l_obj = $ux->getRecords($p_obj, $sql);
      }
      


      $fieldtotal = $l_obj->{"total_records"};
      $title="Doctors";
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
<th style="width=10%" ></th>
<th style="width=40%" >Name</th>
<th style="width=40%" >Designation</th>
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
         $l_name  =  $rec["first_name"];
         $l_url  =  $rec["photourl"];
         $l_designation  =  $rec["designation"];
         $l_phone  =  $rec["phone"];
      ?>
<tr bgcolor="white"
    onclick="callform('doctor.php',<?php echo $l_record_id; ?>,'patient')">
<td>
<img style="width:50px; height:50px" src="<? echo $l_url ; ?>"   />
</td>
<td >
 <a href="#" class="top-create-account"><font color='blue'> <?php echo $l_name; ?></font> </a> 
</td>
<td > <?php echo $l_designation; ?></td>
<td > <?php echo $l_phone; ?></td>
</tr>
      <?php
}
?>
</table>