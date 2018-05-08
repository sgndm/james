<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 

      $l_dt =  date('Y-m-d H:i:s');
      $l_dt_run ="";
      $sql="select DATE_FORMAT(DATE_ADD(now(),INTERVAL 1 DAY),'%m/%d/%Y') ";
      $l_dt_run = $ctrl->getRecordText($sql);
      $sql=" select u.first_name, u.last_name ,u.phone,
      		 u.phone_carrier, m.id,m.patient_id,x.symptom, 
      		 DATE_FORMAT(m.start_date,'%m/%d/%y')  start_date_fmt, 
      		 DATE_FORMAT(m.end_date,'%m/%d/%y')  end_date_fmt 
      		 from symptoms_list x, symptoms m, patient u 
      		 where x.id = m.symptom_id and u.id = m.patient_id and 
      		 DATE(DATE_ADD(now(),INTERVAL 1 DAY))  between 
      		 date(m.start_date) and date(m.end_date) and 
      		 u.isactive = 'Y' and m.isactive = 'Y' 
      		 order by patient_id";
     
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
                senddata($cr_obj);
                $l_message ="";
         }
         $l_st_patient_id= $l_patient_id;
         $l_symptom  =  $rec["symptom"];
         $l_phone  =  $rec["phone"];
         $l_phone_carrier  =  $rec["phone_carrier"];
         $l_name  =  $rec["first_name"] . " " .  $rec["last_name"];

         $l_message .= $l_symptom . ", \r " ;
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
         senddata($cr_obj);
      }


      function senddata($pcr_data)
      {
            $l_name=$pcr_data->{"name"};
            $l_patient_id=$pcr_data->{"patient_id"};
            $l_phone=$pcr_data->{"phone"};
            $l_phone_carrier=$pcr_data->{"phone_carrier"};
            $l_message=$pcr_data->{"message"};
            $l_dt_run = $pcr_data->{"dt_run"};

            $px=json_decode("{}");
            $l_to =$l_phone."@".$l_phone_carrier;
            $px->{"to"}=$l_to;
            $px->{"patient_id"}=$l_patient_id;
            $px->{"alert_type"}="symptoms";
            $px->{"message"}="Report your symptoms in MobiMD";
            $px->{"subject"}="Symptom Assessment";
            $ctrl = Controller::get(); 
            echo "<BR/>calling symptom alert  to=$l_to me=$l_message";
            $ux = UserModel::get(); 
            $ux->push_notificationlog($px);
            $px->{"message"}="Report your symptoms in MobiMD ";
            $r1 = $ctrl->sendMail($px);
      }
    ?>
