<?php require_once("init_setup.php") ?>
<?php
	$ctrl = Controller::get(); 
    $dx = DataModel::get();
	
	 
	 $current_day = gmdate("Y-m-d");
	 $current_time = gmdate("H");
	 $current_ts = gmdate("Y-m-d H:i:s");
	 $day_to_remove = gmdate("Y-m-d", strtotime('-2 days'));
	 $start_of_day_to_remove = gmdate("Y-m-d", strtotime('-1 days'));
	 
	 
	 echo "Current Day : $current_day </br>";
	 echo "Current Time : $current_time </br>";
	 echo "Current ts : $current_ts </br>";
	 echo "Yesterday day : $day_to_remove </br></br>";
	 
	 
	 $sql = "SELECT * FROM patient_todo WHERE scheduled_ts < '$start_of_day_to_remove' 
	 		 and completed = 'N'";
	 $records = $dx->getOnlyRecords($sql);
	 $totalrows = $records->{"total_records"};

     for ( $colidx =0; $colidx <$totalrows; $colidx++)
     {
     	$rec = $records->{"record"}[$colidx];
		$l_patient_id = $rec["patient_id"];
        $l_medication_id = $rec["activity_id"];
        $l_ts = $rec["ts"];
		
		$fields =   " patient_id,log_type,record_id,total_choice,total_answer,response ";
        $values =    $ctrl->MYSQLQ($l_patient_id) . "," .
                     $ctrl->MYSQLQ('medication') . "," .
                     $ctrl->MYSQLQ($l_medication_id) . "," .
                     $ctrl->MYSQLQ(1) . "," .
                     $ctrl->MYSQLQ(0) . "," .
                     $ctrl->MYSQLQ('Y');

        $sql= "insert into user_activity ( $fields ) values ( $values ) ";
		//TODO: the line below needs to be uncommented when 
		//records need to be added to user_activity.
        
        //$result = $ctrl->execute($sql);
		
     }
	 
	 //Remove old tasks
	 $sql = "SELECT * FROM patient_todo WHERE 
	   		scheduled_ts < '$start_of_day_to_remove'";
	 $records = $dx->getOnlyRecords($sql);
	 $totalrows = $records->{"total_records"};
	 
	 for ( $colidx =0; $colidx <$totalrows; $colidx++)
     {
     	$rec = $records->{"record"}[$colidx];
		$l_id = $rec["id"];
        
        $sql= "DELETE FROM patient_todo WHERE id = $l_id ";
        $result = $ctrl->execute($sql);
		
     }
	 //Start medication tasks
	 $sql="SELECT id FROM patient WHERE isactive = 'Y'";
     $patient_ids = $dx->getRecordIds($sql);
     foreach ($patient_ids as $key => $value) 
     {
        $currentpatient = $value[0];
		$sql = "SELECT med_start_time FROM patient WHERE id = $currentpatient";
		$current_start_time = $ctrl->getRecordField($sql);
		$sql = "SELECT time_zone FROM patient WHERE id = $currentpatient";
		$patient_offset = $ctrl->getRecordField($sql);
		$local_time = date("Y-m-d H:i:s", strtotime($current_day) + ($current_start_time * 3600));
		
		
		echo "Patient id : $currentpatient </br>";
		
		$sql = "SELECT id, frequency, patient_id, medication_id  
				FROM medication WHERE 
				patient_id = $currentpatient AND isactive = 'Y' AND 
				start_date < '$current_day' AND end_date > '$current_day'"; 
		$records = $dx->getOnlyRecords($sql);
		$totalrows = $records->{"total_records"};
		
		echo print_r($records);
		echo "</br></br>";
		for ( $idx =0; $idx <$totalrows; $idx++)
		{
			$rec = $records->{"record"}[$idx];
			$l_medication_id = $rec["id"];
			$l_freq = $rec["frequency"];
			$l_med_id = $rec["medication_id"];
			$sql = "SELECT medication FROM medication_list WHERE id = $l_med_id";
			$medicationName = $ctrl->getRecordTEXT($sql);
			$time_for_meds = $ctrl->getUTC($local_time, $patient_offset);
			//TODO: Finish at bed time and narcotics
			$times_a_day = 0;
			$hours_to_add = 0;
			if($l_freq == 1 || $l_freq == 5 || $l_freq == 9 || $l_freq == 14) //1
			{
				$times_a_day = 1;
				$hours_to_add = 0;
			}
			else if ($l_freq == 2 || $l_freq == 6 || $l_freq == 10 || $l_freq == 15) 
			{
				$times_a_day = 2;
				$hours_to_add = 12;
			}
			else if ($l_freq == 3 || $l_freq == 7 || $l_freq == 11 || $l_freq == 16) 
			{
				$times_a_day = 3;
				$hours_to_add = 8;
			}
			else if ($l_freq == 4 || $l_freq == 8 || $l_freq == 12 || $l_freq == 17) 
			{
				$times_a_day = 4;
				$hours_to_add = 6;
			}
			else if ($l_freq == 13 || $l_freq == 18 ) 
			{
				$times_a_day = 6;
				$hours_to_add = 4;
			}
			else if ($l_freq == 19 ) //at bedtime
			{
				$times_a_day = 0;
				$hours_to_add = 0;
			}
			else //narcotics
			{
				$times_a_day = 0;
				$hours_to_add = 0;
			}
			for ( $i = 0; $i < $times_a_day; $i++)
			{
				if($i != 0)
				{
					$time_for_meds = date("Y-m-d H:i:s", strtotime($time_for_meds) + ($hours_to_add * 3600));
				}
					$fields =   " patient_id,activity_type,activity_id,description,scheduled_ts,completed";
        			$values =   $ctrl->MYSQLQ($currentpatient) . "," .
        						$ctrl->MYSQLQ("M") . "," .
                     			$ctrl->MYSQLQ($l_medication_id) . "," .
                     			$ctrl->MYSQLQ($medicationName) . "," .
                     			$ctrl->MYSQLQ($time_for_meds) . "," . 
                     			$ctrl->MYSQLQ('N');

        			$sql= "insert into patient_todo ( $fields ) values ( $values ) ";
        			$result = $ctrl->execute($sql);  //Uncomment this for full testing.
        			if($result == 1)
        			{
        				echo "SUCCESS - $sql </br></br>";
        			}
					else 
					{
						echo "FAIL - $sql </br></br>";
					}
			}
		} //end medications for loop
	 }//end patient for loop

	 
	 //Start patient tasks
	 $sql="SELECT id FROM patient WHERE isactive = 'Y'";
     $patient_ids = $dx->getRecordIds($sql);
     foreach ($patient_ids as $key => $value) 
     {
        $currentpatient = $value[0];
		$sql = "SELECT med_start_time FROM patient WHERE id = $currentpatient";
		$current_start_time = $ctrl->getRecordField($sql);
		$sql = "SELECT time_zone FROM patient WHERE id = $currentpatient";
		$patient_offset = $ctrl->getRecordField($sql);
		$local_time = date("Y-m-d H:i:s", strtotime($current_day) + ($current_start_time * 3600));
		
		
		echo "Patient id : $currentpatient </br>";
		
		$sql = "SELECT id, frequency_id, task, patient_id  
				FROM patient_tasks WHERE 
				patient_id = $currentpatient AND isactive = 'Y' AND 
				start_date < '$current_day' AND end_date > '$current_day'"; 
		$records = $dx->getOnlyRecords($sql);
		$totalrows = $records->{"total_records"};
		
		echo print_r($records);
		echo "</br></br>";
		for ( $idx =0; $idx <$totalrows; $idx++)
		{
			$rec = $records->{"record"}[$idx];
			$l_task_id = $rec["id"];
			$l_freq = $rec["frequency_id"];
			$taskName = $rec["task"];
			$time_for_meds = $ctrl->getUTC($local_time, $patient_offset);
			//TODO: Finish at bed time and narcotics
			$times_a_day = 0;
			$hours_to_add = 0;
			if($l_freq == 1 || $l_freq == 5 || $l_freq == 9 || $l_freq == 14) //1
			{
				$times_a_day = 1;
				$hours_to_add = 0;
			}
			else if ($l_freq == 2 || $l_freq == 6 || $l_freq == 10 || $l_freq == 15) 
			{
				$times_a_day = 2;
				$hours_to_add = 12;
			}
			else if ($l_freq == 3 || $l_freq == 7 || $l_freq == 11 || $l_freq == 16) 
			{
				$times_a_day = 3;
				$hours_to_add = 8;
			}
			else if ($l_freq == 4 || $l_freq == 8 || $l_freq == 12 || $l_freq == 17) 
			{
				$times_a_day = 4;
				$hours_to_add = 6;
			}
			else if ($l_freq == 13 || $l_freq == 18 ) 
			{
				$times_a_day = 6;
				$hours_to_add = 4;
			}
			else if ($l_freq == 19 ) //at bedtime
			{
				$times_a_day = 0;
				$hours_to_add = 0;
			}
			else //narcotics
			{
				$times_a_day = 0;
				$hours_to_add = 0;
			}
			for ( $i = 0; $i < $times_a_day; $i++)
			{
				if($i != 0)
				{
					$time_for_meds = date("Y-m-d H:i:s", strtotime($time_for_meds) + ($hours_to_add * 3600));
				}
					$fields =   " patient_id,activity_type,activity_id,description,scheduled_ts,completed";
        			$values =   $ctrl->MYSQLQ($currentpatient) . "," .
        						$ctrl->MYSQLQ("T") . "," .
                     			$ctrl->MYSQLQ($l_task_id) . "," .
                     			$ctrl->MYSQLQ($taskName) . "," .
                     			$ctrl->MYSQLQ($time_for_meds) . "," . 
                     			$ctrl->MYSQLQ('N');

        			$sql= "insert into patient_todo ( $fields ) values ( $values ) ";
        			$result = $ctrl->execute($sql);  //Uncomment this for full testing.
        			if($result == 1)
        			{
        				echo "SUCCESS - $sql </br></br>";
        			}
					else 
					{
						echo "FAIL - $sql </br></br>";
					}
			}
		} //end tasks for loop
	 }//end patient for loop
	 
	 
	 //Start for Vitals
	 $sql="SELECT id FROM patient WHERE isactive = 'Y'";
     $patient_ids = $dx->getRecordIds($sql);
     foreach ($patient_ids as $key => $value) 
     {
        $currentpatient = $value[0];
		$sql = "SELECT med_start_time FROM patient WHERE id = $currentpatient";
		$current_start_time = $ctrl->getRecordField($sql);
		$sql = "SELECT time_zone FROM patient WHERE id = $currentpatient";
		$patient_offset = $ctrl->getRecordField($sql);
		$local_time = date("Y-m-d H:i:s", strtotime($current_day) + ($current_start_time * 3600));
		
		
		echo "Patient id : $currentpatient </br>";
		
		$sql = "SELECT id, frequency_id, patient_id, vital_id 
				FROM vitals WHERE 
				patient_id = $currentpatient AND isactive = 'Y' "; 
		$records = $dx->getOnlyRecords($sql);
		$totalrows = $records->{"total_records"};
		
		echo print_r($records);
		echo "</br></br>";
		for ( $idx =0; $idx <$totalrows; $idx++)
		{
			$rec = $records->{"record"}[$idx];
			$l_vital_id = $rec["id"];
			$l_freq = $rec["frequency_id"];
			$l_vit_id = $rec["vital_id"];
			$sql = "SELECT vital FROM vitals_list WHERE id = $l_vit_id";
			$vitalName = $ctrl->getRecordTEXT($sql);
			$time_for_vital = $ctrl->getUTC($local_time, $patient_offset);
			//TODO: Finish at bed time and narcotics
			$times_a_day = 0;
			$hours_to_add = 0;
			if($l_freq == 1 || $l_freq == 5 || $l_freq == 9 || $l_freq == 14) //1
			{
				$times_a_day = 1;
				$hours_to_add = 0;
			}
			else if ($l_freq == 2 || $l_freq == 6 || $l_freq == 10 || $l_freq == 15) 
			{
				$times_a_day = 2;
				$hours_to_add = 12;
			}
			else if ($l_freq == 3 || $l_freq == 7 || $l_freq == 11 || $l_freq == 16) 
			{
				$times_a_day = 3;
				$hours_to_add = 8;
			}
			else if ($l_freq == 4 || $l_freq == 8 || $l_freq == 12 || $l_freq == 17) 
			{
				$times_a_day = 4;
				$hours_to_add = 6;
			}
			else if ($l_freq == 13 || $l_freq == 18 ) 
			{
				$times_a_day = 6;
				$hours_to_add = 4;
			}
			else if ($l_freq == 19 ) //at bedtime
			{
				$times_a_day = 0;
				$hours_to_add = 0;
			}
			else //narcotics
			{
				$times_a_day = 0;
				$hours_to_add = 0;
			}
			for ( $i = 0; $i < $times_a_day; $i++)
			{
				if($i != 0)
				{
					$time_for_vital = date("Y-m-d H:i:s", strtotime($time_for_vital) + ($hours_to_add * 3600));
				}
					$fields =   " patient_id,activity_type,activity_id,description,scheduled_ts,completed";
        			$values =   $ctrl->MYSQLQ($currentpatient) . "," .
                     			$ctrl->MYSQLQ("V") . "," .
                     			$ctrl->MYSQLQ($l_vital_id) . "," .
                     			$ctrl->MYSQLQ($vitalName) . "," .
                     			$ctrl->MYSQLQ($time_for_vital) . "," . 
                     			$ctrl->MYSQLQ('N');

        			$sql= "insert into patient_todo ( $fields ) values ( $values ) ";
        			$result = $ctrl->execute($sql);  //Uncomment this for full testing.
        			if($result == 1)
        			{
        				echo "SUCCESS - $sql </br></br>";
        			}
					else 
					{
						echo "FAIL - $sql </br></br>";
					}
			}
		} //end vitals for loop
	 }//end patient for loop


?>