<?php require_once("init_setup.php") ?>
<?php
     $ctrl = Controller::get(); 
     $dx = DataModel::get(); 
      
	 
	 $current_day = gmdate("Y-m-d");
	 $current_time = gmdate("H"); 
	 $scheduled_datetime = date("Y-m-d H:i:s", strtotime($current_day) + ($current_time * 3600));
	   
	 $sql="SELECT id FROM patient WHERE isactive = 'Y'";
     $patient_ids = $dx->getRecordIds($sql);
     foreach ($patient_ids as $key => $value) 
     {
        $current_patient = $value[0];
		$current_medications = "";
		$current_vitals = "";
		$current_tasks = "";
		$isrecord = 0;
		$isMedRecord = 0;
		$isVitalRecord = 0;
		$isTaskRecord = 0;
		$sql = "SELECT * FROM patient_todo 
				WHERE patient_id = $current_patient AND 
				scheduled_ts = '$scheduled_datetime' ";
		$records = $dx->getOnlyRecords($sql);
		$totalrows = $records->{"total_records"};
		for ( $colidx =0; $colidx <$totalrows; $colidx++)
     	{
     		$isrecord = 1;
     		$rec = $records->{"record"}[$colidx];
			$l_activity_id = $rec["activity_id"];
			$l_activity_type = $rec["activity_type"];
			$activity_name = $rec["description"];
			if($l_activity_type == "M")
			{
				$isMedRecord = 1;
				$current_medications .= " $activity_name";
			}
			else if($l_activity_type == "V")
			{
				$isVitalRecord = 1;
				$current_vitals .= " $activity_name";
			}
			else if($l_activity_type == "T")
			{
				$isTaskRecord = 1;
				$current_tasks .= " $activity_name";
			}
			
			
		
     	}
     	if($isrecord == 1) //send notification
		{
			$alerttype = "";
			if($isMedRecord == 1)
			{
				$current_medications = "Please take the listed medication(s): \n $current_medications \n";
				$alerttype .= "M_";
			}
			if($isVitalRecord == 1)
			{
				$current_vitals = "Please record the listed vital(s): \n $current_vitals";
				$alerttype .= "V_";
			}
			if($isTaskRecord == 1)
			{
				$current_tasks = "Please complete the listed task(s): \n $current_tasks";
				$alerttype .= "T_";
			}
			$sql = "SELECT * FROM patient WHERE id = $current_patient";
			$patient_details = $dx->getOnlyRecords($sql);
			$patient = $patient_details->{"record"}[0];
			$l_message  =  $current_medications . $current_vitals;
         	$l_phone  =  $patient["phone"];
         	$l_phone_carrier  =  $patient["phone_carrier"];
         	$l_name  =  $patient["first_name"] . " " .  $patient["last_name"];
			
			
			$px = null;
            $px=json_decode("{'none':'none'}");
            $l_to =$l_phone."@".$l_phone_carrier;
            $px->{"to"}=$l_to;
            $px->{"patient_id"}=$current_patient;
            $px->{"alert_type"}=$alerttype . $current_time;
            $px->{"message"}=" $l_message ";
            $px->{"subject"}="Task Reminder";
            $ctrl = Controller::get(); 
            echo "calling task alert <BR>
            to=$l_to <BR>
            me=$l_message <BR>";
            $ux = UserModel::get(); 
            if($alerttype == "T_")
            {
            	//$ux->push_notificationlog($px);
            	//$r1 = $ctrl->sendMail($px);
            }
            //$ux->push_notificationlog($px);
            //$r1 = $ctrl->sendMail($px);
			
			
			
		}
	 } //end task check
	  
?>
