<?php
class DataModel  extends Base
{
	public function __construct() 
   { 
   } 
	static $st_instance = null;
	public static function get()
   { 
      if ( DataModel::$st_instance == null  )
         DataModel::$st_instance = new DataModel();
      return DataModel::$st_instance;
   } 
	public function getUser($t3_obj)
   {
     $ctrl = Controller::get();
     $user_id= $t3_obj->{"user_id"};
     $res="";
     $sql = "select * from user a " .
          " where a.id = $user_id ";
     $rec = $ctrl->getRecord($sql);
     if ( count($rec) > 0 ) 
     {
      foreach(array_keys($rec) as $key){
           $t3_obj->{$key}=$rec[$key];
      }
      $t3_obj->{"user_id"}=$rec["id"];
      $t3_obj->{"user_type"}=$rec["user_type"];
      $t3_obj->{"status"}="success";
     }
     else
     {
        $t3_obj->{"recordid"}=0;
        $t3_obj->{"status"}="failure";
        $t3_obj->{"message"}= DB_ERROR;
     }
     return $t3_obj;
   } // get user
	public function authenticate($t3_obj)
  {
     $ctrl = Controller::get();
	 $ip_address = $_SERVER["REMOTE_ADDR"];
     $username = $t3_obj->{"username"};
     $pa = $t3_obj->{"password"};
     $uid = $t3_obj->{"deviceid"};
     $t3_obj->{"status"}="None";
     $t3_obj->{"message"}="None";
     $_SESSION["mobile_user_id"]=$uid;
     $r1 = $ctrl->lb_chkpwd($username,$pa);
     $t3_obj->{"replydata"}=$r1;
     if ( $r1["status"]  == "success" )
     {
        $_SESSION[$uid]=$r1["user_id"];
        $_SESSION["mobile_patient_id"]=$r1["user_id"];
        $t3_obj->{"message"}="matched";
        $t3_obj->{"uid"}=$uid;
        $t3_obj->{"status"}="success";
        $t3_obj->{"message"}="success";
		$fields = "ip_address, login_name, success";
			   $values =  $ctrl->MYSQLQ($ip_address) . "," .
                          $ctrl->MYSQLQ($username) . "," .
                          $ctrl->MYSQLQ("Y");
			   $sql = "insert into login_attempts ( $fields ) values ( $values )";
			   $l_resid = $ctrl->execute($sql);
     }
     else
     {
        $t3_obj->{"message"}=$r1["message"];
        $t3_obj->{"status"}="failure";
		$fields = "ip_address, login_name, success";
			   $values =  $ctrl->MYSQLQ($ip_address) . "," .
                          $ctrl->MYSQLQ($username) . "," .
                          $ctrl->MYSQLQ("N");
			   $sql = "insert into login_attempts ( $fields ) values ( $values )";
			   $l_resid = $ctrl->execute($sql);
     }
     return $t3_obj;
  } // authenticate 
	public function recover($t3_obj)
   {
         $l_email= $t3_obj->{'username'};
         require_once("UserModel.php");
         $ux = UserModel::get(); 
         return $ux->forgotpassword_call($l_email);
   }
	public function oldxl7Data($t3_obj)
   {

      $ctrl = Controller::get();
      $t3_obj->{"status"}="failure";
      $t3_obj->{"message"}= "failure";
      $l_segment =  $t3_obj->{'segment'};
      $l_user_id= $ctrl->getUserID();

      $flname=$l_segment . ".schema";
      $handle = fopen($flname, "r");
      if ($handle) {
          $ctr=0;
          while (($line = fgets($handle)) !== false) {
           if ( substr($line,0,1) == "#" )
           {
             continue;
           }
           $fld = explode("|",$line);
           $pid=trim($fld[0]);
           $type=trim($fld[1]);
           $size=trim($fld[2]);
           $name=trim($fld[3]);
           $t3_obj->{"fieldpid"}[$ctr]=$pid;
           $t3_obj->{"fieldtype"}[$ctr]=$type;
           $t3_obj->{"fieldsize"}[$ctr]=$size;
           $t3_obj->{"fieldname"}[$ctr]=$name;
           $ctr++;
           $t3_obj->{"fieldtotal"}=$ctr;
          }
          $t3_obj->{"status"}="success";
          $t3_obj->{"message"}= "success";
      } 
      $flname=$l_segment . ".data";
      $handle = fopen($flname, "r");
      $lne=0;
      if ($handle) {
          $ctr=0;
          $sql="";
          while (($line = fgets($handle)) !== false) 
          {
             $rec=$line;
             $fldnames="";
             $fldvals="";
             $fldschema="";
             $dlim="";

             $line=$rec;
             $fld = explode("|",$line);
             $tot_ctr =count($fld);
             $ctr=0;
             $ld_name = "fieldvalue" . "_" . $lne;
             for ( $idx=1;$idx<$tot_ctr;$idx++)
             {
                $l_val = $fld[$idx];
                $t3_obj->{$ld_name}[$ctr]=$l_val;

                $l_fld = "field_" . ( $ctr + 1 );
                $fldnames .=  $dlim . $l_fld ;
                $fldvals .=  $dlim . $this->MYSQLQ($l_val); 
                $l_name = $t3_obj->{"fieldname"}[$ctr];
                $fldschema .=  $dlim . $this->MYSQLQ($l_name); 
                $dlim=",";
   
                $ctr++;

             }
             $lne++;

                /*$tm =  date('Y-m-d H:i:s');
                $fldnames .=  $dlim . "user_id";
                $fldvals .=  $dlim . $this->MYSQLQ($l_user_id);
                $fldschema .=  $dlim . $this->MYSQLQ($l_user_id);
           
                $fldnames .=  $dlim . "segment_id";
                $fldvals .=  $dlim . $this->MYSQLQ($l_segment);
                $fldschema .=  $dlim . $this->MYSQLQ($l_segment);
           
                $fldnames .=  $dlim . "created_user_id";
                $fldvals .=  $dlim . $this->MYSQLQ($l_user_id);
                $fldschema .=  $dlim . $this->MYSQLQ($l_user_id);
           
                $fldnames .=  $dlim . "created_ts";
                $fldvals .=  $dlim . $this->MYSQLQ($tm);
                $fldschema .=  $dlim . $this->MYSQLQ($tm);
           
                $fldnames .=  $dlim . "updated_user_id";
                $fldvals .=  $dlim . $this->MYSQLQ($l_user_id);
                $fldschema .=  $dlim . $this->MYSQLQ($l_user_id);


                $fldnames .=  $dlim . "updated_ts";
                $fldvals .=  $dlim . $this->MYSQLQ($tm);
                $fldschema .=  $dlim . $this->MYSQLQ($tm); */
                //$sql="insert into xl7datatable ( $fldnames ) values ( $fldvals)";

                //$sql="insert into xl7schematable ( $fldnames ) values ( $fldschema)";
          }
      } //handle
      $t3_obj->{"totalseg"}= $lne;
      return $t3_obj;
    }
	public function getHl7Data($t3_obj)
   {

      $ctrl = Controller::get();


      $t3_obj->{"status"}="failure";
      $t3_obj->{"message"}= "failure";
      $l_segment =  $t3_obj->{'segment'};
      $l_user_id= $ctrl->getUserID();

      $arr  = $this->get_hl7_schema($l_segment);

      $rec =  $arr[0];

      $t3_obj->{"status"}="success";
      $t3_obj->{"message"}= "success";

      $tot_fields=$arr["field_count"];

      for ( $idx=0;$idx<$tot_fields;$idx++)
      {
           $l_fld="field_" . ( $idx + 1);
           $lx="fieldname_" . ( $idx + 1);
           $l_val= $arr[$l_fld];
           $t3_obj->{$lx}=$arr[$l_fld];
      } 

      $arr  = $this->get_hl7_data($l_segment);
      $tot_records=count($arr);

      for ( $r_idx=0;$r_idx<$tot_records;$r_idx++)
      {
          $rec=$arr[$r_idx];
          for ( $f_idx=1;$f_idx<$tot_fields;$f_idx++)
          {
              $l_nm = "fieldvalue_" . $r_idx .  "_" . ($f_idx+1);
              $lx = "field_" .  ($f_idx + 1 );
              $l_val = $rec[$lx];
              $l_val = str_replace("^"," ",$l_val);
              $l_val = str_replace("\""," ",$l_val);
              $l_val = str_replace("''","  ",$l_val);
              $l_val = str_replace(",,,","   ",$l_val);
              $l_val = trim($l_val);
              if ( $l_val == null )
                  $l_val ="";
              $t3_obj->{$l_nm}=$l_val;
          }
       }
       $t3_obj->{"total_fields"}=$tot_fields;
       $t3_obj->{"total_records"}=$tot_records;
       return $t3_obj;
    }
	public function get_hl7_schema($p_segment_id)
   {
      $ctrl = Controller::get();
      $sql = " select * from hl7schematable where segment_id =  "  . $this->MYSQLQ($p_segment_id) ;
      $l_record = $ctrl->getRecord($sql);
      return $l_record;
   } 
	public function get_hl7_data($p_segment_id)
   {
      $ctrl = Controller::get();

      $sql = " select * from hl7datatable where segment_id =  "  . $this->MYSQLQ($p_segment_id) ;
      $l_record = $ctrl->query($sql);
      return $l_record;
   } 
	public function getAppointments($t3_obj)
   {
     $ctrl = Controller::get();
     $user_id= $ctrl->getUserID();
     $user_id= $t3_obj->{"user_id"};
     $sql = "select  a.id,DATE_FORMAT(a.appointment_ts,'%Y') appointment_ts_yyyy, DATE_FORMAT(a.appointment_ts,'%m') appointment_ts_mm, DATE_FORMAT(a.appointment_ts,'%d') appointment_ts_dd, DATE_FORMAT(a.appointment_ts,'%W,%D %M %Y %h:%i %p') appointment_ts_fmt, a.appointment_ts, c.first_name clinic_name, c.address1, c.address2, ". 
            "c.city, c.state, c.zipcode, a.doctor_id " .
            "from user c , appointments a".
            " where a.clinic_id  = c.id ".
            "   and a.isactive  = 'Y' " .
            "   and a.patient_id = $user_id ";
     if ( $ctrl->getval($t3_obj,"yyyy") > 0 )
     {
        $sql .= " and DATE_FORMAT(a.appointment_ts,'%Y') = ".  $t3_obj->{"yyyy"} ;
        $sql .= " and DATE_FORMAT(a.appointment_ts,'%m') = ".  ( $t3_obj->{"mm"} + 1 ) ;
     }
	 $sql .= " order by a.appointment_ts DESC";
     return $this->getRecords($t3_obj,$sql);
   } // 
	public function getDoctorName($id)
   {
       $sql = "SELECT first_name FROM user WHERE id = $id";
       $records = $this->getRecord($sql);
       return $records[0];
   }
	public function getMedications($t3_obj)
   {
     $ctrl = Controller::get();
     $user_id  = $ctrl->getval($t3_obj,"user_id");
     $record_id= $ctrl->getval($t3_obj,"record_id");

     $sql = "select a.*,  b.medication, b.description, b.url, 
             DATE_FORMAT(a.start_date,'%m/%d/%Y')  start_date_fmt, DATE_FORMAT(a.end_date,'%m/%d/%Y') end_date_fmt 
             from medication a, medication_list b 
             where a.medication_id = b.id  
             and  a.patient_id = $user_id ";
	$l_isactive= $ctrl->getval($t3_obj,"isactive");
	if ( $l_isactive  ) 
		$sql .= " and a.isactive = " . $ctrl->MYSQLQ($l_isactive);

     return $this->getRecords($t3_obj,$sql);
   } //   
   public function getTasks($t3_obj)
   {
     $ctrl = Controller::get();
     $user_id  = $ctrl->getval($t3_obj,"user_id");

     $sql = "select id, task, patient_id, frequency_id, isactive, 
     		 DATE_FORMAT(start_date,'%m/%d/%Y')  start_date_fmt, 
     		 DATE_FORMAT(end_date,'%m/%d/%Y') end_date_fmt 
             from patient_tasks
             where patient_id = $user_id ";
	$l_isactive= $ctrl->getval($t3_obj,"isactive");
	if ( $l_isactive  ) 
		$sql .= " and isactive = " . $ctrl->MYSQLQ($l_isactive);

     return $this->getOnlyRecords($sql);
   }
	public function getFrequency($t3_obj)
	   {
	       $sql = "SELECT * FROM frequency";
	       $records = $this->getOnlyRecords($sql);
	       return $records;
	   } 
	public function getHours($t3_obj)
   {
       $sql = "SELECT * FROM hourmaster";
       return $this->getOnlyRecords($sql);
   }
	public function getStartTime($t3_obj)
	   {
	       $ctrl = Controller::get();
	       $user_id  = $ctrl->getval($t3_obj,"user_id");
	       $sql = "SELECT med_start_time FROM patient WHERE id = $user_id";
	       return $ctrl->getRecordTEXT($sql);
	   }  
	public function getSymptoms($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $t3_obj->{"user_id"};
	     $record_id= $ctrl->getval($t3_obj,"record_id");
	     $includenosymptom= $ctrl->getval($t3_obj,"includenosymptom");
	     $sql = "select a.start_date, a.isactive, a.end_date ,
	     		 DATE_FORMAT(a.start_date,'%m/%d/%Y')  start_date_fmt, 
	     		 DATE_FORMAT(a.end_date,'%m/%d/%Y')  end_date_fmt ,
	     		 a.id record_id, a.id, a.patient_id,b.symptom, b.symptom symptoms, 
	     		 b.url, b.id symptom_id  
	     		 from symptoms a, symptoms_list b where b.id = a.symptom_id 
	               and a.patient_id = $user_id ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
		 if ( $l_isactive  ) 
			$sql .= " and a.isactive = " . $ctrl->MYSQLQ($l_isactive);
	
	     $sql .=  "order by  1 ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getSymptomsmobile($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $t3_obj->{"user_id"};
	     $record_id= $ctrl->getval($t3_obj,"record_id");
	     $includenosymptom= $ctrl->getval($t3_obj,"includenosymptom");
	     $sql = "select b.id record_id, a.id, a.patient_id,b.symptom, b.symptom symptoms, b.url   ".
	             " from symptoms a, symptoms_list b where b.id = a.symptom_id " . 
	             "  and a.patient_id = $user_id ".
	             "  and a.isactive = 'Y' ";
	     $sql .= "  and date(now()) between date(a.start_date) and date(a.end_date)";
	     if ( $includenosymptom == 1 ) 
	     {
	         //go back
	     }
	        
	     if ( $record_id > 0 ) 
	        $sql .= " and a.id = " .$ctrl->MYSQLQ($record_id);
	     $sql .=  " order by  1 ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getPhysicalActivity($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select a.* , b.physical_activity ".
	             " from physical_activity a, physical_activity_list b  " . 
	             " where a.physical_activity_id = b.id and a.patient_id = $user_id ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
		 if ( $l_isactive  ) 
			$sql .= " and a.isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getPhysicalActivityMobile($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select a.* , b.physical_activity as physicalactivity  ".
	             " from physical_activity a, physical_activity_list b  " . 
	             " where a.physical_activity_id = b.id and a.patient_id = $user_id ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
		 if ( $l_isactive  ) 
			$sql .= " and a.isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getDiagnosis($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select b.description , b.url ".
	             " from patient a, diagnosis_list b  " . 
	             " where a.discharge_diagnosis = b.id and a.id = $user_id ";
	     $records = $this->getRecords($t3_obj,$sql);
	     return $records;
	   }
	public function getDiet($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select a.*, b.diet   " .
	             " from diet a, diet_list b " . 
	             " where a.patient_id = $user_id and b.id = a.diet_id ";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
		if ( $l_isactive  ) 
			$sql .= " and a.isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   } //
	public function getMyVital($t3_obj)
	{
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select a.*, b.vital   " .
	             " from vitals a, vitals_list b " . 
	             " where a.patient_id = $user_id and b.id = a.vital_id ";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
		if ( $l_isactive  ) 
			$sql .= " and a.isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	} 
	public function getFaqs($t3_obj)
	   {
	       $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "SELECT discharge_diagnosis FROM patient WHERE id = $user_id";
	     $number = $ctrl->getRecordTEXT($sql);
	     $sql = "select question, answer, url ".
	             " from faqs  where diagnosis_id = $number" ;
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getWoundcare($t3_obj)
	{
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select wc.* , wcl.description".
	             " from wound_care wc, wound_care_list wcl  " .
	             " where wc.patient_id = $user_id and wcl.id = wc.wound_care_id";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
		if ( $l_isactive  ) 
		$sql .= " and wc.isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	} // 
	public function getNotifications($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select description".
	             " from notifications  " .
	             " where patient_id = $user_id ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getVideos($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select a.id, a.isactive, a.video_id, a.patient_id,b.video, b.url 
	     		 from videos a, videos_list b where b.id = a.video_id 
	               and a.patient_id = $user_id";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
		 if ( $l_isactive  ) 
			$sql .= " and a.isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getSymptom_list($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t_id= $t3_obj->{"record_id"};
	     $sql = "select * from symptoms_list ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	     
	   } // 
	public function getVital_list($t3_obj)
	{
		$ctrl = Controller::get();
	     $t_id= $t3_obj->{"record_id"};
	     $sql = "select * from vitals_list ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	}
	public function getClinics($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "SELECT * FROM user WHERE user_type=2";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " and isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getDoctors($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "SELECT * FROM user WHERE user_type=3";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " and isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getCC($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t_id= $ctrl->getval($t3_obj,"record_id");
	     $sql = "SELECT * FROM user WHERE user_type=4";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " and isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getAdmins($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t_id= $ctrl->getval($t3_obj,"record_id");
	     $sql = "SELECT * FROM user WHERE user_type=1 ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " and isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getOrganizations($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t_id= $ctrl->getval($t3_obj,"record_id");
	     return $this->getTableRecord($t3_obj,"organization","id",$t_id,null);
	   } //  
	public function getPatients($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "select * from patient  " ;
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " where isactive = " . $ctrl->MYSQLQ($l_isactive);
	     
		 return $this->getRecords($t3_obj,$sql);
	   } 
	public function getPatientsForClinic($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "SELECT * FROM patient WHERE clinic_id = $user_id " ;
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " AND isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getPatientsForDoctor($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $stringToSend = "SELECT clinic_id FROM clinic_doctor WHERE doctor_id = $user_id AND isConnected = 'Y'";
	     $clinic_ids = $ctrl->getRecordIDs($stringToSend);
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "SELECT * FROM patient WHERE (clinic_id = 0 " ;
	     foreach ($clinic_ids as $key => $value) 
	     {
	         $sql .= "OR clinic_id = $value[0] ";
	     }
	     $sql .= ")";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " AND isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getPatientsForCC($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $ctrl->getUserID();
	     $stringToSend = "SELECT clinic_id FROM user WHERE id = $user_id";
	     $clinic_id = $ctrl->getRecordID($stringToSend);
	     $user_id= $t3_obj->{"user_id"};
	     $sql = "SELECT * FROM patient WHERE clinic_id = $clinic_id " ;
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= " AND isactive = " . $ctrl->MYSQLQ($l_isactive);
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getPatient($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id= $t3_obj->{"user_id"};
	     $res="";
	     $sql = "select a.*, t.name from patient a, timezones t  
	           where a.id = $user_id AND t.gmt = a.time_zone";
	     $rec = $ctrl->getRecord($sql);
	     if ( count($rec) > 0 ) 
	     {
	      foreach(array_keys($rec) as $key){
	           $t3_obj->{$key}=$rec[$key];
	      }
	      $doc_obj=json_decode('{}');
	      $l_doc_id = $rec["doctor_id"];
	      $l_clinic_id = $rec["clinic_id"];
	      if ( $l_doc_id > 0 ) 
	      {
	         $sql = "select * from user a " .
	             " where a.id = '$l_doc_id' ";
	         $rec = $ctrl->getRecord($sql);
	         if ( count($rec) > 0 ) 
	         {
	             foreach(array_keys($rec) as $key){
	               $doc_obj->{$key}=$rec[$key];
	             }
	         }
	      }
	      $t3_obj->{"doctor"}=$doc_obj;
	
	      $clinic_obj=json_decode('{}');
	      if( $l_clinic_id > 0 ) 
	      {
	        $sql = "select * from user a " .
	            " where a.id = '$l_clinic_id' ";
	        $rec = $ctrl->getRecord($sql);
	        if ( count($rec) > 0 ) 
	        {
	            foreach(array_keys($rec) as $key){
	                 $clinic_obj->{$key}=$rec[$key];
	            }
	         }
	      }
	      $t3_obj->{"clinic"}=$clinic_obj;
	
	      $t3_obj->{"user_id"}=$rec["id"];
	      $t3_obj->{"status"}="success";
	     }
	     else
	     {
	        $t3_obj->{"recordid"}=0;
	        $t3_obj->{"status"}="failure";
	        $t3_obj->{"message"}= DB_ERROR;
	     }
	     return $t3_obj;
	   } // get user
	public function getAppointment($t3_obj,$p_id)
	   {
	     $ctrl = Controller::get();
	     $sql = "select  DATE_FORMAT(a.appointment_ts,'%m/%d/%Y') appointment_ts_mmddyyy,
	     		 DATE_FORMAT(a.appointment_ts,'%h:%i  %p') appointment_ts_time, 
	     		 DATE_FORMAT(a.appointment_ts,'%i') appointment_ts_mi, 
	     		 DATE_FORMAT(a.appointment_ts,'%p') appointment_ts_ampm, 
	     		 a.appointment_ts, d.id doctor_id,d.name doctor_name, 
	     		 c.id clinic_id, c.name clinic_name, c.address1, c.address2, 
	     		 c.city, c.state, c.zipcode 
	     		 from doctor d , clinic c , appointments a 
	     		  where a.doctor_id = d.id and a.clinic_id  = c.id 
	     		     and a.id = $p_id ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getUsers($t3_obj)
	   {
	     $t_id= $t3_obj->{"record_id"};
	     return $this->getTableRecord($t3_obj,"user","id",$t_id,null);
	   } // 
	public function mobilerecoverpwd($t3_obj)
	   {
	         $ctrl = Controller::get(); 
	         $username = $t3_obj->{"username"};
	         $l_res=false;
	         $sql = "select id, password ,first_name  , email from patient " .
	                " where ( email = " . $ctrl->MYSQLQ($username)  .
	                "    or username = " . $ctrl->MYSQLQ($username) . ")";
	         $l_record  = $ctrl->getRecord($sql);
			 $newpassword = $this->randomPassword();
			 $passwordhash = password_hash($newpassword, PASSWORD_DEFAULT);
			 $fieldlists =   " password  =  " . $ctrl->Q($passwordhash) ;
	         if ( $l_record && count($l_record) > 0  && $l_record[0]  > 0 ) 
	         {
	         	$patientID=$l_record["id"];
				$sql= "update patient set  " . $fieldlists . 
                               " where id   = " . $ctrl->MYSQLQ($patientID) ;
                $l_resid = $ctrl->execute($sql);
             	if ( $l_resid == 1 ) 
             	{
             		$server = $_SERVER['SERVER_NAME'];	 
                    $urlToSend = $server . "/passwordreset.php?pass=" . $newpassword . 
			 		"&id=". $patientID;
	            	$pwd=$l_record["password"];
	            	$nm=$l_record["first_name"];
	            	$l_email=$l_record["email"];
	            	// send email
	            	$dt=json_decode("{'none':'none'}");
	            	$dt->{"to"} = $l_email;
	            	$dt->{"subject"} = "You requested a password " ;
	            	$dlim="\r\n\r\n";
	            	$dt->{"message"} = "Dear $nm, $dlim $dlim
	            			You recently requested to reset your password. $dlim
	            			Please follow the link below to reset your password: $dlim 
	            			$urlToSend  $dlim
	            			If you did not request to reset your password, let us know immediately. $dlim 
	            			MobiMD Support Team";
	
	            	$ctrl->sendmail($dt);
	            	$t3_obj->{"status"}="success";
	            	$t3_obj->{"message"}="Email sent ...Please check your email ";
             	}
				else
	         	{
	            	$t3_obj->{"message"}= "server error";
	            	$t3_obj->{"status"}="false";
	         	}
	         	
	         }
	         else
	         {
	            $t3_obj->{"message"}= "server error";
	            $t3_obj->{"status"}="false";
	         }
	         return $t3_obj;
	  } // forgotpassword
	public function randomPassword() 
	{
    	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    	$pass = array();
    	$alphaLength = strlen($alphabet) - 1;
    	for ($i = 0; $i < 10; $i++) 
    	{
        	$n = rand(0, $alphaLength);
        	$pass[] = $alphabet[$n];
    	}
    	return implode($pass);
	}
	public function getVersion($t3_obj)
	{
		$ctrl = Controller::get(); 
	    $currentVersion = $t3_obj->{"version"};
		$sql = "SELECT MAX(version_number) as version FROM mobile_versions";
		$highestVersion = $ctrl->getRecordTEXT($sql);
		if($currentVersion < $highestVersion)
		{
			$t3_obj->{"status"}="success";
			$t3_obj->{"message"}="MobiMD has been updated.";
		}
		else 
		{
			$t3_obj->{"status"}="failure";
		}
		return $t3_obj;
	}
	public function createVideoLog($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t1_patient_id= $t3_obj->{"user_id"};
	     $p_record_id= $t3_obj->{"record_id"};
	     $t3_obj->{"patient_id"}=$t1_patient_id;
	     $t3_obj->{"log_type"}="video";
	     $t3_obj->{"record_id"}=$p_record_id;
	     $t3_obj->{"response"}="";
	     return $this->createuserlog($t3_obj);
	   } // 
	public function mychart1($t_obj)
	  {
	      $l_information   = $t_obj->{"information"};
	      $l_functype   = $t_obj->{"functype"};
	      $l_charttype   = $t_obj->{"charttype"};
	      $l_chartid   = $t_obj->{"chartid"};
	      $allrec = $this->getcommon($t_obj);
	      $med1 = "";
	      $med2 = "";
	      $med3 = "";
	
	      $fieldtotal = $allrec->{"total_records"};
	      $l_dataval = "";
	      $l_cnt=0;
	      $l_dia=0;
	      $l_hr=0;
	      if ( $fieldtotal > 31) 
	         $fieldtotal = 31; 
	      for ( $idx =0; $idx <$fieldtotal; $idx++)
	      {
	         $rec=$allrec->{"record"}[$idx];
	         $l_lbl=$rec["fld"];
	         
	         //if ( ( $l_functype   == "medicationassessmenttaken" ) || ( $l_functype   == "symptomassessmenttaken" ) )
	         //   $l_cnt =$rec["cnt"];
	
	         if( $l_information  == "Cumulative" )
	         {
	            if ( $l_functype == "medicalcompliance" || $l_functype == "bodyTemp" || $l_functype == "bodyWeight" || $l_functype == "patientsignin")
	            {
	                $l_cnt =$rec["cnt"];
	            }
	            else if($l_functype == "painpills")
	            {
	                $l_cnt =$rec["cnt"];
	                $l_per2 =$rec["percent2"];
	                $l_per3 =$rec["percent3"];
	                if($rec["med1"] != null && $rec["med1"] != "")
	                {
	                    $med1 = $rec["med1"];
	                }
	                if($rec["med2"] != null && $rec["med2"] != "")
	                {
	                    $med2 = $rec["med2"];
	                }
	                if($rec["med3"] != null && $rec["med3"] != "")
	                {
	                    $med3 = $rec["med3"];
	                }
	                
	            }
	            else 
	               $l_cnt +=$rec["cnt"];
	         }
	         else
	         {
	             $l_cnt =$rec["cnt"];
	             
	         }
	
	         if ( $t_obj->{"charttype"}  == "SCATTER" )
	         {
	             $l_lbl=$rec["fldval"];
	             $l_dataval .= "[$l_lbl,$l_cnt],";
	         }
	         else if ( $t_obj->{"charttype"}  == "COMBOCHART" )
	         {
	             if($l_functype == "painpills")
	             {
	                 if($l_cnt == "" || $l_cnt == NULL)
	                 {
	                     $l_cnt = 0;
	                 }
	                 if($l_per2 == "" || $l_per2 == NULL)
	                 {
	                     $l_per2 = 0;
	                 }
	                 if($l_per3 == "" || $l_per3 == NULL)
	                 {
	                     $l_per3 = 0;
	                 }
	                 $l_dataval .= "[\"$l_lbl\",$l_cnt,100,$l_per2,$l_per3],";
	             }
	             else 
	             {
	                 
	                $l_dataval .= "[\"$l_lbl\",$l_cnt,80],";
	             }
	             
	         }
	         else
	         {
	             
	                 $l_dataval .= "[\"$l_lbl\",$l_cnt],";
	                 
	         } 
	          
	       }
	       $t_obj->{"dataval"}=$l_dataval;
	       $t_obj->{"med1"}=$med1;
	       $t_obj->{"med2"}=$med2;
	       $t_obj->{"med3"}=$med3;
	       return $t_obj;
	    }
	public function createVitalChart($t_obj)
	{
		$l_information   = $t_obj->{"information"};
	      $l_functype   = $t_obj->{"functype"};
	      $l_charttype   = $t_obj->{"charttype"};
	      $l_chartid   = $t_obj->{"chartid"};
	      $l_graphvital = $t_obj->{"graphvital"};
		  $l_type   = $t_obj->{"datatype"};
	      $allrec = $this->getcommon($t_obj);
	      $l_dataval = "";
	      $count = 0;
		  $countMAX = 31;
	      foreach ( $allrec as $rec )
	      {
	      	  if($l_type == "HOURLY")
			  {
			  	$countMAX = 24;
			  }
	          if($count < $countMAX)
	          {
	              $l_lbl=$rec["fld"];
	         
	              $patientInput = $rec["$l_graphvital"];
	              if($patientInput == "" || $patientInput == null)
	                  $patientInput = 0;
	              
	              if($l_charttype  == "SCATTER")
	              {
	                  $l_lbl=$rec["fldval"];
	                  $l_dataval .= "[$l_lbl,$patientInput],";
	              }
	              else if($l_charttype  == "COMBOCHART")
	              {
	                  if($l_graphvital == "Temp")
	                  {
	                      $l_dataval .= "[\"$l_lbl\",$patientInput,98.6],";
	                  }
	              }
	              else 
	              {
	                  $l_dataval .= "[\"$l_lbl\",$patientInput],";
	              }
	           
	              $count++;
	           }//end if  
	       }
	       $t_obj->{"dataval"}=$l_dataval;
	       return $t_obj;
	}
	public function createChart($t_obj)
	   {
	      $l_information   = $t_obj->{"information"};
	      $l_functype   = $t_obj->{"functype"};
	      $l_charttype   = $t_obj->{"charttype"};
	      $l_chartid   = $t_obj->{"chartid"};
	      $l_graphvital = $t_obj->{"graphvital"};
	      $allrec = $this->getcommon($t_obj);
	      $l_dataval = "";
	      $count = 0;
	      foreach ( $allrec as $rec )
	      {
	          if($count < 31)
	          {
	              $l_lbl=$rec["fld"];
	         
	              if($l_graphvital == "Weight")
	              {
	                $patientInput = $rec["Weight (lbs)"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	              }
	              else if($l_graphvital == "Temp")
	              {
	                  $patientInput = $rec["Temperature (F)"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	              }
	              else if($l_graphvital == "Power")
	              {
	                  $patientInput = $rec["Power (watts)"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	              }
	              else if($l_graphvital == "PI")
	              {
	                  $patientInput = $rec["PI"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	              }
	              else if($l_graphvital == "Flow")
	              {
	                  $patientInput = $rec["Flow (L/min)"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	              }
	              else if($l_graphvital == "Setting")
	              {
	                  $patientInput = $rec["Speed Setting"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	              }
	              else if($l_graphvital == "MAP")
	              {
	                  $patientInput = $rec["MAP"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	              }
	              else if($l_graphvital == "Speed")
	              {
	                  $patientInput = $rec["Speed (rpm)"];
	                if($patientInput == "" || $patientInput == null)
	                    $patientInput = 0;
	                else 
	                    $patientInput = $patientInput / 1000;
	                
	              }
	              else if($l_graphvital == "BP")
	              {
	                $s =$rec["Systolic (Blood Pressure)"];
	                if($s == "" || $s == null)
	                    $s = 0;
	                $d =$rec["Diastolic (Blood Pressure)"];
	                if($d == "" || $d == null)
	                    $d = 0;
	                $hr =$rec["Heart Rate"];
	                if($hr == "" || $hr == null)
	                    $hr = 0;
	              }
	              
	              if($l_charttype  == "SCATTER")
	              {
	                  $l_lbl=$rec["fldval"];
	                  $l_dataval .= "[$l_lbl,$patientInput],";
	              }
	              else if($l_charttype  == "COMBOCHART")
	              {
	                  if($l_graphvital == "BP")
	                  {
	                      $l_dataval .= "[\"$l_lbl\",$hr,$d,$d,$s, $s],";
	                  }
	                  else if($l_graphvital == "Temp")
	                  {
	                      $l_dataval .= "[\"$l_lbl\",$patientInput,98.6],";
	                  }
	              }
	              else 
	              {
	                  $l_dataval .= "[\"$l_lbl\",$patientInput],";
	              }
	           
	              $count++;
	           }//end if  
	       }
	       $t_obj->{"dataval"}=$l_dataval;
	       return $t_obj;
	   }
	public function SaveSymptomNote($t3_obj)
	  {
	          $ctrl = Controller::get();
	          $l_record_id = $t3_obj->{"id"};
	          $l_user_id = $t3_obj->{"user_id"};
	          $l_confirm = $t3_obj->{"confirm"};
	          $tm =  date('Y-m-d H:i:s');
	         {
	             $fieldlists =   " response  =  " . $ctrl->MYSQLQ($l_confirm) . "," .
	                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
	                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;
	             $sql= "update symptom set  " . $fieldlists . 
	                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
	             $l_resid = $ctrl->execute($sql);
	             if ( $l_resid == 1 ) 
	             { 
	                   $t3_obj->{"status"}="success";
	                   $t3_obj->{"message"}="success";
	             }
	             else
	             {
	                   $t3_obj->{"message"}= $ctrl->getServerError();
	                   $t3_obj->{"status"}="false";
					   $message = "Symptom Report Saving Error \n User id : $l_user_id \n SQL : $sql";
	          		   $ctrl->sendError($message);
	             }
	         }
	         return $t3_obj;
	    }  
	public function createuserlog($t3_obj)
	  {
	         $ctrl = Controller::get(); 
	         $t1_patient_id = $t3_obj->{"patient_id"};
	         $p_log_type = $t3_obj->{"log_type"};
	         $p_record_id = $t3_obj->{"record_id"};
	         $p_response = $ctrl->getval($t3_obj,"response");
	         $p_choice = $ctrl->getval($t3_obj,"total_choice");
	         $p_answer  = $ctrl->getval($t3_obj,"total_answer");
	         $tm =  date('Y-m-d H:i:s');
	         $fields =   " patient_id,log_type,record_id,total_choice,total_answer,response,created_ts ";
	         $values =    $ctrl->MYSQLQ($t1_patient_id) . "," .
	                          $ctrl->MYSQLQ($p_log_type) . "," .
	                          $ctrl->MYSQLQ($p_record_id) . "," .
	                          $ctrl->MYSQLQ($p_choice) . "," .
	                          $ctrl->MYSQLQ($p_answer) . "," .
	                          $ctrl->MYSQLQ($p_response) . "," .
	                          $ctrl->MYSQLQ($tm);
	
	         $sql= "insert into user_activity ( $fields ) values ( $values ) ";
	         $l_resid = $ctrl->execute($sql);
	         if ( $l_resid == 1 ) 
	         { 
	                    $t3_obj->{"message"}= "Saved" ;
	                    $t3_obj->{"success"}= "true";
	                    $t3_obj->{"status"}="success";
	         }
	         else
	         {
	                   $t3_obj->{"message"}= $ctrl->getServerError();
	                   $t3_obj->{"success"}= "false";
	                    $t3_obj->{"status"}="failure";
	                    $message = "Create user log Error \n Patient id : $t1_patient_id \n SQL : $sql";
	          $ctrl->sendError($message);
	         }
	        return $t3_obj;
	  }
	public function createSymptomLog($t3_obj)
	   {
	     $ctrl = Controller::get();
		 $patient_id = $t3_obj->{"patient_id"};
	     $p_ids= $t3_obj->{"ids"};
		 $customSymptom= $t3_obj->{"custom"};
		 if($customSymptom != null && $customSymptom != "")
		 {
		 	$tm =  date('Y-m-d H:i:s');
		 	$fields = "patient_id, created_ts, response, application_function_id, updated_user_id";
			$values =    $ctrl->MYSQLQ($patient_id) . "," .
	                          $ctrl->MYSQLQ($tm) . "," .
	                          $ctrl->MYSQLQ($customSymptom) . "," .
	                          $ctrl->MYSQLQ('2') . "," .
	                          $ctrl->MYSQLQ('166'); 
			$sql= "insert into user_non_standard_response ( $fields ) values ( $values ) ";
	         $l_resid = $ctrl->execute($sql);
			 $t3_obj->{"response"} = "";
			 $sql = "SELECT id FROM user_non_standard_response WHERE response = '$customSymptom' AND patient_id = $patient_id";
			 $customID = $ctrl->getRecordTEXT($sql);
	          $t3_obj->{"log_type"}="customSymptom";
	          $t3_obj->{"record_id"}=$customID;
	          $t1_obj=$this->createuserlog($t3_obj);
			  if ( $t1_obj->{"status"} !=  "success" )
	             $l_error=1;
	          if($p_ids != null)
			  {
			  	$fld_arr = explode(",",$p_ids);
	        	$tot_ctr =count($fld_arr);
				for ( $idx=0;$idx<$tot_ctr;$idx++)
	     		{
	       			$l_val = $fld_arr[$idx];
	       			if ( $l_val > 0 ) 
	       			{
	          			$p_response= $t3_obj->{"response"};
	          			$t3_obj->{"log_type"}="symptom";
	          			$t3_obj->{"record_id"}=$l_val;
	          			$t1_obj=$this->createuserlog($t3_obj);
	          			if ( $t1_obj->{"status"} !=  "success" )
	             		$l_error=1;
	       			}
		 		}
			  }
			  
		 }
		 else 
		 {
			if($p_ids == null)
	     	{
	        $fld_arr = array(0 => 1 );
	        $tot_ctr =count($fld_arr);
	     	}
	     	else 
	     	{
	        $fld_arr = explode(",",$p_ids);
	        $tot_ctr =count($fld_arr);
	     	}
	     	$l_error=0;
	     	for ( $idx=0;$idx<$tot_ctr;$idx++)
	     	{
	       		$l_val = $fld_arr[$idx];
	       		if ( $l_val > 0 ) 
	       		{
	          		$p_response= $t3_obj->{"response"};
	          		$t3_obj->{"log_type"}="symptom";
	          		$t3_obj->{"record_id"}=$l_val;
	          		$t1_obj=$this->createuserlog($t3_obj);
	          		if ( $t1_obj->{"status"} !=  "success" )
	             	$l_error=1;
	       		}
		 	}
	     
	     } // for
	      if ( $l_error  == 0 ) 
	      { 
	                    $t3_obj->{"message"}= "Symptom Report Saved" ;
	                    $t3_obj->{"success"}= "true";
	                    $t3_obj->{"status"}="success";
	      }
	      else
	      {
	                   $t3_obj->{"message"}= "System error";
	                   $t3_obj->{"success"}= "false";
	                    $t3_obj->{"status"}="failure";
	      }
	      return $t3_obj;
	   } // 
	public function savevitals($t3_obj)
	   {
	       $ctrl = Controller::get();
	       $weight = $t3_obj->{"weight"};
	       $systolic = $t3_obj->{"systolic"};
	       $diastolic = $t3_obj->{"diastolic"};
	       $temperature = $t3_obj->{"temp"};
	       $heart_rate = $t3_obj->{"heart_rate"};
	       $patient_id = $t3_obj->{"patient_id"};
	         $tm =  date('Y-m-d H:i:s');
	         $fields =   " patient_id,weight,systolic,diastolic,temperature,heart_rate,isreviewed,created_ts ";
	         $values =    $ctrl->MYSQLQ($patient_id) . "," .
	                          $ctrl->MYSQLQ($weight) . "," .
	                          $ctrl->MYSQLQ($systolic) . "," .
	                          $ctrl->MYSQLQ($diastolic) . "," .
	                          $ctrl->MYSQLQ($temperature) . "," .
	                          $ctrl->MYSQLQ($heart_rate) . "," .
	                          $ctrl->MYSQLQ('N') . "," .
	                          $ctrl->MYSQLQ($tm);
	
	         $sql= "insert into user_vitals ( $fields ) values ( $values ) ";
	         $l_resid = $ctrl->execute($sql);
	         if ( $l_resid == 1 ) 
	         { 
	                    $t3_obj->{"message"}= "Vitals Report Saved" ;
	                    $t3_obj->{"success"}= "true";
	                    $t3_obj->{"status"}="success";
	         }
	         else
	         {
	                   $t3_obj->{"message"}= $ctrl->getServerError();
	                   $t3_obj->{"success"}= "false";
	                    $t3_obj->{"status"}="failure";
					
	         }
	        return $t3_obj;
	   }
	public function savevitals02($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $p_data = array();
	     $patient_id = $t3_obj->{"patient_id"};
	     $tm =  date('Y-m-d H:i:s');
	     $p_str01 = $t3_obj->{"ids"};
	     $ids = explode(";_:",$p_str01);
	     $p_str02 = $t3_obj->{"values"};
	     $values_array = explode(";_:",$p_str02);
	     $count =count($values_array);
	     $l_error = 0;
	     for ($i=0; $i <= $count; $i++) 
	     {
	         $vitalid = $ids[$i];
	         $currentvalue = $values_array[$i];
	         if($currentvalue > 0)
	         {
	             $fields =   " patient_id,vital_id,value_entered,created_ts ";
	             $values =    $ctrl->MYSQLQ($patient_id) . "," .
	                          $ctrl->MYSQLQ($vitalid) . "," .
	                          $ctrl->MYSQLQ($currentvalue) . "," .
	                          $ctrl->MYSQLQ($tm);
	
	            $sql= "insert into vitals_reported ( $fields ) values ( $values ) ";
	            $l_resid = $ctrl->execute($sql);
	            if($l_resid != 1)
	            {
	            	$l_error = 1;
	            }
	         }
	         
	     }
	     if ( $l_error  == 0 ) 
	     { 
	          $t3_obj->{"message"}= "Vitals Report Saved" ;
	          $t3_obj->{"success"}= "true";
	          $t3_obj->{"status"}="success";
	     }
	     else
	     {
	          $t3_obj->{"message"}= "System error";
	          $t3_obj->{"success"}= "false";
	          $t3_obj->{"status"}="failure";
			  $message = "Vital Report Saving Error \n Patient id : $patient_id \n SQL : $sql";
	          $ctrl->sendError($message);
	     }
	      return $t3_obj;
	   }
	public function getPushMedications($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $user_id  = $t3_obj->{"user_id"};
	     $record_id= $t3_obj->{"record_id"};
	     
	     $sql = "SELECT * FROM med_activity WHERE patient_id = $user_id";
	
	     //$sql = "select a.*, b.medication , b.medication ,DATE_FORMAT(a.start_date,'%m/%d/%y')  start_date_fmt, DATE_FORMAT(a.end_date,'%m/%d/%y')  end_date_fmt FROM medication a, medication_list b" . 
	       //      " where a.medication_id = b.id and a.user_id = $user_id and now() between date(a.start_date) and date(a.end_date) and a.isactive = 'Y' ";
	     //if ( $record_id > 0 ) 
	        //$sql .= " and id = " .$ctrl->MYSQLQ($record_id);
	
	     return $this->getRecords($t3_obj,$sql);
	   } //
	public function saveMedicalLog($t3_obj)
	{
		$l_patient_id = $t3_obj->{"patient_id"};
		$p_str = $t3_obj->{"ids"};
	    $fld_arr = explode(";_:",$p_str);
	    $p_str = $t3_obj->{"sqls"};
	    $sql_array = explode(";_:",$p_str);
	    $count =count($sql_array);
		//Example SQL statement : UPDATE med_activity SET taken_6 = 'Y' WHERE id = 125
		/*
		 * Steps that need to be taken. 
		 * 1. Get patient's med start time and time_zone 
		 */
		
	}
	public function createMedicalLog($t3_obj)
	{
	     $ctrl = Controller::get();
		 $l_dt =  date('Y-m-d');
	     $l_patient_id = $t3_obj->{"patient_id"};
	     $sql = "UPDATE patient SET first_antibiotic_taken = '$l_dt'
	     		 where id = $l_patient_id";
		 $l_id = $ctrl->execute($sql);
	     $l_logtype = "medication";
	     $sql= "update user_activity  set  isreviewed = 'Y' " .
	                       " where patient_id  = " . $ctrl->MYSQLQ($l_patient_id) .
	                       "   and log_type = " . $ctrl->MYSQLQ($l_logtype) ;
	     $l_id = $ctrl->execute($sql);
	     $p_data = array();
	
	
	     $p_str = $t3_obj->{"ids"};
	     $fld_arr = explode(";_:",$p_str);
	     $p_str= $t3_obj->{"response"};
	     $resp_arr = explode(";_:",$p_str);
	     $p_str= $t3_obj->{"total_answer"};
	     $answer_arr = explode(";_:",$p_str);
	     $p_str =  $t3_obj->{"total_choice"};
	     $choice_arr = explode(";_:",$p_str);
	     $p_str = $t3_obj->{"sqls"};
	     $sql_array = explode(";_:",$p_str);
	     $count =count($sql_array);
	     for ($i=0; $i < $count; $i++) 
	     { 
	         $l_id = $ctrl->execute($sql_array[$i]);
	     }
	     $tot_ctr =count($fld_arr);
	     $l_error=0;
	     for ( $idx=0;$idx<$tot_ctr;$idx++)
	     {
	       $l_val = $fld_arr[$idx];
	       $l_resp = $resp_arr[$idx];
	       $l_choice = $choice_arr[$idx];
	       $l_answer = $answer_arr[$idx];
	       if ( $l_val > 0 ) 
	       {
	          $t3_obj->{"log_type"}="medication";
	          $t3_obj->{"response"}=$l_resp;
	          $t3_obj->{"record_id"}=$l_val;
	          $t3_obj->{"total_choice"}=$l_choice;
	          $t3_obj->{"total_answer"}=$l_answer;
	          if(strpos($l_resp,'Pain Pill Taken') !== false)
	          {
	              $sqlForAnswerGiven = "SELECT times_taken FROM med_activity WHERE sec_id= $l_val AND patient_id = $l_patient_id";
	              $times_taken = $ctrl->getRecordTEXT($sqlForAnswerGiven);
	              $sql03 = "SELECT limit_times FROM med_activity WHERE sec_id= $l_val AND patient_id = $l_patient_id";
	              $timesCanTake = $ctrl->getRecordTEXT($sql03);
	              $t3_obj->{"log_type"}="painMedication";
	              $t3_obj->{"total_choice"}=$timesCanTake;
	              $t3_obj->{"total_answer"}=$times_taken;
	             
	             
	          }
	          $t1_obj=$this->createuserlog($t3_obj);
	          if ( $t1_obj->{"status"} !=  "success" )
	             $l_error=1;
	       }
	     } // for
	      if ( $l_error  == 0 ) 
	      { 
	          $t3_obj->{"message"}= "Medication Report Saved" ;
	          $t3_obj->{"success"}= "true";
	          $t3_obj->{"status"}="success";
	      }
	      else
	      {
	          $t3_obj->{"message"}= "System error";
	          $t3_obj->{"success"}= "false";
	          $t3_obj->{"status"}="failure";
	          $message = "Medication Report Saving Error \n Patient id : $l_patient_id \n SQL : $sql";
	          $ctrl->sendError($message);
	      }
	      $this->updateUserPainPills();
	      return $t3_obj;
	} //
	public function updateUserPainPills()
	   {
	        //Get pain pills for today
	        $ctrl = Controller::get();
	        $l_dt =  date('Y-m-d H:i:s');
	        $sql="SELECT id FROM patient";
	        $patient_ids = $ctrl->getRecordIds($sql);
	        $sql = "SELECT medicine, patient_id, times_taken, limit_times FROM med_activity WHERE time1 = 25 ORDER BY user_id";
	            $pain_meds_info = $ctrl->getRecordIds($sql);
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
	         }
	   }
	public function createEmptyMedicalLog($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $l_patient_id = $t3_obj->{"patient_id"};
	     $l_logtype = "medication";
	     $sql= "update user_activity  set  isreviewed = 'Y' " .
	                       " where patient_id  = " . $ctrl->MYSQLQ($l_patient_id) .
	                       "   and log_type = " . $ctrl->MYSQLQ($l_logtype) ;
	     $l_id = $ctrl->execute($sql);
	     $p_data = array();
	     if ( $l_id == 0 ) 
	     {
	               $p_data["message"]= $ctrl->getServerError();
	               $p_data["success"]= "false";
	               return $p_data;
	     }
	
	
	     $p_str = $t3_obj->{"ids"};
	     $fld_arr = explode(";_:",$p_str);
	     $p_str= $t3_obj->{"response"};
	     $resp_arr = explode(";_:",$p_str);
	     $p_str= $t3_obj->{"total_answer"};
	     $answer_arr = explode(";_:",$p_str);
	     $p_str =  $t3_obj->{"total_choice"};
	     $choice_arr = explode(";_:",$p_str);
	     $tot_ctr =count($fld_arr);
	     $l_error=0;
	     for ( $idx=0;$idx<$tot_ctr;$idx++)
	     {
	       $l_val = $fld_arr[$idx];
	       $l_resp = $resp_arr[$idx];
	       $l_choice = $choice_arr[$idx];
	       $l_answer = $answer_arr[$idx];
	       if ( $l_val > 0 ) 
	       {
	          $t3_obj->{"log_type"}="medication";
	          $t3_obj->{"response"}=$l_resp;
	          $t3_obj->{"record_id"}=$l_val;
	          $t3_obj->{"total_choice"}=$l_choice;
	          $t3_obj->{"total_answer"}=$l_answer;
	          $t1_obj=$this->createuserlog($t3_obj);
	          if ( $t1_obj->{"status"} !=  "success" )
	             $l_error=1;
	       }
	     } // for
	      if ( $l_error  == 0 ) 
	      { 
	          $t3_obj->{"message"}= "Medication Report Saved" ;
	          $t3_obj->{"success"}= "true";
	          $t3_obj->{"status"}="success";
	      }
	      else
	      {
	                   $t3_obj->{"message"}= "System error";
	                   $t3_obj->{"success"}= "false";
	                    $t3_obj->{"status"}="failure";
						$message = "Medication Report Saving Error \n Patient id : $l_patient_id \n SQL : $sql";
	          $ctrl->sendError($message);
	      }
	      return $t3_obj;
	   } 
	public function getMedicationUserResponse($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
	     $response= $ctrl->getval($t3_obj,"response");
	     $reviewed= $ctrl->getval($t3_obj,"reviewed");
	     $sql = "select distinct b.id, ua.patient_id,c.medication medicine, ua.created_ts ,DATE_FORMAT(ua.created_ts,'%m/%d/%Y') created_ts_fmt, ua.reviewer_note , ua.reviewer_id ,ua.reviewed_ts, ua.total_choice,ua.total_answer ".
	             " from user_activity ua, medication b , medication_list c, user u ".
	             " where b.id = ua.record_id  ". 
	               " and   b.medication_id = c.id ".
	             "  and ua.patient_id = $t1_patient_id ".
	             "  and ua.total_answer > 0 ".
	             "  and ua.log_type = 'medication' " ;
	     if ( $ctrl->hasValue($response) )
	        $sql .= "  and ua.response = 'Y' ";
	
	     if ( $ctrl->hasValue($reviewed) )
	         $sql .= "  and ua.isreviewed = ".  $this->MYSQLQ($reviewed); 
	     $sql .= "  order by ua.created_ts  desc ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getcommon($t1_obj)
	  {
	    $ctrl = Controller::get();
	    $p_year = $t1_obj->{"year"};
	    $l_frdt = $t1_obj->{"frdt"};
	    $l_todt   = $t1_obj->{"todt"};
	    $l_type   = $t1_obj->{"datatype"};
	    $l_tablename   = $t1_obj->{"tablename"};
	    $l_fieldname   = $t1_obj->{"fieldname"};
	    $l_information   = $t1_obj->{"information"};
	    $l_functype   = $t1_obj->{"functype"};
	    $l_patient_id   = $t1_obj->{"patient_id"};
	    $graphvital   = $t1_obj->{"graphvital"};
	    $diagnosis   = $t1_obj->{"diagnosis"};
	    
	
	    $l_where = " date(f_date) between STR_TO_DATE('". $l_frdt . "', '%m/%d/%Y') and STR_TO_DATE('". $l_todt . "', '%m/%d/%Y') "; 
	    if ( $l_type  == "DAILY" ) 
	       $l_fldlist=" DATE_FORMAT(f_date,'%d') fldval,DATE_FORMAT(f_date,'%d/%b') fld, DATE_FORMAT(f_date,'%y%m%d') fld1";
	    if ( $l_type  == "MONTHLY" ) 
	       $l_fldlist=" DATE_FORMAT(f_date,'%m') fldval, DATE_FORMAT(f_date,'%b/%y') fld, DATE_FORMAT(f_date,'%y%m') fld1";
	    if ( $l_type  == "WEEKLY" ) 
	       $l_fldlist=" DATE_FORMAT(f_date,'%U') fldval, DATE_FORMAT(DATE_ADD(f_date, INTERVAL(1-DAYOFWEEK(f_date)) DAY),'%d/%b') fld, DATE_FORMAT(f_date,'%y%U') fld1";
	    if ( $l_type  == "QUARTERLY" )
	       $l_fldlist=" quarter(f_date) fldval, CONCAT(substring('JANAPRJULOCT',((quarter(f_date)-1)*3+1),3), DATE_FORMAT(f_date,'/%y'))  fld, DATE_FORMAT(f_date,'%y') fld1";
	
	    if ( $l_patient_id   > 0 && $l_functype != "medicalcompliance" && $l_functype != "vitals") 
	    {
	       $l_where .=   "and ( patient_id is null || patient_id =  " . $l_patient_id. ")";
	    }
	
	    $sql = " select count($l_fieldname) cnt, $l_fldlist from $l_tablename  RIGHT OUTER JOIN datemaster    ON date(created_ts) = datemaster.f_date where $l_where group by fld1, fld,fldval order by fld1 asc, fld";
	
	    if ( $l_type  == "HOURLY" ) 
	    {
	       $l_fldlist=" hourmaster.caption fld, hourmaster.fld_hour  fld1, hourmaster.fld_hour fldval";
	       $l_where = " created_ts is null or date(created_ts) between STR_TO_DATE('". $l_frdt . "', '%m/%d/%Y') and STR_TO_DATE('". $l_todt . "', '%m/%d/%Y') "; 
	        $sql = " select count($l_fieldname) cnt, $l_fldlist from $l_tablename  RIGHT OUTER JOIN hourmaster    ON hour(created_ts) = hourmaster.fld_hour where $l_where group by fld1, fld,fldval order by fldval";
	    }
	
	    if ( ( $l_functype   == "painpills" ) )
	    {
	    $sql = " select  MAX(percent1) as cnt, MAX(percent2), MAX(percent3),med1,med2,med3, $l_fldlist from $l_tablename RIGHT OUTER JOIN datemaster    ON date(created_ts) = datemaster.f_date where $l_where group by fld1, fld,fldval order by fld1 asc, fld";
	    if ( $l_type  == "HOURLY" ) 
	    {
	       $l_fldlist=" hourmaster.caption fld, hourmaster.fld_hour  fld1, hourmaster.fld_hour fldval";
	       $l_where = " created_ts is null or date(created_ts) between STR_TO_DATE('". $l_frdt . "', '%m/%d/%Y') and STR_TO_DATE('". $l_todt . "', '%m/%d/%Y') "; 
	        $sql = " select count($l_fieldname) * 100 /( select count(*) from patient)  cnt, $l_fldlist from $l_tablename  RIGHT OUTER JOIN hourmaster    ON hour(created_ts) = hourmaster.fld_hour where $l_where group by fld1, fld,fldval order by fldval";
	    }
	    }
	    
	    
	    if(($l_functype == "vitals"))
	    {
	        $vitalsArray = array();
	        $sql= "
	            select distinct
					v.vital_id,
					vl.vital,
					coalesce(vr.value_entered, 0) as value_entered,
					$l_fldlist
				from
					datemaster dm
					join vitals v
					join vitals_list vl on vl.id = v.vital_id
					left outer join
						(select
							patient_id,
							vital_id,
							DATE_FORMAT(created_ts,'%y%m%d') as created_ts,
							max(value_entered) as value_entered
						from
							vitals_reported
						group by
							patient_id,
							vital_id,
							DATE_FORMAT(created_ts,'%y%m%d')
						) as vr on vr.patient_id = v.patient_id 
						 and vr.vital_id = v.vital_id 
						 and vr.created_ts = DATE_FORMAT(dm.f_date, '%y%m%d')
				where
					v.isactive = 'Y'
					and vl.isactive = 'Y'
					and v.patient_id = $l_patient_id
					and $l_where
				order by 
					fld1,
					vital_id";
			if ( $l_type  == "HOURLY" ) 
			{
				$sql = "SELECT
						h.fld_hour as fld,
						vit.vital_id,
						coalesce(vit.value_entered, 0) as value_entered,
						vit.vital
						FROM
						hourmaster h
						left outer join (
							SELECT
								vr.patient_id,
								vr.vital_id,
								DATE_FORMAT(vr.created_ts, '%H') as created_ts,
								MAX(vr.value_entered) as value_entered,
								vl.vital
							FROM
								vitals_reported vr
								LEFT OUTER JOIN vitals_list vl on vl.id = vr.vital_id
								LEFT OUTER JOIN vitals v on v.vital_id = vr.vital_id and v.patient_id = vr.patient_id
							WHERE
								vl.isactive = 'Y'
								AND v.isactive = 'Y'
								AND vr.patient_id = $l_patient_id 
								AND DATE(vr.created_ts) = STR_TO_DATE('$l_frdt', '%m/%d/%Y') 
							GROUP BY
								vr.patient_id,
								vr.vital_id,
								DATE_FORMAT(vr.created_ts, '%H'),
								vl.vital
						) as vit on vit.created_ts = h.fld_hour";
			}
	        $list = $this->getOnlyRecords($sql);
	        $totalrows = $list->{"total_records"};
	        for($i = 0; $i < $totalrows; $i++)
	        {
	            $key = $list->{"record"}[$i];
	            $vitalsKey = $key["fld"];
	            if (!array_key_exists($vitalsKey, $vitalsArray)) 
	            {
	                $vitalsArray[$vitalsKey] = array("fld"=>$vitalsKey);
	            }
	            $vitalNameKey = $key["vital"];
	            $entered = $key["value_entered"];
	            $vitalsArray[$vitalsKey][$vitalNameKey] = $entered;
	        }
	        $allrecords = $vitalsArray;
	        
	    }
	    
	    
	
	    if ( ( $l_functype   == "medicalcompliance" ) )
	    {
	        $sql = "select
	                coalesce(uam.cnt, 0) as cnt,
	                $l_fldlist
	                from
	                datemaster dm
	                left outer join
	                    ( select
	                        patient_id,
	                        date(created_ts) as created_ts,
	                        (max(total_answer)  * 100) /(max(total_choice)) cnt
	                        from
	                        user_activity_medication
	                        where
	                        patient_id = $l_patient_id 
	                        group by
	                        patient_id,
	                        date(created_ts)
	                     ) 
	                as uam on uam.created_ts = dm.f_date
	                where
	                $l_where 
	                order by
	                fld1 asc,  
	                fld";
	    if ( $l_type  == "HOURLY" ) 
	    {
	       $l_fldlist=" hourmaster.caption fld, hourmaster.fld_hour  fld1, hourmaster.fld_hour fldval";
	       $l_where = " created_ts is null or date(created_ts) between STR_TO_DATE('". $l_frdt . "', '%m/%d/%Y') and STR_TO_DATE('". $l_todt . "', '%m/%d/%Y') "; 
	        $sql = " select count($l_fieldname) * 100 /( select count(*) from patient)  cnt, $l_fldlist from $l_tablename  RIGHT OUTER JOIN hourmaster    ON hour(created_ts) = hourmaster.fld_hour where $l_where group by fld1, fld,fldval order by fldval";
	    }
	    else if($l_type != "DAILY")
	    {
	        $sql = " select  sum(total_answer)  * 100 /sum(total_choice) cnt, $l_fldlist from $l_tablename  RIGHT OUTER JOIN datemaster    ON date(created_ts) = datemaster.f_date where $l_where group by fld1, fld,fldval order by fld1 asc, fld";
	    }
	    }
	    if ( ( $l_functype   == "medicationassessmenttaken" ) || ( $l_functype   == "symptomassessmenttaken" ) )
	    {
	    $sql = " select ((count( DISTINCT patient_id)/(select count(*) from patient where isactive = 'Y'))*100) as cnt, $l_fldlist from $l_tablename  RIGHT OUTER JOIN datemaster    ON date(created_ts) = datemaster.f_date where $l_where group by fld1, fld,fldval order by fld1 asc, fld";
	    if ( $l_type  == "HOURLY" ) 
	    {
	       $l_fldlist=" hourmaster.caption fld, hourmaster.fld_hour  fld1, hourmaster.fld_hour fldval";
	       $l_where = " created_ts is null or date(created_ts) between STR_TO_DATE('". $l_frdt . "', '%m/%d/%Y') and STR_TO_DATE('". $l_todt . "', '%m/%d/%Y') "; 
	        $sql = " select count($l_fieldname) * 100 /( select count(*) from patient)  cnt, $l_fldlist from $l_tablename  RIGHT OUTER JOIN hourmaster    ON hour(created_ts) = hourmaster.fld_hour where $l_where group by fld1, fld,fldval order by fldval";
	    }
	    }
	    if($l_functype == "patientsignin")
	    {
	        $sql = " select ((count( DISTINCT patient_id)/(select count(*) from patient where isactive = 'Y'))*100) as cnt,
	                $l_fldlist from $l_tablename  RIGHT OUTER JOIN datemaster
	                ON date(created_ts) = datemaster.f_date 
	                where $l_where group by fld1, fld,fldval order by fld1 asc, fld";
	    }
	    if($l_functype != "vitals")
	    {
	        $allrecords = $this->getRecords($t1_obj,$sql);
	        $type   = $allrecords->{"functype"};
	        $fieldtotal = $allrecords->{"total_records"};
	        $l_dataval = "";
	        for ( $idx =0; $idx <$fieldtotal; $idx++)
	        {
	            $rec=$allrecords->{"record"}[$idx];
	            $field=$rec["fld"];
	            $data=$rec["cnt"];
	        }
	    }
	    return $allrecords;
	  } // 
	public function getMedicalCompliancePercent($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t1_patient_id= $t3_obj->{"user_id"};
	     //$from= $t3_obj->{"from"};
	     //$sql = "SELECT first_antibiotic_taken from patient WHERE id = $t1_patient_id";
		 //$response = $ctrl->getRecordTEXT($sql);
	     $sql = "select sum(total_answer) * 100 / sum(total_choice) from user_activity_medication where patient_id = $t1_patient_id ";
	     $res = $ctrl->getRecordTEXT($sql);
	     $r1 = $ctrl->fmt($res);
	     /*if(($response == null || $response == ""))
		 {
		 	$r1 = "--";
		 }*/
	     return $r1;
	   }
	public function mobileauthenticate($t3_obj)
	  {
	     $ctrl = Controller::get();
		 $ip_address = $_SERVER["REMOTE_ADDR"];
	     $username = $t3_obj->{"username"};
	     $email = $t3_obj->{"email"};
	     $pa = $t3_obj->{"password"};
	     $uid = $t3_obj->{"deviceid"};
	     $t3_obj->{"status"}="None";
	     $t3_obj->{"message"}="None";
	     $_SESSION["mobile_user_id"]=$uid;
	     $r1 = $this->lb_mobilechkpwd($username,$pa,0);
	     $t3_obj->{"replydata"}=$r1;
	     if ( $r1["status"]  == "success" )
	     {
	        $t_userid = $r1["user_id"];
	        $_SESSION["mobile_patient_id"]=$t_userid;
	        $t_obj=json_decode("");
	
	        $t_obj->{"patient_id"}=$t_userid;
	        $t_obj->{"log_type"}="login";
	        $t_obj->{"record_id"}="0";
	        $t_obj->{"response"}="";
	        $this->createuserlog($t_obj);
	        $_SESSION[$uid]=$r1["user_id"];
	        $t3_obj->{"message"}="matched";
	        $t3_obj->{"uid"}=$uid;
	        $t3_obj->{"status"}="success";
	        $t3_obj->{"message"}="success";
			$fields = "ip_address, login_name, success";
			   $values =  $ctrl->MYSQLQ($ip_address) . "," .
                          $ctrl->MYSQLQ($username) . "," .
                          $ctrl->MYSQLQ("Y");
			   $sql = "insert into login_attempts ( $fields ) values ( $values )";
			   $l_resid = $ctrl->execute($sql);
	     }
	     else
	     {
	        $t3_obj->{"message"}=$r1["message"];
	        $t3_obj->{"status"}=$r1["status"];
	        $fields = "ip_address, login_name, success";
			   $values =  $ctrl->MYSQLQ($ip_address) . "," .
                          $ctrl->MYSQLQ($username) . "," .
                          $ctrl->MYSQLQ("N");
			   $sql = "insert into login_attempts ( $fields ) values ( $values )";
			   $l_resid = $ctrl->execute($sql);
	     }
	     return $t3_obj;
	  } // authenticate 
	public function lb_mobilechkpwd($p_username,$p_pwd)
	   {
	       $ctrl = Controller::get();
	    $res="";
	    $sql = "select id, first_name,last_name,username,email ,photourl, isactive, password " . 
	                     " from patient where ( upper(email) = " . $ctrl->MYSQLQ(strtoupper($p_username)) .
	                     "  or  upper(username) = " . $ctrl->MYSQLQ(strtoupper($p_username)) . ")";
	    $rec = $this->getRecord($sql);
	    $ln = count($rec);
	    if ( $ln > 0 )
	    {
	      $i=1;
	      $userid=$rec["id"];
	      $username=$rec["username"];
	      $email=$rec["email"];
	      $salt="";
	      $passwordEncrypted=$rec["password"];
	      $first_name=$rec["first_name"];
	      $last_name=$rec["last_name"];
	      $l_photourl=$rec["photourl"];
	      $l_isactive=$rec["isactive"];
		  $correct = password_verify($p_pwd, $passwordEncrypted);
            if($correct)
            {
	      		$ha = hash('sha256', $salt . $p_pwd);
	         	$res["userid"] = $userid;
	         	$res["photourl"] = $l_photourl;
	         	$res["user_id"] = $userid;
	         	$res["username"] = $username;
	         	$res["status"] = "success";
	         	$res["email"] = $email;
	         	$res["message"] = "matched";
	         	$res["first_name"] = $first_name;
	         	$res["last_name"] = $last_name;
	         	$res["isactive"] = $l_isactive;
			 }
			else 
			{
				$res["username"] = $p_username;
	         	$res["status"] = "failure";
	         	$res["message"] = "please enter valid password";
			}
	      }
	      else
	      {
	         $res["username"] = $p_username;
	         $res["status"] = "failure";
	         $res["message"] = "please enter valid password";
	      }
	   return $res;
	 }
	public function getDistinctSymptomsUserResponse($t3_obj, $date)
	   {
	     $ctrl = Controller::get();
	     $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
	     $t_response= $ctrl->getval($t3_obj,"response");
	     $t_reviewed= $ctrl->getval($t3_obj,"reviewed");
		 if($date == "today")
	     {
	         $date_l =  date('Y-m-d');
	     }   
	     else 
	     {
	         $l_date = mktime(date('H'), date('i'), 0, date('m') , date('d')-1, date('Y'));
	         $date_l =  date('Y-m-d', $l_date);
	     }
	     $sql = "select distinct symptom as 'symptom', ua.id, NULL as 'custom' 
	              from user_activity ua, symptoms_list b where b.id = ua.record_id   
	               and ua.patient_id = $t1_patient_id 
	               and ua.record_id = b.id 
	               and ua.log_type = 'symptom'
	               and DATE(ua.created_ts) = '$date_l'
	               and ua.isreviewed = 'N'
				UNION 
				select distinct b.response as 'symptom', ua.id, 'Y' as 'custom' 
	            from user_activity ua, user_non_standard_response b where b.id = ua.record_id   
	               and ua.patient_id = $t1_patient_id 
	               and ua.record_id = b.id 
	               and ua.log_type = 'customSymptom'
	               and DATE(ua.created_ts) = '$date_l'
	               and ua.isreviewed = 'N' ";
				   
	     $sql .= "  order by symptom ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getSymptomsUserResponse($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
	     $t_response= $ctrl->getval($t3_obj,"response");
	     $t_reviewed= $ctrl->getval($t3_obj,"reviewed");
	     $sql = "select b.symptom as 'symptom', ua.created_ts as 'created_ts' , ua.reviewer_note, ua.reviewer_id,ua.reviewed_ts, NULL as 'custom'
	     		   from user_activity ua, symptoms_list b where b.id = ua.record_id  
	               and ua.patient_id = $t1_patient_id   
	               and ua.record_id = b.id 
	               and ua.log_type = 'symptom' 
				UNION
				select b.response as 'symptom', ua.created_ts as 'created_ts' , ua.reviewer_note, ua.reviewer_id,ua.reviewed_ts, 'Y' as 'custom'
	     		   from user_activity ua, user_non_standard_response b where b.id = ua.record_id  
	               and ua.patient_id = $t1_patient_id   
	               and ua.record_id = b.id 
	               and ua.log_type = 'customSymptom'  
				order by created_ts  desc " ;
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getVitalsUserResponse($t3_obj)
	{
	     $ctrl = Controller::get();
	     $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
	     $t_response= $ctrl->getval($t3_obj,"response");
	     $t_reviewed= $ctrl->getval($t3_obj,"reviewed");
		 $sql = "select b.vital , ua.created_ts as 'created_ts' , ua.value_entered, 
		 		 DATE_FORMAT(ua.created_ts,'%m/%d/%Y') created_ts_fmt,
		 		 ua.reviewer_note, ua.reviewer_id,ua.reviewed_ts 
	     		 from vitals_reported ua, vitals_list b where b.id = ua.vital_id  
	               and ua.patient_id = $t1_patient_id 
	               order by ua.created_ts  desc";
	     return $this->getRecords($t3_obj,$sql);
	}
	public function getVitalColor($t3_obj)
	   {
	       /*
	        * Heart Colors :
	        * black = 0
	        * green = 1
	        * yellow = 2
	        * red = 3
	        * 
	        */
	       $ctrl = Controller::get();
	       $dx = DataModel::get();
	       $l_dt =  date('Y-m-d H:i:s');
	       $patient_id= $ctrl->getval($t3_obj,"user_id");
	       $heartColor = 1;
		   $sql = "SELECT * FROM vitals WHERE patient_id = $patient_id 
		   			AND isactive = 'Y' ";
		   $vitals = $dx->getOnlyRecords($sql);
		   $vitaltotal = $vitals->{"total_records"};
		   for ($i=0; $i < $vitaltotal; $i++) 
		   { 
			   $rec = $vitals->{"record"}[$i];
			   $record_id = $rec["id"];
			   $vital_id = $rec["vital_id"];
			   $lowred = $rec["low_alert"];
			   $lowyellow = $rec["low_warning"];
			   $highyellow = $rec["high_warning"];
			   $highred = $rec["high_alert"];
			   if($lowred == 0 && $lowyellow == 0 && $highred == 0 && $highyellow == 0)
			   {
			   		//do nothing
			   }
			   else 
			   {
			   		$sql = "SELECT value_entered FROM vitals_reported WHERE 
			   				patient_id = $patient_id AND 
			   				vital_id = $vital_id AND 
			   				isreviewed = 'N' AND 
			   				(`created_ts` > DATE_SUB('$l_dt', INTERVAL 2 DAY))";
			  		$vitalValues = $dx->getOnlyRecords($sql);
			   		$valuetotal = $vitalValues->{"total_records"};
			   		for ($j=0; $j < $valuetotal; $j++) 
			   		{ 
				   		$valueRec = $vitalValues->{"record"}[$j];
				   		$currentValue = $valueRec["value_entered"];
				   		if($heartColor == 3 ||  $heartColor == "3") //If red
				   		{
				   				$heartColor = 3; //Stay red
				   		}
				   		else if($heartColor == 2 ||  $heartColor == "2") // If Yellow
				   		{
				   				if($currentValue > $highred || $currentValue < $lowred )  // If value is in red range
				   				{
				   					$heartColor = 3; //Make heart color red
				   				}
				   		}
				   		else if($heart_color == 1 ||  $heartColor == "1") //If green
				   		{
				   				if($currentValue > $highred || $currentValue < $lowred )  // If value is in red range
				   				{
				   					$heartColor = 3; //Make heart color red
				   				}
								else if(($currentValue <= $highred && $currentValue >= $highyellow) || 
										($currentValue >= $lowred && $currentValue <= $lowyellow)) //If value is in yellow range
								{
									$heartColor = 2; //Make heart color yellow
								}
				   		}
			   		}
			   }
			   
		   }
	       
	     return $heartColor;
	     
	   }
	public function getPillColor($t3_obj)
	   {
	       /*
	        * Pill Colors :
	        * green = 0
	        * red = 1
	        * 
	        */
	       $pillColor = 0;
	       $pillColor = $this->getPainMeds($t3_obj);
	     return $pillColor;
	     
	   }
	public function getPainMeds($t3_obj)
	   {
	       $ctrl = Controller::get();
	       $l_dt =  date('Y-m-d H:i:s');
	       $pillColor = 0;
	     $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
	     $sql = "select (total_answer * 100 / total_choice) as percent 
	             from user_activity 
	             where patient_id = $t1_patient_id AND 
	             log_type = 'painMedication' AND 
	             (`created_ts` > DATE_SUB('$l_dt', INTERVAL 2 DAY))";
	     $records = $this->getRecords($t3_obj, $sql);
	     
	     $number = $records->{"total_records"};
	     for($i = 0; $i < $number; $i++)
	     {
	         $rec = $records->{"record"}[$i];
	         $lPercent = $rec["percent"];
	         if($lPercent > 100)
	         {
	             $pillColor = 1; //change pill color to red.
	         }
	     }
	     
	     
	      return $pillColor;
	   }
	public function getImages($t3_obj)
	   {
	       $ctrl = Controller::get();
	       $imageColor = 0;
	     $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
	     $sql = "select id  
	             from user_activity 
	             where patient_id = $t1_patient_id AND 
	             log_type = 'photo' AND 
	             isreviewed = 'N' ";
	     $records = $this->getOnlyRecords($sql);
	     
	     $number = $records->{"total_records"};
	     for($i = 0; $i < $number; $i++)
	     {
	         $rec = $records->{"record"}[$i];
	         $lPercent = $rec["id"];
	         $imageColor = 1;
	     }
	     
	     
	      return $imageColor;
	   }
	public function getPainMedication($day, $patientID)
	   {
	   		$yesterday = mktime(0, 0, 0, date('m') , date('d')-1, date('Y'));
	   		$theDate = date('Y-m-d', $yesterday);
	   		if($day == "today")
			{
				$theDate = date('Y-m-d');
			}
	   		$sql = "select 
	  			ua.patient_id,
	  			ua.id as main_id, 
	  			ml.medication, 
	  			ml.id as med_id, 
	  			DATE(ua.created_ts) as created_ts, 
	  			MAX(ua.total_answer / ua.total_choice) * 100 as max_percent
				from 
	  			user_activity ua
	  			left join medication m on m.id = ua.record_id
	  			left join medication_list ml on ml.id = m.medication_id
				where 
	  			ua.log_type = 'painMedication'
	  			and ua.patient_id = $patientID 
	  			and ua.response > '' 
	  			and ua.isreviewed = 'N' 
	  			and (ua.total_choice - ua.total_answer) < 0
	  			and ua.total_choice <> 0
	  			and ua.created_ts between DATE('$theDate') and DATE('$theDate')+1 
				group by
	  			patient_id,
	  			medication,
	  			date(created_ts)";
		 	$medNames = $this->getOnlyRecords($sql);
			return $medNames;
	   }
	public function getAllVitals($day, $patientID)
	   {
	   		$yesterday = mktime(0, 0, 0, date('m') , date('d')-1, date('Y'));
	   		$theDate = date('Y-m-d', $yesterday);
	   		if($day == "today")
			{
				$theDate = date('Y-m-d');
			}
	   		$sql = "select 
	  				vr.patient_id,
	  				vr.vital_id,
	  				vr.id as main_id, 
	  				vl.vital,
	  				DATE(vr.created_ts) as created_ts, 
	  				MAX(vr.value_entered) as value_entered
					from 
	  				vitals_reported vr
	  				left join vitals_list vl on vl.id = vr.vital_id
	 				where 
	  				vr.created_ts between DATE('$theDate') and DATE('$theDate')+1
	  				and patient_id = $patientID 
	  				and vr.isreviewed = 'N' 
					group by
	  				vr.patient_id,
	  				vl.vital,
	  				date(vr.created_ts)";
	  		$allVitals = $this->getOnlyRecords($sql);
			return $allVitals;
	   }
	public function getAllWounds($patientID)
	   {
	   		$sql = "SELECT ua.id as main_id, ua.patient_id, ua.created_ts, fu.file_name, fu.url   
					FROM user_activity ua, file_uploads fu 
					WHERE ua.patient_id = $patientID AND 
					ua.record_id = fu.id AND 
					ua.isreviewed = 'N' AND 
					ua.log_type = 'photo'";
	  		$allWounds = $this->getOnlyRecords($sql);
			return $allWounds;
	   }
	public function getAllWoundResponses($patientID)
	   {
	   		$sql = "SELECT ua.id as main_id, ua.patient_id, ua.created_ts, fu.file_name, ua.reviewer_id, ua.reviewed_ts, ua.reviewer_note  
					FROM user_activity ua, file_uploads fu 
					WHERE ua.patient_id = $patientID AND 
					ua.record_id = fu.id AND 
					ua.log_type = 'photo'";
	  		$allWounds = $this->getOnlyRecords($sql);
			return $allWounds;
	   }
	public function getWoundImages($t1_obj)
	   {
	     $ctrl = Controller::get();
	     $t_patient_id= $t1_obj->{"patient_id"};
	     $t_frdt= $t1_obj->{"fromdt"};
	     $t_todt= $t1_obj->{"todt"};
	     $sql = "select p.id,p.first_name, p.last_name, f.url, f.file_name,  DATE_FORMAT(f.created_ts,'%m/%d/%y %h:%i %p') created_ts_fmt from file_uploads f, patient p".
	             " where f.user_id = p.id ";
	     if ( $t_patient_id > 0 ) 
	             $sql .= " and  f.user_id = $t_patient_id ";
	     //if ( $ctrl->hasValue($t_frdt ) )
	          //$sql .= " and date(f.created_ts) between STR_TO_DATE('". $t_frdt . "', '%m/%d/%Y') and STR_TO_DATE('". $t_todt . "', '%m/%d/%Y') "; 
	
	     $sql .= "  order by f.created_ts  desc ";
	     return $this->getRecords($t1_obj,$sql);
	   } // 
	public function getWoundImages_1($t1_obj)
	   {
	     $ctrl = Controller::get();
	     $t_patient_id= $ctrl->getval($t1_obj,"patient_id");
	     $t_frdt= $ctrl->getval($t1_obj,"fromdt");
	     $t_todt= $ctrl->getval($t1_obj,"todt");
	     $t_rows= $ctrl->getval($t1_obj,"rows_limit");
	     $t_cur_page= $ctrl->getval($t1_obj,"pageno");
	     $t_next= $ctrl->getval($t1_obj,"next");
	
	
	     $l_where=" 1=1 ";
	     if ( $t_patient_id > 0 ) 
	             $l_where  .= " and  f.user_id = $t_patient_id ";
	     if ( $ctrl->hasValue($t_frdt ) )
	          $l_where  .= " and date(f.created_ts) between STR_TO_DATE('". $t_frdt . "', '%m/%d/%Y') and STR_TO_DATE('". $t_todt . "', '%m/%d/%Y') "; 
	
	     $sql = "select count(*) from file_uploads f, patient p  where f.user_id = p.id  and $l_where ";
	
	     $l_tot_rows = $ctrl->getRecordField($sql);
	     $t1_obj->{"TOTALROWS"}=$l_tot_rows;
	
	     $l_end_page=$l_tot_rows / $t_rows;
	     if ( $l_end_page > intval($l_end_page) ) 
	         $l_end_page = intval($l_end_page)  + 1;
	
	
	     if ( $t_next == "start" ) 
	        $t_cur_page = 1; 
	     if ( $t_next == "end" ) 
	        $t_cur_page = $l_end_page; 
	     if ( $t_next == "prev" ) 
	        $t_cur_page = $t_cur_page - 1 ; 
	     if ( $t_next == "next" ) 
	        $t_cur_page = $t_cur_page + 1 ; 
	     if (  $t_cur_page > $l_end_page )
	        $t_cur_page = $l_end_page ;
	     if (  $t_cur_page < 1 )
	        $t_cur_page = 1;
	     $l_start_row = ( $t_cur_page  - 1) * $t_rows;
	     $l_end_row =  $l_start_row + $t_rows;
	     if ( $l_end_row >   $l_tot_rows )
	        $l_end_row  = $l_tot_rows ;
	
	     $t1_obj->{"cur_page"}=$t_cur_page;
	     $t1_obj->{"end_page"}=$l_end_page;
	
	
	     $sql = "select p.id,p.first_name, p.last_name, f.url, 
	       ua.reviewer_id, 
	       DATE_FORMAT(ua.reviewed_ts,'%m/%d/%y %h:%i %p') reviewed, ua.reviewer_note,
	       DATE_FORMAT(f.created_ts,'%m/%d/%y %h:%i %p') created_ts_fmt
	       from file_uploads f, patient p, user_activity ua  
	       where f.user_id = p.id and ua.record_id = f.id AND 
					ua.log_type = 'photo'  and $l_where ";
	
	     $sql .= "  order by f.created_ts  desc ";
	     $sql .= "  LIMIT $l_start_row , $t_rows ";
	     return $this->getRecords($t1_obj,$sql);
	   } // 
	public function getSymptomsList($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from symptoms_list where " .
	             "  id > 1 ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "AND isactive = " . $ctrl->MYSQLQ($l_isactive);
	     $sql .=  "order by  symptom ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getVideo_list($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from videos_list ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     $sql .=  "order by  video ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getVideosUserResponse($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
	     $sql = "select b.video , ua.created_ts ,DATE_FORMAT(ua.created_ts,'%m/%d/%Y') created_ts_fmt, ua.response ".
	             " from user_activity ua, videos  b where b.video_id = ua.record_id  ". 
	             "  and ua.patient_id = $t1_patient_id ".
	             "  and ua.record_id = b.id ".
	             "  and ua.log_type = 'video' " ;
	     $sql .= "  order by ua.created_ts  desc ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getVitals($t3_obj)
	   {
	       $ctrl = Controller::get();
	       $t1_patient_id= $ctrl->getval($t3_obj,"user_id");
		   $sql = "SELECT vl.* FROM vitals_list vl, vitals v 
		   		   WHERE vl.id = v.vital_id AND v.patient_id = $t1_patient_id
		   		   AND v.isactive = 'Y'";
	       return $this->getRecords($t3_obj, $sql);
	   }
	public function isAlive($t3_obj)
	   {
	        $t3_obj->{"status"}="success";
	        $t3_obj->{"message"}="ok";
	        return $t3_obj;
	  } // forgotpassword
	public function getDietList($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from diet_list ";
	     $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     $sql .=  "order by  diet";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getMedicationList($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from medication_list  ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     $sql .=  "order by  category,medication";
	     return $this->getOnlyRecords($sql);
	   } // 
	public function getMedicationClasses($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from medications_class ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
		 $records = $this->getOnlyRecords($sql);
	     return $records;
	   }
	public function getDiagnosisList($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from diagnosis_list ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     $sql .=  "order by  diagnosis";
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getPhyactList($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from physical_activity_list ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     $sql .=  " order by  physical_activity";
		 $results = $this->getRecords($t3_obj,$sql);
	     return $results;
	   } // 
	public function getWoundList($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $sql = "select * from wound_care_list ";
		 $l_isactive= $ctrl->getval($t3_obj,"isactive");
	     if ( $l_isactive  ) 
	         $sql .= "WHERE isactive = " . $ctrl->MYSQLQ($l_isactive);
	     $sql .=  "order by description";
	     return $this->getRecords($t3_obj,$sql);
	   }
	public function getPushNotificationlog($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $nowFormat = gmdate('Y-m-d H:i:s');
	     $t1_patient_id= $ctrl->getval($t3_obj,"patient_id");
	     $t1_isrreviewed = $ctrl->getval($t3_obj,"isrreviewed ");
	     $sql = "select DATE_FORMAT(l.created_ts,'%m/%d/%Y  %h:%i') created_ts_fmt, l.*, p.first_name, p.last_name  from patient p, push_notification l 
	              where l.patient_id = p.id ";
	     if ( $t1_patient_id > 0 ) 
	     $sql .= " and l.patient_id = ". $this->MYSQLQ($t1_patient_id); 
	     if ( $this->hasValue($t1_isrreviewed) )
	     $sql .= " and l.isreviewed = ". $this->MYSQLQ($l_isreviewed); 
	     $sql .= " and date(l.created_ts) > DATE_ADD('$nowFormat' , INTERVAL -10 DAY) ";
	     $sql .= "  order by l.id desc ";
	     return $this->getRecords($t3_obj,$sql);
	   } // 
	public function getmobilehomepagealert($t3_obj)
	   {
	     $ctrl = Controller::get();
	     $nowFormat = date('Y-m-d H:i:s');
	     $t1_patient_id= $ctrl->getval($t3_obj,"patient_id");
	     $t1_isrreviewed = $ctrl->getval($t3_obj,"isrreviewed ");
	     $sql = "select DATE_FORMAT(l.created_ts,'%m/%d/%y  %h:%i') created_ts_fmt, l.*, p.first_name, p.last_name from patient p, push_notification l 
	              where l.patient_id = p.id ";
	     if ( $t1_patient_id > 0 ) 
	     $sql .= " and l.patient_id = ". $this->MYSQLQ($t1_patient_id); 
	     if ( $this->hasValue($t1_isrreviewed) )
	     $sql .= " and l.isreviewed = ". $this->MYSQLQ($l_isreviewed); 
	     $sql .= " and date(l.created_ts) > DATE_ADD('$nowFormat' , INTERVAL -1 DAY) ";
	     $sql .= "  order by l.id desc ";
	
	     return $this->getRecords($t3_obj,$sql);
	   } //
	public function findRedFlags($patientID)
	   {
	   		$redflags = array();
	   		$data='{"user_id":"' . $patientID . '"}';
	    	$p_obj=json_decode($data);
			
			$heartColor = $this->getVitalColor($p_obj);
			if($heartColor == 3) //if vital is red.
				$redflags["vitals"] = 1;
			else
				$redflags["vitals"] = 0;
			
			$pillColor = $this->getPainMeds($p_obj);
			if($pillColor == 1) //if pill color is red.
				$redflags["pills"] = 1;
			else 
				$redflags["pills"] = 0;
				
			$wound = $this->getImages($p_obj);
			if($wound == 1)
				$redflags["wound"] = 1;
			else 
				$redflags["wound"] = 0;
			
			//$p_obj->{"from"} = "flags";
			$medPercent = $this->getMedicalCompliancePercent($p_obj);
			if($medPercent == "--")
				$redflags["meds"] = 0;
			else if($medPercent < 80) //if percent is red.
				$redflags["meds"] = 1;
			else 
				$redflags["meds"] = 0;
			
			
		 	 $r1_obj = $this->getDistinctSymptomsUserResponse($p_obj, "today");
	         $t_sym = $r1_obj->{"total_records"};
	         $l_symp_res="";
	         $symptomArray = array();
	         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
	         {
	            $s_rec=$r1_obj->{"record"}[$s_idx];
	            $l_symptoms  =  $s_rec["symptom"];
				
				if($l_symptoms != "No Symptoms")
	            	$symptomArray[] = $l_symptoms;
	         }
	         if(empty( $symptomArray[0] ))
	         	$redflags["symptomToday"] = 0;
			 else
			 	$redflags["symptomToday"] = 1;
			 
			 $r1_obj = $this->getDistinctSymptomsUserResponse($p_obj, "yesterday");
	         $t_sym = $r1_obj->{"total_records"};
	         $l_symp_res="";
	         $symptomArray = array();
	         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
	         {
	            $s_rec=$r1_obj->{"record"}[$s_idx];
	            $l_symptoms  =  $s_rec["symptom"];
				if($l_symptoms != "No Symptoms")
	            	$symptomArray[] = $l_symptoms;
	         }
	         if(empty( $symptomArray[0] ))
	         	$redflags["symptomYesterday"] = 0;
			 else
			 	$redflags["symptomYesterday"] = 1;
			
			return $redflags;
	   }
	public function make_thumb($src, $dest, $type) 
   {
		/* read the source image */
		if($type == "jpeg" || $type == "jpg")
		{
			$source_image = imagecreatefromjpeg($src);
   		}
		else
		{
			$source_image = imagecreatefrompng($src);
		}
		$width = imagesx($source_image);
		$height = imagesy($source_image);
	
		/* find the "desired height" of this thumbnail, relative to the desired width  */
		$desired_height = floor($height * (60 / $width));
	
		/* create a new, "virtual" image */
		$virtual_image = imagecreatetruecolor(60, $desired_height);
	
		/* copy source image at a resized size */
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, 60, $desired_height, $width, $height);
	
		/* create the physical thumbnail image to its destination */
		if($type == "jpeg")
		{
			imagejpeg($virtual_image, $dest);
		}
		else if($type == "png")
		{
			imagepng($virtual_image, $dest);
		}
		else
		{
			$l_dt =  date('Y-m-d H:i:s');
			$this->logme("Unknown file type make sure to use jpeg and png only. $l_dt");
		}
	} 
	public function getZone($t3_obj)
	{
		$ctrl = Controller::get();
	    $patient_id= $t3_obj->{"user_id"};
		$sql = "SELECT time_zone FROM patient WHERE id= $patient_id";
		$patient_tz = $ctrl->getRecordTEXT($sql);
		$sql = "SELECT GMT, name, 
				IF(GMT = '$patient_tz', 'Y', 'N') AS active 
				FROM timezones ";
		$results = $this->getRecords($t3_obj, $sql);
		return $results;
	}
	public function saveZone($t3_obj)
	{
		$ctrl = Controller::get();
	    $patient_id= $t3_obj->{"patient_id"};
		$gmt= $t3_obj->{"gmt"};
		$fieldlists =   " time_zone  =  " . $ctrl->MYSQLQ($gmt);
		$sql = "UPDATE patient set  " . $fieldlists . 
                               " where id = $patient_id ";
		$l_resid = $ctrl->execute($sql);
	         if ( $l_resid == 1 ) 
	         { 
	                    $t3_obj->{"message"}= "Updated time zone" ;
	                    $t3_obj->{"success"}= "true";
	                    $t3_obj->{"status"}="success";
	         }
	         else
	         {
	                   $t3_obj->{"message"}= $ctrl->getServerError();
	                   $t3_obj->{"success"}= "false";
	                    $t3_obj->{"status"}="failure";
	                    $message = "Zone Saving Error \n Patient id : $patient_id \n SQL : $sql";
	          $ctrl->sendError($message);
	         }
		return $t3_obj;
	}
} 

