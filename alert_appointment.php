<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      
      $l_date = mktime(date('H'), date('i'), 0, date('m') , date('d')+1, date('Y'));
      $tomorrow =  date('Y-m-d H:i:s', $l_date);
      $l_date = mktime(date('H'), date('i'), 0, date('m') , date('d')+7, date('Y'));
      $next_week =  date('Y-m-d H:i:s', $l_date);
      echo "Tomorrow : $tomorrow <BR>";
      echo "Next Week : $next_week<BR><BR>";
       
       
      //testing only remember to remove after testing is complete.
      //$tomorrow = "2014-08-07 08:30:00";
      
      $sql="SELECT * FROM appointments WHERE (appointment_ts = '$tomorrow' OR appointment_ts = '$next_week')";
      //echo "$sql <BR><BR>";
      $appointmentsTomorrow = $dx->getOnlyRecords($sql);
      $totalrows = $appointmentsTomorrow->{"total_records"};
      for ( $i =0; $i <$totalrows; $i++)
      {
            $rec=$appointmentsTomorrow->{"record"}[$i];
            $lPatientID = $rec["patient_id"];
            $lDoctorID = $rec["doctor_id"];
            $lAppointmentTS = $rec["appointment_ts"];
            $lTime =  date('h:i A', strtotime($lAppointmentTS));
            $lDay =  date('D, F d', strtotime($lAppointmentTS));
            echo "PatientID : $lPatientID 
            <BR> Appointment : $lAppointmentTS 
            <BR> Day : $lDay 
            <BR> Time : $lTime 
            <BR>";
            $isTomorrow = false;
            if($lAppointmentTS == $tomorrow)
                $isTomorrow = true;
            
            $sql=" SELECT 
                first_name,
                last_name,
                phone,
                phone_carrier 
                FROM patient 
                WHERE id = $lPatientID ";
            $lPatientInformation = $dx->getOnlyRecords($sql);
            $sql="SELECT first_name FROM user WHERE id = $lDoctorID";
            $lDoctorName = $ctrl->getRecordTEXT($sql);
            $patientRec = $lPatientInformation->{"record"}[0];
            $lPatientName = $patientRec["first_name"] . " " . $patientRec["last_name"];
            $lTo = $patientRec["phone"]."@".$patientRec["phone_carrier"];
            $px=json_decode("{'none':'none'}");
            $px->{"to"}=$lTo;
            $px->{"patient_id"}=$lPatientID;
            $px->{"user_id"}= 166;
            $px->{"alert_type"}="appt";
            $px->{"message"}="You have an appointment with $lDoctorName on $lDay at $lTime";
            if($isTomorrow)
            {
                $px->{"message"} = "Don\'t forget your appointment tomorrow with $lDoctorName at $lTime, you can log on to MobiMD for directions";
            }
            $px->{"subject"}="Appointment Reminder";
            $ux = UserModel::get(); 
            $sqlMade = $ux->push_notificationlog($px);
            echo "<BR> $sqlMade 
            <BR>
            <BR>";
            $r1 = $ctrl->sendMail($px);
            
      }
      
      
?>