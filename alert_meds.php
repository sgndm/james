<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $l_show_time= $argv[1];

      $l_dt =  date('Y-m-d H:i:s');
      if(!$l_show_time)
      {
          $l_show_time = 50;
          echo "$l_show_time" . ":00 <BR><BR>";
      }
      $l_dt_run ="";
      $sql="select DATE_FORMAT(now(),'%m/%d/%Y') ";
      $l_dt_run = $ctrl->getRecordText($sql);
      $sql=" SELECT DISTINCT 
                u.first_name,
                u.last_name,
                u.phone,
                u.phone_carrier, 
                ma.patient_id,
                ma.medicine 
                FROM patient u, med_activity ma 
                WHERE u.id = ma.patient_id AND 
                u.isactive = 'Y' AND 
                ma.time1 < 25 AND 
                (ma.time1 = $l_show_time OR ma.time2 = $l_show_time OR ma.time3 = $l_show_time OR 
                ma.time4 = $l_show_time OR ma.time5 = $l_show_time OR ma.time6 = $l_show_time )  
                ORDER BY ma.patient_id ";
      $x_obj=json_decode("{}");
      $l_obj = $dx->getRecords($x_obj,$sql);
      $medtotalrows = $l_obj->{"total_records"};
     

      $l_st_patient_id=0;
      $l_message="";
      $l_gotit=0;
      for ( $colidx =0; $colidx <$medtotalrows; $colidx++)
      {
         $rec=$l_obj->{"record"}[$colidx];
         $l_patient_id  =  $rec["patient_id"];
         if ( $l_st_patient_id > 0 && $l_patient_id  != $l_st_patient_id )
         {
                $cr_obj=json_decode("{}");
                $cr_obj->{"patient_id"}=$l_st_patient_id;
                $cr_obj->{"dt_run"}=$l_dt_run;
                $cr_obj->{"name"}=$l_name;
                $cr_obj->{"phone"}=$l_phone;
                $cr_obj->{"phone_carrier"}=$l_phone_carrier;
                $cr_obj->{"message"}=$l_message;
                $cr_obj->{"show_time"}=$l_show_time;
                senddata($cr_obj);
                $l_message ="";
         }
         $l_st_patient_id= $l_patient_id;
         $l_medication  =  $rec["medicine"];
         $l_phone  =  $rec["phone"];
         $l_phone_carrier  =  $rec["phone_carrier"];
         $l_name  =  $rec["first_name"] . " " .  $rec["last_name"];

            $l_message .= $l_medication . ", ";
         
      }
      if ( $medtotalrows > 0 ) 
      {
         $cr_obj=json_decode("{}");
         $cr_obj->{"patient_id"}=$l_st_patient_id;
         $cr_obj->{"dt_run"}=$l_dt_run;
         $cr_obj->{"name"}=$l_name;
         $cr_obj->{"phone"}=$l_phone;
         $cr_obj->{"phone_carrier"}=$l_phone_carrier;
         $cr_obj->{"message"}=$l_message;
         $cr_obj->{"show_time"}=$l_show_time;
         senddata($cr_obj);
      }


      $mess ="";

      function senddata($pcr_data)
      {
            $l_name=$pcr_data->{"name"};
            $l_patient_id=$pcr_data->{"patient_id"};
            $l_phone=$pcr_data->{"phone"};
            $l_phone_carrier=$pcr_data->{"phone_carrier"};
            $l_message=$pcr_data->{"message"};
            $l_dt_run = $pcr_data->{"dt_run"};
            $l_show_time=$pcr_data->{"show_time"};

            $px = null;
            $px=json_decode("{'none':'none'}");
            $l_to =$l_phone."@".$l_phone_carrier;
            $px->{"to"}=$l_to;
            $px->{"patient_id"}=$l_patient_id;
            $px->{"alert_type"}="medication_" . $l_show_time;
            $px->{"message"}="Please take the listed medication(s): \n $l_message ";
            $px->{"subject"}="Medication Reminder";
            $ctrl = Controller::get(); 
            echo "calling medcation alert <BR>
            to=$l_to <BR>
            me=$l_message <BR>";
            $ux = UserModel::get(); 
            $ux->push_notificationlog($px);
            //$px->{"message"}="Take medication assessment in MobiMD";
            $r1 = $ctrl->sendMail($px);
      }
    ?>
