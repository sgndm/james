<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $yesterday = mktime(0, 0, 0, date('m') , date('d')-1, date('Y'));
      $l_dt =  date('Y-m-d H:i:s', $yesterday);
      $dateTest = date(strtotime("-1 day"));
      $l_dt_run ="";
      $sql="SELECT patient_id FROM `user_activity_medication` WHERE (`created_ts` > DATE_SUB(now(), INTERVAL 1 DAY))";
      $x_obj=json_decode("{}");
      $submissions = $dx->getRecordIds($sql);
      $sql="SELECT id FROM patient WHERE isactive = 'Y'";
      $patient_ids = $dx->getRecordIds($sql);
      
      
      /*
       * Steps:
       * 1. Save results of yesterday into user_activity based on patient medication compliance.
       * 2. Clear out med_activity table. 
       * 3. Loop through patients.
       *    a. Get patient's med start time.
       *    b. Loop through medications the patient has.
       *        I. Get frequency for current medication.
       *        II. Based on frequency get all times patient needs to take that medication on that day.
       *        III. Sort all the times in order starting with the earliest(12AM).
       *    c. Create SQL statement that will add to med_activity the following:
       *        I. User id
       *        II. medicine
       *        III. time1 (for example it would hold 9 for 9AM) through time6 (all times not used will hold 25)
       *        IV. time1_taken through time6_taken all filled with 'N'.
       *        V. all timestamps needed.
       *        VI. sec_id that connects with the medication table.
       */
       
       
       /*
        * STEP 1: Save results of yesterday into user_activity based on patient medication compliance.
        */
      foreach ($patient_ids as $key => $value) 
      {
        $foundRow = FALSE;
        $currentvalue = $value[0];
        
        foreach ($submissions as $k => $v) 
        { 
            if($v[0] == $currentvalue)
            {
                //we have a match
                $foundRow = TRUE;
            }
        }
         
         if($foundRow == FALSE)
         {
             $count = 0;
             $sql = "SELECT time1, time2, time3, time4, time5, time6 FROM med_activity WHERE patient_id = $currentvalue AND ( now() BETWEEN start_date AND end_date ) AND isactive = 'Y'";
             $timesToLookAt = $dx->getRecordIds($sql);
             foreach ($timesToLookAt as $key => $value) 
             {
                 if($value[0] != 25)
                 {
                     $count++;
                 }
             }
             if($count > 0)
             {
                $ctrl = Controller::get();
                $fields =   " patient_id,log_type,record_id,total_choice,total_answer,response,created_ts ";
                $values =    $ctrl->MYSQLQ($id) . "," .
                          $ctrl->MYSQLQ('medication') . "," .
                          $ctrl->MYSQLQ(80) . "," .
                          $ctrl->MYSQLQ($count) . "," .
                          $ctrl->MYSQLQ(0) . "," .
                          $ctrl->MYSQLQ('Y') . "," .
                          $ctrl->MYSQLQ($l_dt);

                $sql= "insert into user_activity ( $fields ) values ( $values ) ";
                $result = $ctrl->execute($sql);
                if ($result == 1) 
                {
                    
                }
                else 
                {
                    echo $ctrl->getServerError();
                }
            }
            
        }
      }


        /*
         * Set up user_narcotics for yesterday.
         */
            $sql = "SELECT medicine, patient_id, times_taken, limit_times FROM med_activity WHERE time1 = 25 ORDER BY patient_id";
            $pain_meds_info = $dx->getRecordIds($sql);
            foreach ($patient_ids as $key => $value) 
            {
                $foundRow = FALSE;
                $currentID = $value[0];
                $medicationNames = array();
                $medicationPercents = array();
                $currentPosition = 0;
            
                for($i = 0; $i < count($pain_meds_info); $i++)
                {
                    $rec = $pain_meds_info[$i];
                    $patientID = $rec["patient_id"];
                    if($currentID == $patientID)
                    {
                        $lMedicine = $rec["medicine"];
                        $lMax = $rec["limit_times"];
                        $lTaken = $rec["times_taken"];
                        $lPercent = round((($lTaken / $lMax) * 100),3);
                        
                        
                        $medicationNames[$currentPosition] = $lMedicine;
                        $medicationPercents[$currentPosition] = $lPercent;
                        $currentPosition++;
                    }
                    
                  }
                  $med1 = $medicationNames[0];
                  $med2 = $medicationNames[1];
                  $med3 = $medicationNames[2];
                  $percent1 = $medicationPercents[0];
                  $percent2 = $medicationPercents[1];
                  $percent3 = $medicationPercents[2];
                  $ctrl = Controller::get();
                $fields =   " patient_id,med1,med2,med3,percent1,percent2,percent3,created_ts ";
                $values =    $ctrl->MYSQLQ($currentID) . "," .
                          $ctrl->MYSQLQ($med1) . "," .
                          $ctrl->MYSQLQ($med2) . "," .
                          $ctrl->MYSQLQ($med3) . "," .
                          $ctrl->MYSQLQ($percent1) . "," .
                          $ctrl->MYSQLQ($percent2) . "," .
                          $ctrl->MYSQLQ($percent3) . "," .
                          $ctrl->MYSQLQ($l_dt);

                $sql= "insert into user_narcotics ( $fields ) values ( $values ) ";
                $result = $ctrl->execute($sql);
                if ($result == 1) 
                {
                    //DONE
                }
                else 
                {
                    echo $ctrl->getServerError();
                }
                  
             }
         
         
        
        /*
        * STEP 2
        */
        $sql = "TRUNCATE TABLE med_activity";
        $result = $ctrl->execute($sql);
        if ($result == 1) 
        {
               
            
        }
        else 
        {
            echo $ctrl->getServerError();
        }
        
        /*
        * STEP 3
        */
        //LOOP THROUGH PATIENTS
        foreach ($patient_ids as $key => $value) 
        {
            $currentPatientID = $value[0];
            $data='{"user_id":"' . $currentPatientID . '"}';
            $x_obj=json_decode($data);
            $currentPatientStartTime = $dx->getStartTime($x_obj);
            
            //FIND ALL MEDICATIONS
            $sqlForMedications = "select m.id,m.patient_id,l.medication, m.frequency,
            						DATE_FORMAT(m.start_date,'%m/%d/%y')  start_date_fmt,
            						DATE_FORMAT(m.end_date,'%m/%d/%y')  end_date_fmt 
            						from medication_list l, medication m 
            						where l.id = m.medication_id and m.patient_id = $currentPatientID 
            						and DATE(DATE_ADD(now(),INTERVAL 1 DAY)) 
            						between date(m.start_date) and date(m.end_date) and m.isactive = 'Y' ";
            $medicationsForCurrentPatient = $dx->getRecords($x_obj,$sqlForMedications);
            $medtotalrows = $medicationsForCurrentPatient->{"total_records"};

            //LOOP THROUGH MEDICATIONS
            for ( $colidx =0; $colidx <$medtotalrows; $colidx++)
            {
                $rec=$medicationsForCurrentPatient->{"record"}[$colidx];
                $lMedication = $rec["medication"];
                $lFrequency = $rec["frequency"];
                $lMedicationTableID = $rec["id"];
                $frequencies = array();
                $limit = 0;
                
                
                //FIND TIMES THAT GO WITH CURRENT FREQUENCY
                if($lFrequency < 19) //This means Start Time is included in the Frequency
                {
                    $frequencies[] = $currentPatientStartTime;
                }
                if($lFrequency == 3 || $lFrequency == 4
                 || $lFrequency == 7 || $lFrequency == 8 
                 || $lFrequency == 13 || $lFrequency == 18) //This means Frequency requires intervals of 4 hours
                {
                    $timeToAdd = $ctrl->AddHours($currentPatientStartTime, 4);
                    $frequencies[] = $timeToAdd;
                    $timeToAdd = $ctrl->AddHours($timeToAdd, 4);
                    $frequencies[] = $timeToAdd;
                    if($lFrequency == 4 || $lFrequency > 7)
                    {
                        $timeToAdd = $ctrl->AddHours($timeToAdd, 4);
                        $frequencies[] = $timeToAdd;
                        if ($lFrequency >= 13 ) 
                        {
                            $timeToAdd = $ctrl->AddHours($timeToAdd, 4);
                            $frequencies[] = $timeToAdd;
                            $timeToAdd = $ctrl->AddHours($timeToAdd, 4);
                            $frequencies[] = $timeToAdd;
                        }
                    }
                    
                }
                if ($lFrequency == 12 || $lFrequency == 17) //This means Frequency requires intervals of 6 hours
                {
                    $timeToAdd = $ctrl->AddHours($currentPatientStartTime, 6);
                    $frequencies[] = $timeToAdd;
                    $timeToAdd = $ctrl->AddHours($timeToAdd, 6);
                    $frequencies[] = $timeToAdd;
                    $timeToAdd = $ctrl->AddHours($timeToAdd, 6);
                    $frequencies[] = $timeToAdd;
                }
                if ($lFrequency == 2 || $lFrequency == 6 
                || $lFrequency == 11 || $lFrequency == 16) //This means Frequency requires intervals of 8 hours
                {
                    $timeToAdd = $ctrl->AddHours($currentPatientStartTime, 8);
                    $frequencies[] = $timeToAdd;
                    if($lFrequency > 7)
                    {
                        $timeToAdd = $ctrl->AddHours($timeToAdd, 8);
                        $frequencies[] = $timeToAdd;
                    }
                    
                    
                }
                if($lFrequency == 10 || $lFrequency == 15)//This means Frequency requires intervals of 12 hours
                {
                    $timeToAdd = $ctrl->AddHours($currentPatientStartTime, 12);
                    $frequencies[] = $timeToAdd;
                }
                if ($lFrequency == 19) //This means Frequency requires Bedtime(9PM) only
                {
                    $frequencies[] = 21;
                }
                if ($lFrequency == 20) //This means Frequency has a max of 1 a day.
                {
                    $limit = 1;
                }
                if ($lFrequency == 21) //This means Frequency has a max of 2 a day.
                {
                    $limit = 2;
                }
                if ($lFrequency == 22) //This means Frequency has a max of 3 a day.
                {
                    $limit = 3;
                }
                if ($lFrequency == 23) //This means Frequency has a max of 4 a day.
                {
                    $limit = 4;
                }
                if ($lFrequency == 24) //This means Frequency has a max of 6 a day.
                {
                    $limit = 6;
                }
                
                
                //SORT ARRAY OF FREQUENCIES.  
                sort($frequencies);
                
                
                //FILL TIME1 - TIME6
                $time1 = 25;
                $time2 = 25;
                $time3 = 25;
                $time4 = 25;
                $time5 = 25;
                $time6 = 25;
                if($frequencies[0])
                {
                    $time1 = $frequencies[0];
                }
                if($frequencies[1])
                {
                    $time2 = $frequencies[1];
                }
                if($frequencies[2])
                {
                    $time3 = $frequencies[2];
                }
                if($frequencies[3])
                {
                    $time4 = $frequencies[3];
                }
                if($frequencies[4])
                {
                    $time5 = $frequencies[4];
                }
                if($frequencies[5])
                {
                    $time6 = $frequencies[5];
                }
                
                
                
                //ADD TO DATABASE
                $fields =   " patient_id, sec_id, medicine,time1,time2,time3,time4,time5,time6,limit_times,current_ts";

                $values =    $ctrl->MYSQLQ($currentPatientID) . "," .
                            $ctrl->MYSQLQ($lMedicationTableID) . "," .
                            $ctrl->MYSQLQ($lMedication) . "," .
                            $ctrl->MYSQLQ($time1) . "," .
                            $ctrl->MYSQLQ($time2) . "," .
                            $ctrl->MYSQLQ($time3) . "," .
                            $ctrl->MYSQLQ($time4) . "," .
                            $ctrl->MYSQLQ($time5) . "," .
                            $ctrl->MYSQLQ($time6) . "," .
                            $ctrl->MYSQLQ($limit) . "," .
                            $ctrl->MYSQLQ($tm);

                $sql= "insert into med_activity ( $fields ) values ( $values ) ";
                
                $l_resid = $ctrl->execute($sql);
                if ( $l_resid == 1 ) 
                {
                    //DONE! 
                }
                else
                {
                   echo "FAILURE <BR> $sql <BR>";
                }
                if($limit != 0)
                {
                    $fields =   " patient_id,log_type,record_id,total_choice,total_answer,response,created_ts ";
                    $values =    $ctrl->MYSQLQ($currentPatientID) . "," .
                                 $ctrl->MYSQLQ("painMedication") . "," .
                                 $ctrl->MYSQLQ($lMedicationTableID) . "," .
                                 $ctrl->MYSQLQ($limit) . "," .
                                 $ctrl->MYSQLQ(0) . "," .
                                 $ctrl->MYSQLQ("") . "," .
                                 $ctrl->MYSQLQ($tm);

                    $sql= "insert into user_activity ( $fields ) values ( $values ) ";
                    $l_resid = $ctrl->execute($sql);
                }
                
                
            }//END LOOP THROUGH MEDICATIONS
            
        }
      
?>