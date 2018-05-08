<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $dx = DataModel::get(); 
      $user_id= $ctrl->getUserID();
      $user_type= $ctrl->getUserType();
      $p_record_id = $_POST["record_id"];
      if ( $p_record_id == null || $p_record_id == "" )
         $p_record_id = "0";
      $p_patient_id=0;
      $p_id = 0;
      $clinicID = 0;
      if ( $p_id == null || $p_id == "" )
         $p_id = "0";
      $p_obj=json_decode($data);
      $l_obj = $ux->getTableRecord($p_obj,"diagnosis_list","id",$p_record_id);
      
      $sqlDiagnosisID = "SELECT diagnosis FROM diagnosis_list WHERE id = $p_record_id";
      $diagnosisresult = $dx->getRecord($sqlDiagnosisID);
      $diagnosisName = $diagnosisresult[0];
      $diagnosisID =$p_record_id;
      $addClinicSelect = false;
      //Clinic Stuff
      if($user_type == "2") //clinic
      {
          $clinicID = $user_id;
      }
      else 
      {
          $addClinicSelect = true;
      }
      
      //Get group id
      $sql = "SELECT id FROM default_patient_discharge_header 
              WHERE diagnosis_id = '$diagnosisID' 
              AND clinic_id = '$clinicID'";
      $result = $dx->getRecord($sql);
      $groupID = $result[0];
	  if($groupID == "" || $groupID == " ")
	  {
	  	$groupID = 0;
	  }

      $vitalsSql = "SELECT id, vital FROM vitals_list";
      $allVitals = $dx->getOnlyRecords($vitalsSql);
      $symptomsSql = "SELECT id, symptom FROM symptoms_list WHERE id != 1 AND id != 2 AND isactive = 'Y'";
      $allSymptoms = $dx->getOnlyRecords($symptomsSql);
      $videosSql = "SELECT id, video FROM videos_list WHERE isactive = 'Y'";
      $allVideos = $dx->getOnlyRecords($videosSql);
      $woundSql = "SELECT id, description FROM wound_care_list WHERE isactive = 'Y'";
      $allWound = $dx->getOnlyRecords($woundSql);
      $paSql = "SELECT id, physical_activity FROM physical_activity_list WHERE isactive = 'Y'";
      $allPa = $dx->getOnlyRecords($paSql);
	  
      $x_obj=0;
      $allFre = $dx->getFrequency($x_obj);
	  $medicationSql = "SELECT * from medication_list WHERE isactive = 'Y' order by category, medication";
      $allMeds = $dx->getOnlyRecords($medicationSql);

?>
<script type="text/javascript">
function callchange()
{
	var currentclinicid = $('#record_clinic_id_admin').val();
	var currentdiagnosisid = $('#record_diagnosis_id').val();
	$.post( 
    	'updateDefault.php', 
    	{ clinic: currentclinicid, diagnosis: currentdiagnosisid }, 
    	function( data )
    	{  
        	$('#inputToChange').html(data);
    	});
    	
	/*$("input:checkbox").each(function() {
    	var checkboxID = this.id;
    	var idArray = checkboxID.split("_");
    	var tableType = idArray[1];
    	var valueID = idArray[2];
	});*/
}
</script>
<h4 class='login-box-head'>Default For Discharge Diagnosis </BR> <?php echo "$diagnosisName"; ?></h4>
<input type="hidden" id="patient_id" value="<?php echo $p_id; ?>" />
<input type="hidden" id="record_diagnosis_id" value="<?php echo $diagnosisID; ?>" />
<input type="hidden" id="record_clinic_id" value="<?php echo $clinicID; ?>" />
<input type="hidden" id="record_id" value="0" />
<input type="hidden" id="record_uniqueid" value="<?php echo $l_unique_id; ?>" />
<input type="hidden" id="record_formname" value="patient" />
<input type="hidden" id="record_copied_file" value="" />
<input type="hidden" id="record_filecreated" value="0" />
<input type="hidden" id="record_file_type" value="profile" />
 
<?php if($addClinicSelect) { ?>
    <div class="row">
  <div class='span4'>
  <h5>Clinic</h5> <select onChange="callchange()"  class="span4" id="record_clinic_id_admin">
<?php
     $ct = ConstantModel::get(); 
     $data='{}';
     $c_obj=json_decode($data);
     $dx = DataModel::get();
     if($user_type == "1")
     {
         $sql = "SELECT * FROM user WHERE user_type = 2";
         $ss_obj = $dx->getRecords($c_obj, $sql);
     }
     $s_fieldtotal = $ss_obj->{"total_records"};
	 echo "<OPTION value='0'>---SELECT CLINIC---</OPTION>";
     for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
     {
        $rr_rec=$ss_obj->{"record"}[$s_idx];
        $p_nm=$rr_rec["first_name"];
        $p_id=$rr_rec["id"];
        echo "<OPTION value='". $p_id . "'>$p_nm</OPTION>";
     }
     
?>
  </select>
  </div>
  </div>
<?php } ?>
  
  
  
 <div id="inputToChange"> 
 	<?php if($addClinicSelect) { }
 	else { ?>
  <div class="row">
    <div class="span4"> <h5>Vitals</h5></div>
  </div>
  <div class="row">
<div class='span12'>
    <table>
    	<thead>
    		<th></th>
    		<th>Name</th>
    		<th>Low Red</th>
    		<th>Low Yellow</th>
    		<th>High Yellow</th>
    		<th>High Red</th>
    		<th>Frequency</th>
    	</thead>
        <tbody>
<?php
    $totalvitals = $allVitals->{"total_records"};
	$totalfre = $allFre->{"total_records"};
      for ( $i =0; $i <$totalvitals; $i++)
      {
            echo "<tr><td>";
            $rec=$allVitals->{"record"}[$i];
            $vitalID = $rec["id"];
            $vitalName = $rec["vital"];
            $connected = "N";
            $sql = "SELECT id FROM default_patient_discharge_vitals WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    vitals_id = $vitalID";
            $result = $dx->getRecord($sql);
            $resultID = $result[0];
            $lowred = "";
			$lowyellow = "";
			$highyellow = "";
			$highred = "";
            if($resultID != "" && $resultID != "E")
            {
                $connected = "Y";
				$sql = "SELECT low_warning, low_alert, high_alert, high_warning FROM default_patient_discharge_vitals WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    vitals_id = $vitalID";
            	$resultset = $dx->getOnlyRecords($sql);
				$totalset = $resultset->{"total_records"};
					$record=$resultset->{"record"}[0];
					$fre = $record["frequency_id"];
					$lowred = $record["low_alert"];
					$lowyellow = $record["low_warning"];
					$highyellow = $record["high_warning"];
					$highred = $record["high_alert"];
            }
?>
        <td class="span3">
        <input type="checkbox" class="span1" id="record_vital_<?php echo $vitalID; ?>" name="record_vital_<?php echo $vitalID; ?>" value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        <?php echo "$vitalName </td> <td>"; ?>
        <input type="number" step="any" class="span2" id="record_vital_lowred_<?php echo $vitalID; ?>" name="record_vital_lowred_<?php echo $vitalID; ?>" value="<?php echo "$lowred"; ?>" placeholder="Low Red" />
        <?php echo "</td> <td>"; ?>
        <input type="number" step="any" class="span2" id="record_vital_lowyellow_<?php echo $vitalID; ?>" name="record_vital_lowyellow_<?php echo $vitalID; ?>" value="<?php echo "$lowyellow"; ?>" placeholder="Low Yellow" />
        <?php echo "</td> <td>"; ?>
        <input type="number" step="any" class="span2" id="record_vital_highyellow_<?php echo $vitalID; ?>" name="record_vital_highyellow_<?php echo $vitalID; ?>" value="<?php echo "$highyellow"; ?>" placeholder="High Yellow" />
        <?php echo "</td> <td>"; ?>
        <input type="number" step="any" class="span2" id="record_vital_highred_<?php echo $vitalID; ?>" name="record_vital_highred_<?php echo $vitalID; ?>" value="<?php echo "$highred"; ?>" placeholder="High Red" />
        <?php echo "</td></td>"; ?>
        <SELECT  id="record_vital_frequency_<?php echo $vitalID; ?>" >
		<?php
   			for ( $i2 =0; $i2 <$totalfre; $i2++)
   			{
      			$rr_rec=$allFre->{"record"}[$i2];
				$freID=$rr_rec["id"];
      			$freName=$rr_rec["name"];
      			$connected="";
				$sql = "SELECT id FROM default_patient_discharge_vitals WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    frequency_id = $freID AND vitals_id = $vitalID";
            	$result = $dx->getRecord($sql);
            	$resultID = $result[0];
            	if($resultID != "" && $resultID != "E")
            	{
                	$connected = "SELECTED";
            	}
      			echo "<OPTION $connected value='". $freID . "'>$freName</OPTION>";
				
   			}
		?>
  		</SELECT></td></tr>

<?php 
   }
?>
</tbody>
</table>
</div>
</div>


<div class="row">
    <div class="span4"> <h5>Medications</h5></div>
  </div>
  <div class="row">
<div class='span10'>
    <table>
    	<thead>
		<tr >
			<th >Medication</th>
			<th >Frequency</th>
			<th >Medication</th>
			<th >Frequency</th>
		</tr>
		</thead>
        <tbody>
        <tr>
<?php
    $totalmeds = $allMeds->{"total_records"};
	$totalfre = $allFre->{"total_records"};
      for ( $i =0; $i <$totalmeds; $i++)
      {
            if ($i%2==0) 
            {
              echo "</tr><tr>";
            }
            $rec=$allMeds->{"record"}[$i];
            $medID = $rec["id"];
            $medName = $rec["medication"];
            $connected = "N";
            $sql = "SELECT id FROM default_patient_discharge_medications WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    medication_id = $medID";
            $result = $dx->getRecord($sql);
            $resultID = $result[0];
            if($resultID != "" && $resultID != "E")
            {
                $connected = "Y";
            }
?>
        <td class="span5">
        <input type="checkbox" class="span1" id="record_medication_<?php echo $medID; ?>" name="record_medication_<?php echo $medID; ?>" value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        <?php echo "$medName </td><td>"; ?>
        <SELECT  id="record_frequency_<?php echo $medID; ?>" >
		<?php
   			for ( $i2 =0; $i2 <$totalfre; $i2++)
   			{
      			$rr_rec=$allFre->{"record"}[$i2];
				$freID=$rr_rec["id"];
      			$freName=$rr_rec["name"];
      			$connected="";
				$sql = "SELECT id FROM default_patient_discharge_medications WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    frequency_id = $freID AND medication_id = $medID";
            	$result = $dx->getRecord($sql);
            	$resultID = $result[0];
            	if($resultID != "" && $resultID != "E")
            	{
                	$connected = "SELECTED";
            	}
      			echo "<OPTION $connected value='". $freID . "'>$freName</OPTION>";
				
   			}
		?>
  		</SELECT></td>

<?php 
   }
?>
</tr>
</tbody>
</table>
</div>
</div>







  
  <div class="row">
    <div class="span4"> <h5>Symptoms</h5></div>
  </div>
  <div class="row">
<div class='span8'>
    <table>
        <tbody>
        <tr>
<?php
    $totalsymptoms = $allSymptoms->{"total_records"};
      for ( $i =0; $i <$totalsymptoms; $i++)
      {
            if ($i%2==0) 
            {
              echo "</tr><tr>";
            }    
            $rec=$allSymptoms->{"record"}[$i];
            $symptomID = $rec["id"];
            $symptomName = $rec["symptom"];
            $connected = "N";
            $sql = "SELECT id FROM default_patient_discharge_symptoms WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    symptoms_id = $symptomID";
            $result = $dx->getRecord($sql);
            $resultID = $result[0];
            if($resultID != "" && $resultID != "E")
            {
                $connected = "Y";
            }
?>
        <td class="span4">
        <input type="checkbox" class="span1" id="record_symptom_<?php echo $symptomID; ?>" name="record_symptom_<?php echo $symptomID; ?>" value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        <?php echo "$symptomName </td>"; ?>

<?php 
   }
?>
</tr>
</tbody>
</table>
</div>
</div>


<div class="row">
    <div class="span4"> <h5>Videos</h5></div>
  </div>
  <div class="row">
<div class='span8'>
    <table>
        <tbody>
        <tr>
<?php
    $totalvideos = $allVideos->{"total_records"};
      for ( $i =0; $i <$totalvideos; $i++)
      {
            if ($i%2==0) 
            {
              echo "</tr><tr>";
            }    
            $rec=$allVideos->{"record"}[$i];
            $videoID = $rec["id"];
            $videoName = $rec["video"];
            $connected = "N";
            $sql = "SELECT id FROM default_patient_discharge_videos WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    video_id = $videoID";
            $result = $dx->getRecord($sql);
            $resultID = $result[0];
            if($resultID != "" && $resultID != "E")
            {
                $connected = "Y";
            }
?>
        <td class="span4">
        <input type="checkbox" class="span1" id="record_video_<?php echo $videoID; ?>" name="record_video_<?php echo $videoID; ?>" value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        <?php echo "$videoName </td>"; ?>

<?php 
   }
?>
</tr>
</tbody>
</table>
</div>
</div>


<div class="row">
    <div class="span4"> <h5>Physical Activity</h5></div>
  </div>
  <div class="row">
<div class='span8'>
    <table>
        <tbody>
        <tr>
<?php
    $totalpa = $allPa->{"total_records"};
      for ( $i =0; $i <$totalpa; $i++)
      {
            if ($i%2==0) 
            {
              echo "</tr><tr>";
            }    
            $rec=$allPa->{"record"}[$i];
            $paID = $rec["id"];
            $paName = $rec["physical_activity"];
            $connected = "N";
            $sql = "SELECT id FROM default_patient_discharge_physicial_activity WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    physicial_activity_id = $paID";
            $result = $dx->getRecord($sql);
            $resultID = $result[0];
            if($resultID != "" && $resultID != "E")
            {
                $connected = "Y";
            }
?>
        <td class="span4">
        <input type="checkbox" class="span1" id="record_pa_<?php echo $paID; ?>" name="record_pa_<?php echo $paID; ?>" value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        <?php echo "$paName </td>"; ?>

<?php 
   }
?>
</tr>
</tbody>
</table>
</div>
</div>


<div class="row">
    <div class="span4"> <h5>Wound Care</h5></div>
  </div>
  <div class="row">
<div class='span8'>
    <table>
        <tbody>
        <tr>
<?php
    $totalwc = $allWound->{"total_records"};
      for ( $i =0; $i <$totalwc; $i++)
      {
            if ($i%2==0) 
            {
              echo "</tr><tr>";
            }    
            $rec=$allWound->{"record"}[$i];
            $wcID = $rec["id"];
            $wcName = $rec["description"];
            $connected = "N";
            $sql = "SELECT id FROM default_patient_discharge_wound_care WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    wound_care_id = $wcID";
            $result = $dx->getRecord($sql);
            $resultID = $result[0];
            if($resultID != "" && $resultID != "E")
            {
                $connected = "Y";
            }
?>
        <td class="span4">
        <input type="checkbox" class="span1" id="record_wc_<?php echo $wcID; ?>" name="record_wc_<?php echo $wcID; ?>" value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        <?php echo "$wcName </td>"; ?>

<?php 
   }
?>
</tr>
</tbody>
</table>
</div>
</div>

<div class="row">
    <div class="span4"> <h5>Patient Tasks</h5></div>
  </div>
  <div class="row">
<div class='span8'>
    <table>
    	<thead>
		<tr>
			<th >Task</th>
			<th >Frequency</th>
		</tr>
		</thead>
        <tbody>
<?php
    
    $sql = "SELECT id, task, frequency_id, isactive FROM default_patient_discharge_patient_tasks WHERE 
                    default_patient_discharge_group_id = $groupID ";
	$allTasks = $dx->getOnlyRecords($sql);
	$totaltasks = $allTasks->{"total_records"};
	$totalfre = $allFre->{"total_records"};
	for ( $i =0; $i <$totaltasks; $i++)
    {
            echo "<tr>";    
            $rec=$allTasks->{"record"}[$i];
            $taskID = $rec["id"];
            $taskName = $rec["task"];
			$taskFre = $rec["frequency_id"];
            $connected = $rec["isactive"];
?>
        <td class="span4">
        <input type="checkbox" class="span1" 
        id="record_task_<?php echo $taskID; ?>" 
        name="record_task_<?php echo $taskID; ?>" 
        value="Y" <?php if ($connected == "Y") { echo "CHECKED"; } ?> />
        <input type="text" id="record_task_description_<?php echo $taskID; ?>" 
        name="record_task_description_<?php echo $taskID; ?>" 
        value="<?php echo $taskName; ?>" placeholder="Task to Complete" />
        </td><td>
        <SELECT  id="record_task_frequency_<?php echo $taskID; ?>" >
		<?php
   			for ( $i2 =0; $i2 <$totalfre; $i2++)
   			{
      			$rr_rec=$allFre->{"record"}[$i2];
				$freID=$rr_rec["id"];
      			$freName=$rr_rec["name"];
      			$connected="";
				$sql = "SELECT id FROM default_patient_discharge_patient_tasks WHERE 
                    default_patient_discharge_group_id = $groupID AND 
                    frequency_id = $freID AND id = $taskID";
            	$result = $dx->getRecord($sql);
            	$resultID = $result[0];
            	if($resultID != "" && $resultID != "E")
            	{
                	$connected = "SELECTED";
            	}
      			echo "<OPTION $connected value='". $freID . "'>$freName</OPTION>";
				
   			}
		?>
  		</SELECT>
        </td></tr>

<?php 
   }
?>
</tr>
<tr>
	<td class="span4">
        <input type="checkbox" class="span1" 
        id="record_task_new" 
        name="record_task_new" 
        value="Y" />
        <input type="text" id="record_task_description_new" 
        name="record_task_description_new" 
        value="" placeholder="Task to Complete" />
        </td><td>
        <SELECT  id="record_task_frequency_new" >
		<?php
   			for ( $i2 =0; $i2 <$totalfre; $i2++)
   			{
      			$rr_rec=$allFre->{"record"}[$i2];
				$freID=$rr_rec["id"];
      			$freName=$rr_rec["name"];
      			echo "<OPTION value='". $freID . "'>$freName</OPTION>";
   			}
		?>
  		</SELECT>
        </td>
</tr>
</tbody>
</table>
</div>
</div>
</BR>
</BR>
<?php } ?>
</div>
  
            <div class='login-actions'>
                <input type="button"  onClick="callme('record,savedefault,')" value="Save" />
                <input type="button"  onClick="callform('diagnosislist.php')" value="Go Back" />
            </div>

<?php include("foot.php") ?>
  <script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
