<?php
class UserModel extends Base
{
	public function __construct()
   {
   }
	static $st_instance = null;
	public static function get()
   {
      if ( UserModel::$st_instance == null  )
         UserModel::$st_instance = new UserModel();
      return UserModel::$st_instance;
   }
	public function chkduplicateemail()
   {
         $ctrl = Controller::get();
         $p_data = array();
         $email=$_POST["email"];
         $l_res=false;
         $sql = "select COALESCE(max(id) ,0) from user where email = " . $this->MYSQLQ($email);
         $l_id = $ctrl->getRecordID($sql);
         if ( $l_id == 0 )
         {
            $p_data["message"]= "Good one";
            $p_data["success"]= "true";
         }
         else
         {
            $p_data["message"]= "Email Address already Registered";
            $p_data["success"]= "false";
         }
         return $p_data;
    }
	public function authenticate()
   {
         $ctrl = Controller::get();
		 $ip_address = $_SERVER["REMOTE_ADDR"];
         $p_data = array();
         $email=$_POST["login_email"];
         $pwd=$_POST["login_password"];
         $l_res=false;
         $sql = "select id, sid,first_name, user_type, password from user " .
             " where ( upper(email) = " . $ctrl->MYSQLQ(strtoupper($email)) .
                      " or upper(username) = " . $ctrl->MYSQLQ(strtoupper($email)) . ")";
         $l_record  = $ctrl->getRecord($sql);
         if ( count($l_record) > 0  && $l_record[0]  > 0 )
         {
            $l_id=$l_record[0];
            $l_sid=$l_record[1];
            $first_name=$l_record[2];
            $userType=$l_record[3];
			$passwordEncrypted=$l_record[4];
			$correct = password_verify($pwd, $passwordEncrypted);
            if($correct)
            {
               $p_data["sid"]= $l_sid;
               $p_data["message"]= "good one ";
               $p_data["success"]= "true";
			   $fields = "ip_address, login_name, success";
			   $values =  $ctrl->MYSQLQ($ip_address) . "," .
                          $ctrl->MYSQLQ($email) . "," .
                          $ctrl->MYSQLQ("Y");
			   $sql = "insert into login_attempts ( $fields ) values ( $values )";
			   $l_resid = $ctrl->execute($sql);
			   $fields = "user_id";
			   $values =  $l_id;
			   $sql = "insert into portal_logins ( $fields ) values ( $values )";
			   $l_returnedvalue = $ctrl->execute($sql);

            }
			else
			{
				$p_data["message"]= "Please check the credentials";
            	$p_data["success"]= "false";
				$fields = "ip_address, login_name, success";
			   $values =  $ctrl->MYSQLQ($ip_address) . "," .
                          $ctrl->MYSQLQ($email) . "," .
                          $ctrl->MYSQLQ("N");
			   $sql = "insert into login_attempts ( $fields ) values ( $values )";
			   $l_resid = $ctrl->execute($sql);

			}
         }
         else
         {
            $p_data["message"]= "Please check the credentials";
            $p_data["success"]= "false";
			$fields = "ip_address, login_name, success";
			   $values =  $ctrl->MYSQLQ($ip_address) . "," .
                          $ctrl->MYSQLQ($email) . "," .
                          $ctrl->MYSQLQ("N");
			   $sql = "insert into login_attempts ( $fields ) values ( $values )";
			   $l_resid = $ctrl->execute($sql);
         }
         return $p_data;
   }
	public function forgotpassword()
   {
         $email=$_POST["email"];
         return $this->forgotpassword_call($email);
   }
	public function forgotpassword_call($email)
   {
         $ctrl = Controller::get();
         $p_data = array();
         $l_res=false;
         $sql = "select id, password ,first_name  from patient " .
                " where ( email = " . $ctrl->MYSQLQ($email)  .
                "    or username = " . $ctrl->MYSQLQ($email) . ")";
         $l_record  = $ctrl->getRecord($sql);
         if ( $l_record && count($l_record) > 0  && $l_record[0]  > 0 )
         {
            $pwd=$l_record[1];
            $nm=$l_record[2];
            $p_data["message"]= "good one ";
            $p_data["success"]= "true";

            // send email
            $dt=array();
            $dt["to"] = $email;
            $dt["subject"] = "You requested a password " ;
            $dlim="\r\n\r\n";
            $l_url="http://" . $_SERVER['SERVER_NAME'];
            $dt["message"] = "Dear $nm, " . $dlim.  $dlim.
                       "You recently asked your password. ". $dlim .
                       "here is your password  ". $dlim .
                       "$pwd  ". $dlim .
                       "Didn't request this change? ". $dlim .
                       "If you didn't request a new password, let us know immediately. ". $dlim .
                       $l_url . $dlim .
                       "Surgical Support Team"  . $dlim;

            $ctrl->sendMail($dt);
            $p_data["message"]= "Email sent ...Please check your email ";
            $p_data["success"]= "true";
         }
         else
         {
            $p_data["message"]= "server error";
            $p_data["success"]= "false";
         }
         return $p_data;
  } // forgotpassword
	public function getUserRecord($p_user_id)
  {
         $ctrl = Controller::get();
         $sql = "select *  from user where id = " . $ctrl->MYSQLQ($p_user_id);
         return $ctrl->getRecord($sql);
  }
	public function getUser()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $sql = "select *  from user where id = " . $ctrl->MYSQLQ($l_user_id);
         return $ctrl->getRecord($sql);
  }
	public function saveProfile()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $l_field_count=$_POST["profile_field_count"];
         $l_user_id=$_POST["profile_user_id"];
         $l_pid=$_POST["profile_pid"];

         $fldval="";
         $fldame="";
         $dlim="";

         for  ( $f_idx = 1;$f_idx<=$l_field_count;$f_idx++)
         {
               $l_fld = "field_" . ( $f_idx);
               $l_nm = "profile_" . $l_fld;
               $l_fld = "field_" . ( $f_idx);
               $l_val = $_POST[$l_nm];
               $l_fldval .= $dlim . $this->buildwithpost($l_fld,$l_nm);
               $dlim = ",";
         }

         $sql= "update hl7datatable  set  " . $l_fldval .
                       " where segment_id  = " . $ctrl->MYSQLQ($l_pid) .
                       "   and user_id  = " . $ctrl->MYSQLQ($l_user_id);
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 )
         {
                $p_data["message"]= "Saved" ;
                $p_data["success"]= "true";
         }
         else
         {
               $p_data["message"]= $ctrl->getServerError();
               $p_data["success"]= "false";
			   $message = "Profile Saving Error \n Patient id : $l_user_id \n SQL : $sql";
	          $ctrl->sendError($message);
         }

         return $p_data;
    }  //
	public function getAllUsers_where($p_where)
    {
         $ctrl = Controller::get();
           $l_user_id= $ctrl->getUserID();
           $sql = "select *  from user $p_where order by last_name";
           return $ctrl->query($sql);
    }
	public function getAllUsers()
    {
         $ctrl = Controller::get();
           $l_user_id= $ctrl->getUserID();
           $sql = "select *  from user where user_type = 1";
           return $ctrl->query($sql);
    }
	public function getPhotoURL($flname,$isPatient, $userID, $isNew)
  {
      $ctrl = Controller::get();
      $const = ConstantModel::get();
      $url = "no";
      if ( $ctrl->hasValue($flname) )
      {
           $url = "/uploads/" . $userID ."/". $flname;
		   if($isPatient)
		   {
		   		$url = "/uploads/" . $userID ."p/". $flname;
		   }
      }
      else if($isNew)
      {
           $url = "/assets/images/photo.png";
      }
      return $url;
  }
	public function isduplicateusername($p_user_id,$p_username)
   {
         $ctrl = Controller::get();
         $sql = "select COALESCE(max(id) ,0) from patient where username = " . $ctrl->MYSQLQ($p_username);
         if ( $p_user_id > 0 )
                $sql .= " and id != $p_user_id " ;
         $t_key = $ctrl->getRecordID($sql);
         if ( $t_key  > 0 )
            return 1 ;
         else
            return 0 ;
    }
	public function getPatient($p_patient_id)
    {
           $ctrl = Controller::get();
           $sql = "select *, DATE_FORMAT(date_of_discharge,'%m/%d/%Y') date_of_discharge_fmt ,DATE_FORMAT(created_ts,'%m/%d/%Y') created_ts_fmt from patient where id = " . $ctrl->MYSQLQ($p_patient_id);
           return $ctrl->getRecord($sql);

    }
	public function getDiagnosis($id)
    {
           $ctrl = Controller::get();
           $sql = "select diagnosis from diagnosis_list where id = " . $ctrl->MYSQLQ($id);
           return $ctrl->getRecordTEXT($sql);

    }
	public function resetPassword()
	{
		$ctrl = Controller::get();
		$p_data = array();
        $tm =  date('Y-m-d H:i:s');
        $patientID = $_POST["login_patient_id"];
        $currentPassword = $_POST["login_old"];
		$newPassword = $_POST["login_new"];
		$confirmPassword = $_POST["login_confirm"];
		if($newPassword == $confirmPassword)
		{
			$sql = "SELECT password FROM patient WHERE id= $patientID";
			$currentHashedPassword = $ctrl->getRecordTEXT($sql);
			$correct = password_verify($currentPassword, $currentHashedPassword);
			if($correct)
			{
				$passwordhash = password_hash($newPassword, PASSWORD_DEFAULT);
				$fieldlists =   " password  =  " . $ctrl->Q($passwordhash) ;
				$sql= "update patient set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($patientID) ;
			 	$l_resid = $ctrl->execute($sql);
             	if ( $l_resid == 1 )
             	{
             		$p_data["message"]= "Password Successfully Saved. You can now use your new password in the MobiMD app.";
            		$p_data["success"]= "true";
             	}
				else
				{
					$p_data["message"]= "Could not update password. Please contact support.";
            		$p_data["success"]= "false";
				}
			}
			else
			{
				$p_data["message"]= "Old password does not match records";
            	$p_data["success"]= "false";
			}
		}
		else
		{
			$p_data["message"]= "New passwords do not match";
            $p_data["success"]= "false";
		}
		return $p_data;
	}
	public function savePatient()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_id = $_POST["record_id"];
         $l_first_name = $_POST["record_first_name"];
         $l_last_name = $_POST["record_last_name"];
         $l_email = $_POST["record_email"];
         $l_username = $_POST["record_username"];
         $l_phone = $_POST["record_phone"];
         $l_phone_carrier = $_POST["record_phone_carrier"];
         $l_reason = $_POST["record_reason"];
         $l_password = $_POST["record_password"];
         $l_phone = $_POST["record_phone"];
         $l_dial_code = $_POST["record_dial_code"];
        //$l_isactive = $_POST["record_isactive"];
				//$l_ignore = $_POST["record_ignore"];
         $l_doctor_id = $_POST["record_doctor_id"];
         $l_clinic_id = $_POST["record_clinic_id"];

         $l_discharge_diagnosis_id = $ctrl->getPostParamValue("record_discharge_diagnosis");
         $l_surgical_procedure = $ctrl->getPostParamValue("record_surgical_procedure");
         $l_medical_record_url = $ctrl->getPostParamValue("record_medical_record_url");

      //   if ( $l_isactive == "false" )
        //    $l_isactive  = "N";
      //   else
        //    $l_isactive  = "Y";

	//	 if ( $l_ignore == "false" )
    //        $l_ignore  = "N";
  //       else
    //        $l_ignore  = "Y";

         $l_isdup = $this->isduplicateusername($l_id,$l_username);

         if( $l_isdup   == 1 )
         {
           $p_data["message"]= "Duplicate username";
           $p_data["success"]= "false";
           return $p_data;
         }



         if ( $l_id > 0 )
         {
             $fieldlists =   " first_name  =  " . $ctrl->MYSQLQ($l_first_name) . "," .
                             " last_name  =  " . $ctrl->MYSQLQ($l_last_name) . "," .
                             " username  =  " . $ctrl->MYSQLQ($l_username) . "," .
                             " doctor_id  =  " . $ctrl->MYSQLQ($l_doctor_id) . "," .
                             " clinic_id  =  " . $ctrl->MYSQLQ($l_clinic_id) . "," .
                             " email  =  " . $ctrl->MYSQLQ($l_email) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) . "," .
                             " phone  =  " . $ctrl->MYSQLQ($l_phone) . "," .
                             " dial_code  =  " . $ctrl->MYSQLQ($l_dial_code) . "," .
                             " discharge_diagnosis  =  " . $ctrl->MYSQLQ($l_discharge_diagnosis_id) . "," .
                             " surgical_procedure  =  " . $ctrl->MYSQLQ($l_surgical_procedure) . "," .
                             " medical_record_url  =  " . $ctrl->MYSQLQ($l_medical_record_url) . "," .
                             // " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             // " ignore_execptions  =  " . $ctrl->MYSQLQ($l_ignore) . "," .
                             " phone_carrier  =  " . $ctrl->MYSQLQ($l_phone_carrier) ."," .
                             " reason  =  " . $ctrl->MYSQLQ($l_reason) ;

         $l_date = $ctrl->getPostParamValue("record_date_of_discharge");
         if (  $this->hasValue($l_date) )
         {
             $lar  = explode("/",$l_date);
             $l_dt  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
             $fieldlists .=   ",date_of_discharge  =  " . $ctrl->MYSQLQ($l_dt);
         }
         $l_flname = $_POST["record_copied_file"];
         $l_url = $this->getPhotoURL($l_flname, TRUE, $l_id, FALSE);
         if ( $l_url != "no" )
         {
             $fieldlists .=   ",photourl  =  " . $ctrl->MYSQLQ($l_url);
         }

             if ( $l_password != null && $l_password != "" ){
             	$l_password = password_hash($l_password, PASSWORD_DEFAULT);
                 $fieldlists .=   " ,password =  " . $ctrl->Q($l_password);
			 }

             $sql= "update patient set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= "error is here"; //$ctrl->getServerError();
                   $p_data["success"]= "false";
                   $message = "Patient Saving Error \n Patient id : $l_id \n SQL : $sql";
	          $ctrl->sendError($message);
             }
         }
         else
         {
         $l_password = password_hash($l_password, PASSWORD_DEFAULT);
         $l_uniqueid = $_POST["record_uniqueid"];
         $l_flname = $_POST["record_copied_file"];
         $l_url = $this->getPhotoURL($l_flname,TRUE, $l_uniqueid, TRUE);
// ignore_execptions,
             $fields =   " first_name,last_name,discharge_diagnosis,  surgical_procedure  ,medical_record_url,  clinic_id,doctor_id,phone,dial_code,phone_carrier,reason,email,username,password,photourl,created_ts,created_user_id,updated_ts,updated_user_id";

             $values =    $ctrl->MYSQLQ($l_first_name) . "," .
                          $ctrl->MYSQLQ($l_last_name) . "," .
                          $ctrl->MYSQLQ($l_discharge_diagnosis_id) . "," .
                          // $ctrl->MYSQLQ("N") . "," .
                          $ctrl->MYSQLQ($l_surgical_procedure) . "," .
                          $ctrl->MYSQLQ($l_medical_record_url) . "," .
                          $ctrl->MYSQLQ($l_clinic_id) . "," .
                          $ctrl->MYSQLQ($l_doctor_id) . "," .
                          $ctrl->MYSQLQ($l_phone) . "," .
                          $ctrl->MYSQLQ($l_dial_code) . "," .
                          $ctrl->MYSQLQ($l_phone_carrier) . "," .
                          $ctrl->MYSQLQ($l_reason) . "," .
                          $ctrl->MYSQLQ($l_email) . "," .
                          $ctrl->MYSQLQ($l_username) . "," .
                          $ctrl->Q($l_password) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);
             $l_date = $ctrl->getPostParamValue("record_date_of_discharge");
             if (  $this->hasValue($l_date) )
             {
                 $lar  = explode("/",$l_date);
                 $l_dt  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
                 $fields  .=   ",date_of_discharge";
                 $values  .=  ", " . $ctrl->MYSQLQ($l_dt);
             }

             $sql= "insert into patient ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid  > 0  )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from patient where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and email =" . $ctrl->MYSQLQ($l_email);
                        $l_record_id = $ctrl->getRecordID($sql);

         $l_newrecord_ctr=0;
         $p_field_array = array();

                        $t_obj=json_decode("{}");
                        $t_obj->{"fld_name"} = "record_id";
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
                        $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
                        $p_data["INSERTED_DATA"]=$p_field_array;
             }
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $l_resid; //"check here"; //$ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Patient Saving Error \n User id : $l_user_id \n SQL : $sql";
	          $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function getMedication($p_patient_id)
    {
           $ctrl = Controller::get();
           $sql = "select *  from patient where id = " . $ctrl->MYSQLQ($p_patient_id);
           return $ctrl->getRecord($sql);

    }
	public function saveClinic()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_id = $_POST["record_id"];
         $l_name = $_POST["record_first_name"];
         $l_username = $_POST["record_username"];
         $l_password = $_POST["record_password"];
         $l_address1 = $_POST["record_address1"];
         $l_address2 = $_POST["record_address2"];
         $l_city = $_POST["record_city"];
         $l_state = $_POST["record_state"];
         $l_zipcode = $_POST["record_zipcode"];
         $l_email = $_POST["record_email"];
         $l_phone = $_POST["record_phone"];
		 $l_dial_code = $_POST["record_dial_code"];
         $l_isactive = $_POST["record_isactive"];
		 $l_tz = $_POST["record_time_zone"];
		 $l_dst = $_POST["record_daylight"];
         $l_operation_hours = $_POST["record_operation_hours"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

		 if ( $l_dst == "true")
            $l_dst = "Y";
         else
            $l_dst = "N";

         $t_sql="select id from user " .
           " where upper(first_name) =  " . $ctrl->MYSQLQ($l_name) .
           " and id != " . $ctrl->MYSQLQ($l_id) ;
         $res_id=$ctrl->getRecordID($t_sql);
         if ( $res_id > 0 )
         {
           $p_data["message"]= "Duplicate Clinic Name...please enter valid one";
           $p_data["success"]= "false";
           return $p_data;
         }

         if ( $l_id > 0 )
         {
             $fieldlists =   " first_name  =  " . $ctrl->MYSQLQ($l_name) . "," .
                             " user_type  =  " . $ctrl->MYSQLQ(2) . "," .
                             " username  =  " . $ctrl->MYSQLQ($l_username) . "," .
                             " address1  =  " . $ctrl->MYSQLQ($l_address1) . "," .
                             " address2  =  " . $ctrl->MYSQLQ($l_address2) . "," .
                             " city  =  " . $ctrl->MYSQLQ($l_city) . "," .
                             " operation_hours  =  " . $ctrl->MYSQLQ($l_operation_hours) . "," .
                             " state  =  " . $ctrl->MYSQLQ($l_state) . "," .
                             " zipcode  =  " . $ctrl->MYSQLQ($l_zipcode) . "," .
                             " email  =  " . $ctrl->MYSQLQ($l_email) . "," .
                             " phone  =  " . $ctrl->MYSQLQ($l_phone) . "," .
                             " dial_code  =  " . $ctrl->MYSQLQ($l_dial_code) . "," .
                             " time_zone  =  " . $ctrl->MYSQLQ($l_tz) . "," .
                             " daylight_savings_time  =  " . $ctrl->MYSQLQ($l_dst) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive);

             $l_flname = $_POST["record_copied_file"];
            $l_url = $this->getPhotoURL($l_flname,FALSE, $l_id, FALSE);
            if ( $l_url != "no" )
            {
	           $fieldlists .=   ",photourl  =  " . $ctrl->MYSQLQ($l_url);
            }
			if ( $l_password != null && $l_password != "" )
			{
             	$l_password = password_hash($l_password, PASSWORD_DEFAULT);
                 $fieldlists .=   " ,password =  " . $ctrl->Q($l_password);
			}
             $sql= "update user set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
                   $message = "Clinic Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
         	$l_password = password_hash($l_password, PASSWORD_DEFAULT);
         $l_uniqueid = $_POST["record_uniqueid"];
         $l_flname = $_POST["record_copied_file"];
         $l_url = $this->getPhotoURL($l_flname,FALSE, $l_uniqueid, TRUE);
             $l_sid = $ctrl->createUniqueID();
             $fields =   " first_name,user_type,created_user_id, updated_user_id ,time_zone,daylight_savings_time,
             				sid,username,password,photourl,operation_hours,isactive,
             				address1,address2,city,state,zipcode,email,phone,dial_code, created_ts";
             $values =    $ctrl->MYSQLQ($l_name) . "," .
                          $ctrl->MYSQLQ(2) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_tz) . "," .
                          $ctrl->MYSQLQ($l_dst) . "," .
                          $ctrl->MYSQLQ($l_sid) . "," .
                          $ctrl->MYSQLQ($l_username) . "," .
                          $ctrl->Q($l_password) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($l_operation_hours) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_address1) . "," .
                          $ctrl->MYSQLQ($l_address2) . "," .
                          $ctrl->MYSQLQ($l_city) . "," .
                          $ctrl->MYSQLQ($l_state) . "," .
                          $ctrl->MYSQLQ($l_zipcode) . "," .
                          $ctrl->MYSQLQ($l_email) . "," .
                          $ctrl->MYSQLQ($l_phone) . "," .
                          $ctrl->MYSQLQ($l_dial_code) . "," .
                          $ctrl->MYSQLQ($tm);

             $sql= "insert into user ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);

             if ( $l_resid == 1 )
             {
                 $sql = "SELECT id FROM user WHERE first_name = '$l_name'";
                 $l_id = $ctrl->getRecordID($sql);
                 $allDoctors = $this->getAllFromUserType(3);
                 foreach ($allDoctors as $key => $value)
                 {
                     $v=$value[0];
                     $sqlAdd = "INSERT INTO clinic_doctor (clinic_id, doctor_id, created_user_id, updated_user_id) values ($l_id, $v, $l_user_id, $l_user_id)";
                     $l_answer = $ctrl->execute($sqlAdd);
                 }
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New clinic Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function saveDoctor()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_id = $_POST["record_id"];
         $l_name = $_POST["record_first_name"];
         $l_username = $_POST["record_username"];
         $l_password = $_POST["record_password"];
         $l_operation_hours = $_POST["record_operation_hours"];
         $l_designation = $_POST["record_designation"];
         $l_email = $_POST["record_email"];
         $l_phone = $_POST["record_phone"];
		 $l_dial_code = $_POST["record_dial_code"];
         $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";


         $t_sql="select id from user " .
           " where upper(first_name) =  " . $ctrl->MYSQLQ($l_name) .
           " and id != " . $ctrl->MYSQLQ($l_id) ;
         $res_id=$ctrl->getRecordID($t_sql);
         if ( $res_id > 0 )
         {
           $p_data["message"]= "Duplicate doctor Name...please enter valid one";
           $p_data["success"]= "false";
           return $p_data;
         }

         if ( $l_id > 0 )
         {
             $fieldlists =   " first_name  =  " . $ctrl->MYSQLQ($l_name) . "," .
                             " user_type  =  " . $ctrl->MYSQLQ(3) . "," .
                             " username  =  " . $ctrl->MYSQLQ($l_username) . "," .
                             " phone  =  " . $ctrl->MYSQLQ($l_phone) . "," .
                             " dial_code  =  " . $ctrl->MYSQLQ($l_dial_code) . "," .
                             " email  =  " . $ctrl->MYSQLQ($l_email) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " operation_hours  =  " . $ctrl->MYSQLQ($l_operation_hours) . "," .
                             " designation  =  " . $ctrl->MYSQLQ($l_designation) ;

            $l_flname = $_POST["record_copied_file"];
            $l_url = $this->getPhotoURL($l_flname,FALSE, $l_id, FALSE);
            if ( $l_url != "no" )
            {
                $fieldlists .=   ",photourl  =  " . $ctrl->MYSQLQ($l_url);
            }
			if ( $l_password != null && $l_password != "" )
			{
             	$l_password = password_hash($l_password, PASSWORD_DEFAULT);
                 $fieldlists .=   " ,password =  " . $ctrl->Q($l_password);
			}
             $sql= "update user set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                 $allClinics = $this->getAllFromUserType(2);
                 foreach ($allClinics as $key => $value)
                 {
                      $v=$value[0];
                      $l_includeClinic = $_POST["record_$v"];
                      $this->getClinicCheckbox($l_includeClinic, $v, $l_id);
                  }
                 $p_data["message"]= "Saved" ;
                 $p_data["success"]= "true";
             }
             else
             {
                 $p_data["message"]= $ctrl->getServerError();
                 $p_data["success"]= "false";
				 $message = "Doctor Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
         	$l_password = password_hash($l_password, PASSWORD_DEFAULT);
         $l_uniqueid = $_POST["record_uniqueid"];
         $l_flname = $_POST["record_copied_file"];
         $l_url = $this->getPhotoURL($l_flname,FALSE, $l_uniqueid, TRUE);
         $l_sid = $ctrl->createUniqueID();
             $fields =   " first_name,user_type,created_user_id, updated_user_id,
             				photourl,sid,username,password,email,phone,dial_code,
             				isactive,operation_hours,designation,created_ts";
             $values =    $ctrl->MYSQLQ($l_name) . "," .
                          $ctrl->MYSQLQ(3) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($l_sid) . "," .
                          $ctrl->MYSQLQ($l_username) . "," .
                          $ctrl->Q($l_password) . "," .
                          $ctrl->MYSQLQ($l_email) . "," .
                          $ctrl->MYSQLQ($l_phone) . "," .
                          $ctrl->MYSQLQ($l_dial_code) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_operation_hours) . "," .
                          $ctrl->MYSQLQ($l_designation) . "," .
                          $ctrl->MYSQLQ($tm);
             $sql= "insert into user ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             $ctrl = Controller::get();
             if ( $l_resid == 1 )
             {

                    $sql = "SELECT id FROM user WHERE first_name = '$l_name'";
                    $l_id = $ctrl->getRecordID($sql);
                    $allClinics = $this->getAllFromUserType(2);
                    foreach ($allClinics as $key => $value)
                    {
                        $v=$value[0];
                        $l_includeClinic = $_POST["record_$v"];
                        $sqlAdd = "INSERT INTO clinic_doctor (clinic_id, doctor_id, created_user_id, updated_user_id) values ($v, $l_id, $l_user_id, $l_user_id)";
                        $l_answer = $ctrl->execute($sqlAdd);
                        if ($l_answer == 1)
                        {
                            $p_data["message"]= "Saved" ;
                            $p_data["success"]= "true";
                        }
                        else
                        {
                            $p_data["message"]= $ctrl->getServerError();
                            $p_data["success"]= "false";
                        }
                        $this->getClinicCheckbox($l_includeClinic, $v, $l_id);
                    }

             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New doctor Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }


         return $p_data;
    }  //
	public function getClinicCheckbox($in, $cid, $did)
  {
      $ctrl = Controller::get();

               if ($in == "true")
               {
                   $sql = "UPDATE clinic_doctor SET isConnected = 'Y' WHERE clinic_id = $cid AND doctor_id = $did";
                   $l_resid = $ctrl->execute($sql);
               }
               else
               {
                   $sql = "UPDATE clinic_doctor SET isConnected = 'N' WHERE clinic_id = $cid AND doctor_id = $did";
                   $l_resid = $ctrl->execute($sql);
               }
        return $sql;
  }
	public function saveCC()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_id = $_POST["record_id"];
         $l_name = $_POST["record_first_name"];
         $l_username = $_POST["record_username"];
         $l_password = $_POST["record_password"];
         $l_operation_hours = $_POST["record_operation_hours"];
         $l_clinic_id = $_POST["record_clinic_id"];
         $l_phone = $_POST["record_phone"];
		 $l_dial_code = $_POST["record_dial_code"];
         $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";


         $t_sql="select id from user " .
           " where upper(first_name) =  " . $ctrl->MYSQLQ($l_name) .
           " and id != " . $ctrl->MYSQLQ($l_id) ;
         $res_id=$ctrl->getRecordID($t_sql);
         if ( $res_id > 0 )
         {
           $p_data["message"]= "Duplicate CC Name...please enter valid one";
           $p_data["success"]= "false";
           return $p_data;
         }

         if ( $l_id > 0 )
         {
             $fieldlists =   " first_name  =  " . $ctrl->MYSQLQ($l_name) . "," .
                             " user_type  =  " . $ctrl->MYSQLQ(4) . "," .
                             " clinic_id  =  " . $ctrl->MYSQLQ($l_clinic_id) . "," .
                             " username  =  " . $ctrl->MYSQLQ($l_username) . "," .
                             " phone  =  " . $ctrl->MYSQLQ($l_phone) . "," .
                             " dial_code  =  " . $ctrl->MYSQLQ($l_dial_code) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " operation_hours  =  " . $ctrl->MYSQLQ($l_operation_hours) ;
            $l_flname = $_POST["record_copied_file"];
            $l_url = $this->getPhotoURL($l_flname,FALSE, $l_id, FALSE);
            if ( $l_url != "no" )
            {
                $fieldlists .=   ",photourl  =  " . $ctrl->MYSQLQ($l_url);
            }
			if ( $l_password != null && $l_password != "" )
			{
             	$l_password = password_hash($l_password, PASSWORD_DEFAULT);
                 $fieldlists .=   " ,password =  " . $ctrl->Q($l_password);
			}
             $sql= "update user set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                 $p_data["message"]= "Saved" ;
                 $p_data["success"]= "true";
             }
             else
             {
                 $p_data["message"]= $ctrl->getServerError();
                 $p_data["success"]= "false";
                 $message = "CC Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
         	$l_password = password_hash($l_password, PASSWORD_DEFAULT);
         $l_uniqueid = $_POST["record_uniqueid"];
         $l_flname = $_POST["record_copied_file"];
         $l_url = $this->getPhotoURL($l_flname,FALSE, $l_uniqueid, TRUE);
         $l_sid = $ctrl->createUniqueID();
             $fields =   " first_name,user_type,created_user_id,updated_user_id,
             				photourl,clinic_id,sid,username,password,phone,dial_code,
             				isactive,operation_hours,created_ts";
             $values =    $ctrl->MYSQLQ($l_name) . "," .
                          $ctrl->MYSQLQ(4) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($l_clinic_id) . "," .
                          $ctrl->MYSQLQ($l_sid) . "," .
                          $ctrl->MYSQLQ($l_username) . "," .
                          $ctrl->Q($l_password) . "," .
                          $ctrl->MYSQLQ($l_phone) . "," .
                          $ctrl->MYSQLQ($l_dial_code) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_operation_hours) . "," .
                          $ctrl->MYSQLQ($tm);
             $sql= "insert into user ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             $ctrl = Controller::get();
             if ( $l_resid == 1 )
             {
                $p_data["message"]= "Saved" ;
                $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New CC Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }


         return $p_data;
    }
	public function saveOrganization()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_name = $_POST["record_name"];
         $l_address1 = $_POST["record_address1"];
         $l_address2 = $_POST["record_address2"];
         $l_city = $_POST["record_city"];
         $l_state = $_POST["record_state"];
         $l_zipcode = $_POST["record_zipcode"];

         $t_sql="select id from organization " .
           " where upper(name) =  " . $ctrl->MYSQLQ($l_name) .
           " and id != " . $ctrl->MYSQLQ($l_record_id) ;
         $res_id=$ctrl->getRecordID($t_sql);
         if ( $res_id > 0 )
         {
           $p_data["message"]= "Duplicate organization Name...please enter valid one";
           $p_data["success"]= "false";
           return $p_data;
         }

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " name  =  " . $ctrl->MYSQLQ($l_name) . "," .
                             " address1  =  " . $ctrl->MYSQLQ($l_address1) . "," .
                             " address2  =  " . $ctrl->MYSQLQ($l_address2) . "," .
                             " city  =  " . $ctrl->MYSQLQ($l_city) . "," .
                             " state  =  " . $ctrl->MYSQLQ($l_state) . "," .
                             " zipcode  =  " . $ctrl->MYSQLQ($l_zipcode) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update organization set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " name,address1,address2,city,state,zipcode, created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_name) . "," .
                          $ctrl->MYSQLQ($l_address1) . "," .
                          $ctrl->MYSQLQ($l_address2) . "," .
                          $ctrl->MYSQLQ($l_city) . "," .
                          $ctrl->MYSQLQ($l_state) . "," .
                          $ctrl->MYSQLQ($l_zipcode) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into organization ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }

         return $p_data;
    }  //
	public function saveDefault()
  {
      $ctrl = Controller::get();
      $ux = UserModel::get();
      $dx = DataModel::get();
      $l_user_id= $ctrl->getUserID();
      $p_data = array();
      $tm =  date('Y-m-d H:i:s');

      $fldame="";
      $dlim="";
      $fieldlists =  "";

      $clinicID = $_POST["record_clinic_id"];
      if( isset($_POST['record_clinic_id_admin']) )
      {
          $clinicID = $_POST['record_clinic_id_admin'];
      }
      $diagnosisID = $_POST["record_diagnosis_id"];
      $groupID = 0;
      $tablePre = "default_patient_discharge_";

      $tableName = $tablePre . "header";
      $sql = "SELECT id FROM $tableName WHERE diagnosis_id = '$diagnosisID'
              AND clinic_id = '$clinicID'";
      $result = $dx->getRecord($sql);
      $resultID = $result[0];
      if($resultID != "E")
      {
          $sql = "DELETE FROM default_patient_discharge_medications WHERE
          			default_patient_discharge_group_id = $resultID";
		  $deleteResult = $ctrl->execute($sql);
		  $sql = "DELETE FROM default_patient_discharge_physicial_activity WHERE
          			default_patient_discharge_group_id = $resultID";
		  $deleteResult = $ctrl->execute($sql);
		  $sql = "DELETE FROM default_patient_discharge_symptoms WHERE
          			default_patient_discharge_group_id = $resultID";
		  $deleteResult = $ctrl->execute($sql);
		  $sql = "DELETE FROM default_patient_discharge_videos WHERE
          			default_patient_discharge_group_id = $resultID";
		  $deleteResult = $ctrl->execute($sql);
		  $sql = "DELETE FROM default_patient_discharge_vitals WHERE
          			default_patient_discharge_group_id = $resultID";
		  $deleteResult = $ctrl->execute($sql);
		  $sql = "DELETE FROM default_patient_discharge_wound_care WHERE
          			default_patient_discharge_group_id = $resultID";
		  $deleteResult = $ctrl->execute($sql);
      }

      $groupID = $resultID;

      $sql = "SELECT id
      			FROM default_patient_discharge_patient_tasks WHERE
      			default_patient_discharge_group_id = $groupID";
      $allTasks = $dx->getOnlyRecords($sql);
      $totaltasks = $allTasks->{"total_records"};
      for ( $i =0; $i <$totaltasks; $i++)
      {
      	$taskRecord = $allTasks->{"record"}[$i];
      	$taskID = $rec["id"];
        $taskName = $_POST["record_task_description_$taskID"];
		$taskFre = $_POST["record_task_frequency_$taskID"];
        $connected = $_POST["record_task_$taskID"];
        if($connected == "true")
        	$isactive = "Y";
		else
			$isactive = "N";

		$fieldlists =   " task  =  " . $ctrl->MYSQLQ($taskName) . "," .
                        " frequency_id  =  " . $ctrl->MYSQLQ($taskFre) . "," .
                        " isactive  =  " . $ctrl->MYSQLQ($isactive) ;
		$sql = "UPDATE default_patient_discharge_patient_tasks
				 SET $fieldlists WHERE id = $taskID";
		$result = $ctrl->execute($sql);
      }


      $task_new = $_POST["record_task_new"];
      if($task_new == "true")
	  {
	  	$tableName = $tablePre . "patient_tasks";
	  	$task_description = $_POST["record_task_description_new"];
	  	$task_fre = $_POST["record_task_frequency_new"];
		$fields =   " default_patient_discharge_group_id,created_user_id,updated_user_id,task,frequency_id,isactive";
              $values =    $ctrl->MYSQLQ($groupID) . "," .
              			   $ctrl->MYSQLQ($l_user_id) . "," .
              			   $ctrl->MYSQLQ($l_user_id) . "," .
                           $ctrl->MYSQLQ($task_description) . "," .
                           $ctrl->MYSQLQ($task_fre) . "," .
                           $ctrl->MYSQLQ('Y');
              $sql= "insert into $tableName ( $fields ) values ( $values ) ";
      		  $result = $ctrl->execute($sql);
	  }

      $vitalsSql = "SELECT id, vital FROM vitals_list";
      $allVitals = $dx->getOnlyRecords($vitalsSql);
      $totalvitals = $allVitals->{"total_records"};
      for($i =0; $i <$totalvitals; $i++)
      {
          $rec=$allVitals->{"record"}[$i];
          $valueID = $rec["id"];
          $checked = $_POST["record_vital_$valueID"];
          $tableName = $tablePre . "vitals";
          if($checked == "true")
          {
          	  $lowred = $_POST["record_vital_lowred_$valueID"];
			  $lowyellow = $_POST["record_vital_lowyellow_$valueID"];
			  $highyellow = $_POST["record_vital_highyellow_$valueID"];
			  $highred = $_POST["record_vital_highred_$valueID"];
			  $fre_id = $_POST["record_vital_frequency_$valueID"];
			  if($lowred == "" || $lowred == " ")
			  	$lowred = NULL;
			  if($lowyellow == "" || $lowyellow == " ")
			  	$lowyellow = NULL;
			  if($highyellow == "" || $highyellow == " ")
			  	$highyellow = NULL;
			  if($highred == "" || $highred == " ")
			  	$highred = NULL;
			  $fields =   " default_patient_discharge_group_id,created_user_id, updated_user_id, vitals_id,low_alert,low_warning,high_warning,high_alert,frequency_id,isactive";
              $values =    $ctrl->MYSQLQ($groupID) . "," .
              			   $ctrl->MYSQLQ($l_user_id) . "," .
              			   $ctrl->MYSQLQ($l_user_id) . "," .
                           $ctrl->MYSQLQ($valueID) . "," .
                           $ctrl->MYSQLQ($lowred) . "," .
                           $ctrl->MYSQLQ($lowyellow) . "," .
                           $ctrl->MYSQLQ($highyellow) . "," .
                           $ctrl->MYSQLQ($highred) . "," .
                           $ctrl->MYSQLQ($fre_id) . "," .
                           $ctrl->MYSQLQ('Y');
              $sql= "insert into $tableName ( $fields ) values ( $values ) ";
      		  $result = $ctrl->execute($sql);
          }


      }

	  $medsSql = "SELECT * from medication_list order by category, medication";
      $allMeds = $dx->getOnlyRecords($medsSql);
      $totalmeds = $allMeds->{"total_records"};
      for($i =0; $i <$totalmeds; $i++)
      {
          $rec=$allMeds->{"record"}[$i];
          $valueID = $rec["id"];
          $checked = $_POST["record_medication_$valueID"];
          $tableName = $tablePre . "medications";
          if($checked == "true")
          {
          	  $freID = $_POST["record_frequency_$valueID"];
              $fields =   " default_patient_discharge_group_id,created_user_id,updated_user_id,medication_id,frequency_id,isactive";
              $values =    $ctrl->MYSQLQ($groupID) . "," .
              			   $ctrl->MYSQLQ($l_user_id) . "," .
              			   $ctrl->MYSQLQ($l_user_id) . "," .
                           $ctrl->MYSQLQ($valueID) . "," .
                           $ctrl->MYSQLQ($freID) . "," .
                           $ctrl->MYSQLQ('Y');
              $sql= "insert into $tableName ( $fields ) values ( $values ) ";
      		  $result = $ctrl->execute($sql);
          }


      }


      $symptomsSql = "SELECT id, symptom FROM symptoms_list WHERE id != 1 AND id != 2";
      $allSymptoms = $dx->getOnlyRecords($symptomsSql);
      $totalsymptoms = $allSymptoms->{"total_records"};
      for($i =0; $i <$totalsymptoms; $i++)
      {
          $rec=$allSymptoms->{"record"}[$i];
          $valueID = $rec["id"];
          $checked = $_POST["record_symptom_$valueID"];
          $tableName = $tablePre . "symptoms";
          if($checked == "true")
          {
              $result = $this->setDefault($tableName, $groupID, "symptoms_id", $valueID, $l_user_id);
          }


      }
      $videosSql = "SELECT id, video FROM videos_list";
      $allVideos = $dx->getOnlyRecords($videosSql);
      $totalvideos = $allVideos->{"total_records"};
      for($i =0; $i <$totalvideos; $i++)
      {
          $rec=$allVideos->{"record"}[$i];
          $valueID = $rec["id"];
          $checked = $_POST["record_video_$valueID"];
          $tableName = $tablePre . "videos";
          if($checked == "true")
          {
              $result = $this->setDefault($tableName, $groupID, "video_id", $valueID, $l_user_id);
          }


      }
      $paSql = "SELECT id, physical_activity FROM physical_activity_list";
      $allPa = $dx->getOnlyRecords($paSql);
      $totalpa = $allPa->{"total_records"};
      for($i =0; $i <$totalpa; $i++)
      {
          $rec=$allPa->{"record"}[$i];
          $valueID = $rec["id"];
          $checked = $_POST["record_pa_$valueID"];
          $tableName = $tablePre . "physicialactivity";
          if($checked == "true")
          {
              $result = $this->setDefault($tableName, $groupID, "physicialactivity_id", $valueID, $l_user_id);
          }


      }
      $woundSql = "SELECT id, description FROM wound_care_list";
      $allWound = $dx->getOnlyRecords($woundSql);
      $totalwc = $allWound->{"total_records"};
      for($i =0; $i <$totalwc; $i++)
      {
          $rec=$allWound->{"record"}[$i];
          $valueID = $rec["id"];
          $checked = $_POST["record_wc_$valueID"];
          $tableName = $tablePre . "wound_care";
          if($checked == "true")
          {
              $result = $this->setDefault($tableName, $groupID, "wound_care_id", $valueID, $l_user_id);
          }
      }

      $p_data["message"]= "Saved" ;
      $p_data["success"]= "true";

      return $p_data;
  }
	public function setDefault($tableName, $groupID, $valueName, $valueID, $l_user_id)
  {
      $message = "";
      $ctrl = Controller::get();
             $fields =   " default_patient_discharge_group_id,created_user_id, updated_user_id,$valueName,isactive";
             $values =    $ctrl->MYSQLQ($groupID) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($valueID) . "," .
                          $ctrl->MYSQLQ('Y');
             $sql= "insert into $tableName ( $fields ) values ( $values ) ";
      $l_resid = $ctrl->execute($sql);
      if($l_resid == 1)
      {
          $message = "all good";
      }
      else
      {
          $message = "SQL statement failed.";
      }
      return $message;

  }
	public function saveVideoList()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_video = $_POST["record_video"];
         $l_url = $_POST["record_url"];
		 $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " video  =  " . $ctrl->MYSQLQ($l_video) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update videos_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Video Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " video,url,isactive,  created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_video) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into videos_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Video Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function saveUploadfile($p_data)
  {
         $ctrl = Controller::get();
         $tm =  date('Y-m-d H:i:s');
         $l_isactive  = "Y";
         {

             $l_user_id= $p_data["user_id"];
             $l_file_name = $p_data["file_name"];
             $l_url = $p_data["url"];
             $l_file_path = $p_data["file_path"];
             $l_description = $p_data["description"];
             $l_file_type = $p_data["file_type"];

             $fields =   " file_name,full_path,description,url,file_type,isactive,created_ts,created_user_id,updated_ts,user_id,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_file_name) . "," .
                          $ctrl->MYSQLQ($l_file_path) . "," .
                          $ctrl->MYSQLQ($l_description) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($l_file_type) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_user_id);
             $sql= "insert into file_uploads ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
                    $sql = "SELECT id FROM file_uploads WHERE created_user_id = $l_user_id
                    		AND full_path = '$l_file_path' ";
					$photoID = $ctrl->getRecordID($sql);
                    $fields =   " patient_id,log_type,record_id,total_choice,total_answer,response,created_ts ";
         			$values =    $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ('photo') . "," .
                          $ctrl->MYSQLQ($photoID) . "," .
                          $ctrl->MYSQLQ(0) . "," .
                          $ctrl->MYSQLQ(0) . "," .
                          $ctrl->MYSQLQ('') . "," .
                          $ctrl->MYSQLQ($tm);

         			$sql= "insert into user_activity ( $fields ) values ( $values ) ";
         			$l_resid = $ctrl->execute($sql);
             }
             else
             {
             		$this->logme("Save uploaded image failed. $tm");
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Image Upload Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function saveUserImage($p_data)
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $tm =  date('Y-m-d H:i:s');
         {
             $l_record_id= $p_data["record_id"];
             $l_formname= $p_data["formname"];
             $l_table= "";
             $l_url = $p_data["url"];
             $fieldlists =   " photourl  =  " . $ctrl->MYSQLQ($l_url) . "," .
                " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;


             $sql= "update $l_formname  set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);

             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "User Image Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function saveUserAppt()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_doctor_id = $_POST["record_doctor_id"];
         $l_clinic_id = $_POST["record_clinic_id"];
         $l_patient_id = $_POST["record_patient_id"];
         $l_date = $_POST["record_date"];
         $l_time = $_POST["record_time"];
         $lar  = explode("/",$l_date);
         $l_apptdt  = $lar[2] . ":". $lar[0] . ":" . $lar[1] .
               " " . $l_time . ":00";

         $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "false" )
            $l_isactive  = "N";
         else
            $l_isactive  = "Y";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " doctor_id  =  " . $ctrl->MYSQLQ($l_doctor_id) . "," .
                             " clinic_id  =  " . $ctrl->MYSQLQ($l_clinic_id) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " appointment_ts  =  " . $ctrl->MYSQLQ($l_apptdt) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update appointments set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Appointment Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " doctor_id,clinic_id, appointment_ts,isactive,created_ts,created_user_id,updated_ts,patient_id,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_doctor_id) . "," .
                          $ctrl->MYSQLQ($l_clinic_id) . "," .
                          $ctrl->MYSQLQ($l_apptdt) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_patient_id) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into appointments ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
                   $message = "Appointment Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function saveUser()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_id = $_POST["record_id"];
		 $l_type = $_POST["record_type"];
         $l_first_name = $_POST["record_first_name"];
         $l_last_name = $_POST["record_last_name"];
         $l_email = $_POST["record_email"];
         $l_username = $_POST["record_username"];
		 $l_address1 = $_POST["record_address1"];
         $l_address2 = $_POST["record_address2"];
         $l_city = $_POST["record_city"];
         $l_state = $_POST["record_state"];
         $l_zipcode = $_POST["record_zipcode"];
         $l_email = $_POST["record_email"];
         $l_phone = $_POST["record_phone"];
		 $l_dial_code = $_POST["record_dial_code"];
         $l_isactive = $_POST["record_isactive"];
         $l_operation_hours = $_POST["record_operation_hours"];
         $l_designation = $_POST["record_designation"];
		 if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";



         $t_sql="select id from user " .
           " where upper(username) =  " . $ctrl->MYSQLQ($l_username) .
           " and id != " . $ctrl->MYSQLQ($l_id) ;
         $res_id=$ctrl->getRecordID($t_sql);
         if ( $res_id > 0 )
         {
           $p_data["message"]= "Duplicate User Name...please enter diferent one";
           $p_data["success"]= "false";
           return $p_data;
         }

         $l_phone = $_POST["record_phone"];
         $l_password = $_POST["record_password"];


         if ( $l_id > 0 )
         {
             $fieldlists =   " first_name  =  " . $ctrl->MYSQLQ($l_first_name) . "," .
                             " user_type  =  " . $ctrl->MYSQLQ(1) . "," .
                             " phone  =  " . $ctrl->MYSQLQ($l_phone) . "," .
                             " dial_code  =  " . $ctrl->MYSQLQ($l_dial_code) . "," .
                             " username  =  " . $ctrl->MYSQLQ($l_username) . "," .
                             " updated_user_id = " . $ctrl->MYSQLQ($l_user_id) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive);

             if ( $l_password != null && $l_password != "" )
             {
             	 $l_password = password_hash($l_password, PASSWORD_DEFAULT);
                 $fieldlists .=   " ,password =  " . $ctrl->Q($l_password);
			 }
			 if($l_type == 2)//Clinic
			 {
			 	$fieldlists .=
                             ", address1  =  " . $ctrl->MYSQLQ($l_address1) . "," .
                             " address2  =  " . $ctrl->MYSQLQ($l_address2) . "," .
                             " city  =  " . $ctrl->MYSQLQ($l_city) . "," .
                             " operation_hours  =  " . $ctrl->MYSQLQ($l_operation_hours) . "," .
                             " state  =  " . $ctrl->MYSQLQ($l_state) . "," .
                             " zipcode  =  " . $ctrl->MYSQLQ($l_zipcode) . "," .
                             " email  =  " . $ctrl->MYSQLQ($l_email) . "," .
                             " phone  =  " . $ctrl->MYSQLQ($l_phone);
			 }
			 else if($l_type == 3)//Doctor
			 {
			 	$fieldlists .=
                             ", operation_hours  =  " . $ctrl->MYSQLQ($l_operation_hours) . "," .
                             " email  =  " . $ctrl->MYSQLQ($l_email) . "," .
                             " designation  =  " . $ctrl->MYSQLQ($l_designation);
			 }
			 $l_flname = $_POST["record_copied_file"];
            $l_url = $this->getPhotoURL($l_flname,FALSE, $l_id, FALSE);
            if ( $l_url != "no" )
            {
                $fieldlists .=   ",photourl  =  " . $ctrl->MYSQLQ($l_url);
            }
             $sql= "update user set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "User Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
			 $l_password = password_hash($l_password, PASSWORD_DEFAULT);
             $l_sid = $ctrl->createUniqueID();
             $fields =   " first_name,user_type,created_user_id, updated_user_id,
             				isactive,sid,email,phone,dial_code,username,password,
             				created_ts";
             $values =    $ctrl->MYSQLQ($l_first_name) . "," .
                          $ctrl->MYSQLQ(1) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_sid) . "," .
                          $ctrl->MYSQLQ($l_email) . "," .
                          $ctrl->MYSQLQ($l_phone) . "," .
                          $ctrl->MYSQLQ($l_dial_code) . "," .
                          $ctrl->MYSQLQ($l_username) . "," .
                          $ctrl->Q($l_password) . "," .
                          $ctrl->MYSQLQ($tm);

             $sql= "insert into user ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New User Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         return $p_data;
    }  //
	public function pushmedication()
    {
       $ctrl = Controller::get();
       $t1_obj=json_decode("{'none':'none'}");
       $l_dt =  date('Y-m-d H:i:s');
       $t1_obj->{"patient_id"}=$_POST["medicationrecord_patient_id"];
       $l_message = $_POST["medicationrecord_pushmessage"];
       $l_subject = "Message from your Doctor";
       $l_logtype="customMessage";
       if ( $ctrl->isEmpty($l_message) )
       {
         $l_message="We miss you in MobiMD, tracking your medications regularly will help you be well.";
         $l_subject = "Medication Reminder";
         $l_logtype = "medication";
       }
       $t1_obj->{"message"}=$l_message;
       $t1_obj->{"subject"}=$l_subject;
       $t1_obj->{"log_type"}=$l_logtype;
       return $this->pushmessage($t1_obj);
    }
	public function pushimage()
  {
       $ctrl = Controller::get();
       $t1_obj=json_decode("{'none':'none'}");
       $l_dt =  date('Y-m-d H:i:s');
       $t1_obj->{"patient_id"}=$_POST["imagerecord_patient_id"];
       $l_message = $_POST["imagerecord_pushmessage"];
       $l_subject = "Message from your Doctor";
       $l_logtype="customMessage";
       if ( $ctrl->isEmpty($l_message) )
       {
         $l_message="Your wound images have been reviewed.";
         $l_subject = "Wound Image";
         $l_logtype = "woundimage";
       }
       $t1_obj->{"message"}=$l_message;
       $t1_obj->{"subject"}=$l_subject;
       $t1_obj->{"log_type"}=$l_logtype;
       return $this->pushwoundmessage($t1_obj);
  }
	public function pushsymptom()
    {
       $ctrl = Controller::get();
       $t1_obj=json_decode("{'none':'none'}");
       $l_dt =  date('Y-m-d H:i:s');
       $t1_obj->{"patient_id"}=$_POST["symptomrecord_patient_id"];
       $l_message = $_POST["symptomrecord_pushmessage"];
       $l_subject="Message from your Doctor";
       $l_logtype="customMessage";
       if ( $ctrl->isEmpty($l_message) )
       {
           $l_message="We miss you in MobiMD, please track your symptoms regularly.";
           $l_subject="Symptom Assessment";
           $l_logtype="symptom";
       }
       $t1_obj->{"message"}=$l_message;
       $t1_obj->{"subject"}=$l_subject;
       $t1_obj->{"log_type"}=$l_logtype;
       return $this->pushmessage($t1_obj);
    }
	public function reviewSomething($recordname, $tablename, $preKey)
  {
      $ctrl = Controller::get();
	  $user_id= $ctrl->getUserID();
	  $l_dt =  date('Y-m-d H:i:s');
      $p_data = array();
      $mainid = 0;
      foreach($_POST as $key => $value)
      {
			$isItID = $this->endsWith($key, 'ID');
            if ($isItID)
            {
				$mainid = $preKey . $value;
				$plainid = $value;
				$noteID = $recordname . $value . "_note";

            }
      }
      $notes = $_POST["$noteID"];
      $fieldlists =   " isreviewed  = 'Y',
      					reviewer_id = '$user_id',
      					reviewed_ts = '$l_dt',
      					reviewer_note = " . $ctrl->MYSQLQ("$notes");
             $sql= "update $tablename set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($plainid) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                 $p_data["message"]="Marked as reviewed.";
                 $p_data["key"]=$mainid;
                 $p_data["success"]= "true";
             }
             else
             {
                 $p_data["message"]="Sorry, could not mark as reviewed.";
                 $p_data["success"]= "false";
             }
      return $p_data;

  }
	public function endsWith($haystack, $needle)
  {
  	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
  }
	public function reviewSomething_Notify($recordname, $tablename, $preKey, $message)
  {
      $ctrl = Controller::get();
      $user_id= $ctrl->getUserID();
      $l_dt =  date('Y-m-d H:i:s');
      $p_data = array();
      $mainid = 0;
      foreach($_POST as $key => $value)
      {
			$isItID = $this->endsWith($key, 'ID');
            if ($isItID)
            {
				$mainid = $preKey . $value;
				$plainid = $value;
				$noteID = $recordname . $value . "_note";

            }
      }
      $notes = $_POST["$noteID"];
      $fieldlists =   " isreviewed  = 'Y',
      					reviewer_id = '$user_id',
      					reviewed_ts = '$l_dt',
      					reviewer_note = " . $ctrl->MYSQLQ("$notes");
             $sql= "update $tablename set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($plainid) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                 $p_data["message"]="Marked as reviewed.";
                 $p_data["key"]=$mainid;
                 $p_data["success"]= "true";
				 $sql = "SELECT patient_id FROM $tablename WHERE id = " . $ctrl->MYSQLQ($plainid);
				 $l_patient_id = $ctrl->getRecordTEXT($sql);
				 $myrec = $this->getPatient($l_patient_id);
       			 $l_phone= $myrec["phone"];
       			 $l_phone_carrier= $myrec["phone_carrier"];
       			 $l_name=$myrec["first_name"]." " .$myrec["last_name"];
       			 $px=json_decode("{'none':'none'}");
       			 $l_to =$l_phone."@".$l_phone_carrier;
       			 $px->{"to"}=$l_to;
       			 $px->{"patient_id"}=$l_patient_id;
       			 $px->{"subject"}="review";
       			 $px->{"alert_type"}="review";
       			 $px->{"message"}=$message;
       			 $this->push_notificationlog($px);
				 $r1 = $ctrl->sendMail($px);
             }
             else
             {
                 $p_data["message"]="Sorry, could not mark as reviewed.";
                 $p_data["success"]= "false";
             }
      return $p_data;

  }
	public function pushmessage($xobj)
    {
       $ctrl = Controller::get();
       $l_logtype= $xobj->{"log_type"};
       $l_message = $xobj->{"message"};
       $l_subject = $xobj->{"subject"};
       $l_patient_id = $xobj->{"patient_id"};
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
       require_once("include_sendgrid.php");
       $ctrl = Controller::get();
       $myrec = $this->getPatient($l_patient_id);
       $l_phone= $myrec["phone"];
       $l_phone_carrier= $myrec["phone_carrier"];
       $l_name=$myrec["first_name"]." " .$myrec["last_name"];
       $px=json_decode("{'none':'none'}");
       $l_to =$l_phone."@".$l_phone_carrier;
       $px->{"to"}=$l_to;
       $px->{"patient_id"}=$l_patient_id;
       $px->{"subject"}=$l_subject;
       $px->{"alert_type"}=$l_logtype;
       $px->{"message"}=$l_message;
       $r1 = $ctrl->sendMail($px);
       $this->push_notificationlog($px);
       $p_data = array();
       $p_data["message"]= "Request/Message sent to patient ";
       $p_data["success"]= "true";
       return $p_data;
   }
	public function pushwoundmessage($xobj)
    {
       $ctrl = Controller::get();
       $l_logtype= $xobj->{"log_type"};
       $l_message = $xobj->{"message"};
       $l_subject = $xobj->{"subject"};
       $l_patient_id = $xobj->{"patient_id"};
       $p_data = array();
       require_once("include_sendgrid.php");
       $ctrl = Controller::get();
       $myrec = $this->getPatient($l_patient_id);
       $l_phone= $myrec["phone"];
       $l_phone_carrier= $myrec["phone_carrier"];
       $l_name=$myrec["first_name"]." " .$myrec["last_name"];
       $px=json_decode("{'none':'none'}");
       $l_to =$l_phone."@".$l_phone_carrier;
       $px->{"to"}=$l_to;
       $px->{"patient_id"}=$l_patient_id;
       $px->{"subject"}=$l_subject;
       $px->{"alert_type"}=$l_logtype;
       $px->{"message"}=$l_message;
       //$r1 = $ctrl->sendMail($px);
       $this->push_notificationlog($px);
       $p_data = array();
       $p_data["message"]= "Message sent to patient ";
       $p_data["success"]= "true";
       return $p_data;
   }
	public function PatientCompliance()
   {
         $ctrl = Controller::get();
         $p_data = array();
         $lstr=$_POST["record_datatype"];
         $p_data["message"]= "Good one" + $lstr;
         $p_data["success"]= "true";
         return $p_data;
    }
	public function saveAllMedication()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "medicationrecord_";
         $tm =  date('Y-m-d H:i:s');

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];
         $l_starttime = $_POST[$l_prefix."starttime_id"];

         $fieldlists =   " med_start_time  =  " . $ctrl->MYSQLQ($l_starttime) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update patient set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);




         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $donotcall= 0;
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_medication_id = $_POST[$l_pre."medication_id"];

         $l_frequency = $_POST[$l_pre."medication_frequency"];

         $l_isactive = $_POST[$l_pre."isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
         {
             $l_isactive = "N";
             $sql="SELECT medication FROM medication_list WHERE id = $l_medication_id";
             $medName = $ctrl->getRecordTEXT($sql);
             $sql = "DELETE FROM med_activity WHERE medicine = '$medName' AND patient_id = $l_patient_id";
             $ctrl->execute($sql);
             $donotcall = 1;
         }




         $l_date = $_POST[$l_pre."start_date"];
         if (  $this->hasValue($l_date) )
         {
             $lar  = explode("/",$l_date);
             $l_start_date  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
         }

         $l_date = $_POST[$l_pre."end_date"];
         if (  $this->hasValue($l_date) )
         {
             $lar  = explode("/",$l_date);
             $l_end_date  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
         }

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " medication_id  =  " . $ctrl->MYSQLQ($l_medication_id) . "," .
                             " frequency  =  " . $ctrl->MYSQLQ($l_frequency) . "," .
                             " start_date  =  " . $ctrl->MYSQLQ($l_start_date) . "," .
                             " end_date  =  " . $ctrl->MYSQLQ($l_end_date) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update medication set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             $donotcall = 1;
             if ( $l_resid == 1 )
             {

                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " medication_id,frequency,isactive,start_date,end_date,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_medication_id) . "," .
                          $ctrl->MYSQLQ($l_frequency) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_start_date) . "," .
                          $ctrl->MYSQLQ($l_end_date) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into medication ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from medication where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and created_ts = " . $ctrl->MYSQLQ($tm).
                        " and medication_id  = " . $ctrl->MYSQLQ($l_medication_id);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
                        if($donotcall == 0)
                        {
                            $sql = "SELECT medication FROM medication_list WHERE id = $l_medication_id";
                            $medName = $ctrl->getRecordTEXT($sql);
                            $addedMedication = $this-> addNewMedication($l_record_id, $medName, $l_frequency, $l_patient_id, $l_starttime);

                        }
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }

        }  // for
        if ( $l_success == 1 )
        {

                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "Medication Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
	public function addNewMedication($lMedicationTableID, $lMedication, $lFrequency, $currentPatientID, $currentPatientStartTime)
  {
                $frequencies = array();
                $limit = 0;
                $ctrl = Controller::get();
                $dx = DataModel::get();
                $tm =  date('Y-m-d H:i:s');

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
                if($l_resid == 1)
                {
                }

  }
	public function saveAllDiet()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "dietrecord_";

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];


         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_diet_id = $_POST[$l_pre."diet_id"];
         $l_isactive = $_POST[$l_pre."isactive"];

         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " diet_id=  " . $ctrl->MYSQLQ($l_diet_id). "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update diet set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " diet_id,isactive,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_diet_id) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into diet ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from diet where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and created_ts = " . $ctrl->MYSQLQ($tm).
                        " and diet_id  = " . $ctrl->MYSQLQ($l_diet_id);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }
        }  // for
        if ( $l_success == 1 )
        {
                   $p_data["message"]= "Saved" ;
                   $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "Diet Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
    public function saveAllTasks()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "taskrecord_";

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];


         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_task = $_POST[$l_pre."name"];
         $l_fre = $_POST[$l_pre."task_frequency"];
		 $l_isactive = $_POST[$l_pre."isactive"];

         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         $l_date = $_POST[$l_pre."start_date"];
         if (  $this->hasValue($l_date) )
         {
             $lar  = explode("/",$l_date);
             $l_start_date  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
         }

         $l_date = $_POST[$l_pre."end_date"];
         if (  $this->hasValue($l_date) )
         {
             $lar  = explode("/",$l_date);
             $l_end_date  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
         }

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " task=  " . $ctrl->MYSQLQ($l_task). "," .
                             " frequency_id  =  " . $ctrl->MYSQLQ($l_fre) . "," .
                             " start_date  =  " . $ctrl->MYSQLQ($l_start_date) . "," .
                             " end_date  =  " . $ctrl->MYSQLQ($l_end_date) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update patient_tasks set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " task,frequency_id,start_date,end_date,isactive,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_task) . "," .
                          $ctrl->MYSQLQ($l_fre) . "," .
                          $ctrl->MYSQLQ($l_start_date) . "," .
                          $ctrl->MYSQLQ($l_end_date) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into patient_tasks ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from patient_tasks where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and task  = " . $ctrl->MYSQLQ($l_task);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }
        }  // for
        if ( $l_success == 1 )
        {
                   $p_data["message"]= "Saved" ;
                   $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "Task Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
    public function saveAllVitals()
  	{
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "vitalrecord_";

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];


         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_vital_id = $_POST[$l_pre."vital_id"];
		 $l_lowred = $_POST[$l_pre."lowred"];
		 $l_lowyellow = $_POST[$l_pre."lowyellow"];
		 $l_highyellow = $_POST[$l_pre."highyellow"];
		 $l_highred = $_POST[$l_pre."highred"];
		 $l_frequency = $_POST[$l_pre."frequency"];
         $l_isactive = $_POST[$l_pre."isactive"];

         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " vital_id=  " . $ctrl->MYSQLQ($l_vital_id). "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " low_alert  =  " . $ctrl->MYSQLQ($l_lowred) . "," .
                             " low_warning  =  " . $ctrl->MYSQLQ($l_lowyellow) . "," .
                             " high_warning  =  " . $ctrl->MYSQLQ($l_highyellow) . "," .
                             " high_alert  =  " . $ctrl->MYSQLQ($l_highred) . "," .
                             " frequency_id  =  " . $ctrl->MYSQLQ($l_frequency) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update vitals set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " vital_id,low_alert, low_warning, high_warning, high_alert,frequency_id,isactive,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_vital_id) . "," .
             			  $ctrl->MYSQLQ($l_lowred) . "," .
             			  $ctrl->MYSQLQ($l_lowyellow) . "," .
             			  $ctrl->MYSQLQ($l_highyellow) . "," .
             			  $ctrl->MYSQLQ($l_highred) . "," .
             			  $ctrl->MYSQLQ($l_frequency) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into vitals ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from vitals where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and created_ts = " . $ctrl->MYSQLQ($tm).
                        " and vital_id  = " . $ctrl->MYSQLQ($l_vital_id);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }
        }  // for
        if ( $l_success == 1 )
        {
                   $p_data["message"]= "Saved" ;
                   $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "Vitals Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
	public function saveAllWoundcare()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "woundcarerecord_";

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];


         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_description = $_POST[$l_pre."woundcare_id"];
         $l_isactive = $_POST[$l_pre."isactive"];

         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " wound_care_id=  " . $ctrl->MYSQLQ($l_description). "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update wound_care set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " wound_care_id,isactive,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_description) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into wound_care ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from woundcare where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and created_ts = " . $ctrl->MYSQLQ($tm).
                        " and description  = " . $ctrl->MYSQLQ($l_description);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }
        }  // for
        if ( $l_success == 1 )
        {
                   $p_data["message"]= "Saved" ;
                   $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "Woundcare Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
	public function saveAllPhyact()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "phyactrecord_";

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];


         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_physicalactivity_id = $_POST[$l_pre."physicalactivity_id"];
         $l_isactive = $_POST[$l_pre."isactive"];

         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " physical_activity_id=  " . $ctrl->MYSQLQ($l_physicalactivity_id). "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update physical_activity set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " physical_activity_id,isactive,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_physicalactivity_id) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into physical_activity ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from physical_activity where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and created_ts = " . $ctrl->MYSQLQ($tm).
                        " and physical_activity_id  = " . $ctrl->MYSQLQ($l_physicalactivity_id);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }
        }  // for
        if ( $l_success == 1 )
        {
                   $p_data["message"]= "Saved" ;
                   $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "PhyAct Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
	public function saveAllSymptom()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "symptomrecord_";

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];


         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_symptom_id =$_POST[$l_pre."symptom_id"];
         $l_isactive = $_POST[$l_pre."isactive"];

         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         $l_date = $_POST[$l_pre."start_date"];
         if (  $this->hasValue($l_date) )
         {
             $lar  = explode("/",$l_date);
             $l_start_date  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
         }

         $l_date = $_POST[$l_pre."end_date"];
         if (  $this->hasValue($l_date) )
         {
             $lar  = explode("/",$l_date);
             $l_end_date  = $lar[2] . "-". $lar[0] . "-" . $lar[1];
         }

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " symptom_id=  " . $ctrl->MYSQLQ($l_symptom_id). "," .
                             " start_date  =  " . $ctrl->MYSQLQ($l_start_date) . "," .
                             " end_date  =  " . $ctrl->MYSQLQ($l_end_date) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update symptoms set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " symptom_id,isactive,start_date,end_date,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_symptom_id). "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_start_date) . "," .
                          $ctrl->MYSQLQ($l_end_date) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into symptoms ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from symptoms where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and created_ts = " . $ctrl->MYSQLQ($tm).
                        " and symptom_id= " . $ctrl->MYSQLQ($l_symptom_id);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }
        }  // for
        if ( $l_success == 1 )
        {
                   $p_data["message"]= "Saved" ;
                   $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "Symptom Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
	public function saveAllVideos()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();

         $l_prefix= "videorecord_";

         $l_patient_id = $_POST[$l_prefix."patient_id"];
         $l_max_row = $_POST[$l_prefix."maxrow"];


         $l_success=1;
         $l_newrecord_ctr=0;
         $p_field_array = array();
         for ( $ictr = 0 ; $ictr <$l_max_row; $ictr++)
         {
         $fieldlists =  "";
         $l_pre= $l_prefix . $ictr . "_";

         $l_id= $l_pre . "id";
         $l_field_id=  $l_id;
         $l_record_id = $_POST[$l_pre."id"];
         $l_video_id =$_POST[$l_pre."video_id"];
         $l_isactive = $_POST[$l_pre."isactive"];

         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         $tm =  date('Y-m-d H:i:s');
         if ( $l_record_id > 0 )
         {
             $fieldlists =   " video_id=  " . $ctrl->MYSQLQ($l_video_id). "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update videos set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) .
                               "   and patient_id  = " . $ctrl->MYSQLQ($l_patient_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " video_id,isactive,created_ts,created_user_id,updated_ts,updated_user_id,patient_id";
             $values =    $ctrl->MYSQLQ($l_video_id). "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . ",".
                          $ctrl->MYSQLQ($l_patient_id);

             $sql= "insert into videos ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                        $sql = "select COALESCE(max(id) ,0) " .
                        " from videos where created_user_id = " . $ctrl->MYSQLQ($l_user_id) .
                        " and created_ts = " . $ctrl->MYSQLQ($tm).
                        " and video_id= " . $ctrl->MYSQLQ($l_video_id);
                        $l_record_id = $ctrl->getRecordID($sql);
                        $t_obj=json_decode($data);
                        $t_obj->{"fld_name"} = $l_field_id;
                        $t_obj->{"fld_value"} = $l_record_id;
                        $p_field_array[$l_newrecord_ctr]=$t_obj;
                        $l_newrecord_ctr++;
             }
         }
         $p_data["INSERTED_ROWS"]=$l_newrecord_ctr;
         $p_data["INSERTED_DATA"]=$p_field_array;
         if ( $l_resid != 1 )
         {
            $l_success =0;
         }
        }  // for
        if ( $l_success == 1 )
        {
                   $p_data["message"]= "Saved" ;
                   $p_data["success"]= "true";
        }
        else
        {
                  $p_data["message"]= $ctrl->getServerError();
                  $p_data["success"]= "false";
				  $message = "Video Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
        }

        return $p_data;
    }  //
	public function push_notificationlog($pa)
    {
       $ctrl = Controller::get();
       $tm =  date('Y-m-d H:i:s');
       $l_patient_id= $ctrl->getval($pa,"patient_id");
       $l_user_id= $ctrl->getval($pa,"user_id");
       $l_alert_type= $ctrl->getval($pa,"alert_type");
       $l_to= $pa->{"to"};
       $l_subject= $pa->{"subject"};
       $l_message= $pa->{"message"};
       $fields =   " patient_id,alert_type,to_email,subject,message,created_ts,created_user_id ";
       $values =$this->Q($l_patient_id) . "," .
                $this->Q($l_alert_type) . "," .
                $this->Q($l_to) . "," .
                $this->Q($l_subject) . "," .
                $this->Q($l_message) . "," .
                $this->Q($tm) . "," .
                $this->Q($l_user_id);

       $sql= "insert into push_notification ( $fields ) values ( $values ) ";
       $l_id = $ctrl->execute($sql);
       return $sql;
   }
	public function saveDietList()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_diet = $_POST["record_diet"];
         $l_url = $_POST["record_url"];
         $l_isactive = $_POST["record_isactive"];

         if ( $l_isactive == "false" )
            $l_isactive  = "N";
         else
            $l_isactive  = "Y";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " diet  =  " . $ctrl->MYSQLQ($l_diet) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update diet_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Diet List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " diet,isactive,url, created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_diet) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into diet_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Diet List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function saveSymptom()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_symptom = $_POST["record_symptom"];
         $l_url = $_POST["record_url"];
		 $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " symptom  =  " . $ctrl->MYSQLQ($l_symptom) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update symptoms_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
                   $message = "Symptom List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " symptom,isactive,url,created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_symptom) . "," .
             			  $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into symptoms_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Symptom List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  // signup
	public function saveVideo()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_video = $_POST["record_video"];
         $l_url = $_POST["record_url"];
		 $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " video  =  " . $ctrl->MYSQLQ($l_video) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update videos_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Video List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " video,isactive,url,created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_video) . "," .
             			  $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into videos_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Video List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
    public function saveVital()
  	{
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_vital = $_POST["record_vital"];
		 $l_graph_type = $_POST["record_graph_type"];
		 $l_graph_min = $_POST["record_graph_min"];
		 $l_graph_max = $_POST["record_graph_max"];
		 $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " vital  =  " . $ctrl->MYSQLQ($l_vital) . "," .
             				 " graph_type  =  " . $ctrl->MYSQLQ($l_graph_type) . "," .
             				 " graph_min  =  " . $ctrl->MYSQLQ($l_graph_min) . "," .
             				 " graph_max  =  " . $ctrl->MYSQLQ($l_graph_max) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update vitals_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
                   $message = "Vital List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " vital,graph_type,graph_min,graph_max,isactive,created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_vital) . "," .
             			  $ctrl->MYSQLQ($l_graph_type) . "," .
             			  $ctrl->MYSQLQ($l_graph_min) . "," .
             			  $ctrl->MYSQLQ($l_graph_max) . "," .
             			  $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into vitals_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
                   $message = "New Vital List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }
	public function saveMedicationList()
  	{
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_category = $_POST["record_category"];
         $l_medication = $_POST["record_medication"];
         $l_description = $_POST["record_description"];
         $l_url = $_POST["record_url"];
         $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " category  =  " . $ctrl->MYSQLQ($l_category) . "," .
                             " medication  =  " . $ctrl->MYSQLQ($l_medication) . "," .
                             " description  =  " . $ctrl->MYSQLQ($l_description) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update medication_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Medication List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " category,medication,isactive,description,url, created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_category) . "," .
                          $ctrl->MYSQLQ($l_medication) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_description) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into medication_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
                   $message = "New Medication List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
    public function saveMedicationClassList()
  	{
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_description = $_POST["record_description"];
         $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " description  =  " . $ctrl->MYSQLQ($l_description) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update medications_class set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }
         else
         {
             $fields =   " isactive,description,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_description) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into medications_class ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
             }
         }

         return $p_data;
    }
	public function saveDiagnosisList()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_diagnosis = $_POST["record_diagnosis"];
         $l_description = $_POST["record_description"];
         $l_description = addslashes($l_description);
         $l_url = $_POST["record_url"];
		 $l_isactive = $_POST["record_isactive"];
         if ( $l_isactive == "true")
            $l_isactive = "Y";
         else
            $l_isactive = "N";


         if ( $l_record_id > 0 )
         {
             $fieldlists =   " diagnosis  =  " . $ctrl->MYSQLQ($l_diagnosis) . "," .
                             " description  =  " . $ctrl->MYSQLQ($l_description) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update diagnosis_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Diagnosis List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " diagnosis,description,isactive,url, created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_diagnosis) . "," .
                          $ctrl->MYSQLQ($l_description) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into diagnosis_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Diagnosis List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }
	public function savePhysicalActivityList()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_physicalactivity = $_POST["record_physicalactivity"];
         $l_url = $_POST["record_url"];
         $l_isactive = $_POST["record_isactive"];

         if ( $l_isactive == "false" )
            $l_isactive  = "N";
         else
            $l_isactive  = "Y";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " physical_activity  =  " . $ctrl->MYSQLQ($l_physicalactivity) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update physical_activity_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Physical Activity List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " physical_activity,isactive,url, created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_physicalactivity) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into physical_activity_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Physical Activity List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
	public function saveWoundList()
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');

         $fldame="";
         $dlim="";
         $fieldlists =  "";
         $l_record_id = $_POST["record_id"];
         $l_wound = $_POST["record_description"];
         $l_url = $_POST["record_url"];
         $l_isactive = $_POST["record_isactive"];

         if ( $l_isactive == "false" )
            $l_isactive  = "N";
         else
            $l_isactive  = "Y";

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " description  =  " . $ctrl->MYSQLQ($l_wound) . "," .
                             " url  =  " . $ctrl->MYSQLQ($l_url) . "," .
                             " isactive  =  " . $ctrl->MYSQLQ($l_isactive) . "," .
                             " updated_ts  =  " . $ctrl->MYSQLQ($tm) . "," .
                             " updated_user_id  =  " . $ctrl->MYSQLQ($l_user_id) ;

             $sql= "update wound_care_list set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "Wound Care List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }
         else
         {
             $fields =   " description,isactive,url, created_ts,created_user_id,updated_ts,updated_user_id";
             $values =    $ctrl->MYSQLQ($l_wound) . "," .
                          $ctrl->MYSQLQ($l_isactive) . "," .
                          $ctrl->MYSQLQ($l_url) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id) . "," .
                          $ctrl->MYSQLQ($tm) . "," .
                          $ctrl->MYSQLQ($l_user_id);

             $sql= "insert into wound_care_list ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
             if ( $l_resid == 1 )
             {
                    $p_data["message"]= "Saved" ;
                    $p_data["success"]= "true";
             }
             else
             {
                   $p_data["message"]= $ctrl->getServerError();
                   $p_data["success"]= "false";
				   $message = "New Wound Care List List Saving Error \n User id : $l_user_id \n SQL : $sql";
	          	   $ctrl->sendError($message);
             }
         }

         return $p_data;
    }  //
    public function ignore($patientID)
    {
    	$ctrl = Controller::get();
    	$fieldlists =   " ignore_execptions  =  " . $ctrl->MYSQLQ('Y') ;
        $sql= "update patient set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($patientID) ;
		$l_resid = $ctrl->execute($sql);
		if ( $l_resid == 1 )
        {
              $p_data["message"]= "Saved" ;
              $p_data["success"]= "true";
        }
        else
        {
              $p_data["message"]= $ctrl->getServerError();
              $p_data["success"]= "false";
        }
        return $p_data;
    }
	public function saveusersettting($p_formname,$p_fieldname,$p_fieldvalue)
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $l_resid=0;
         $k_sql = "select id from user_setting where user_id = " .  $ctrl->MYSQLQ($l_user_id) .
                     " and fieldname = " .$ctrl->MYSQLQ($p_fieldname) .
                     " and formname  = " .$ctrl->MYSQLQ($p_formname ) ;

         $l_record_id = $ctrl->getRecordID($k_sql);

         if ( $l_record_id > 0 )
         {
             $fieldlists =   " fieldvalue  =  " . $ctrl->MYSQLQ($p_fieldvalue);

             $sql= "update user_setting set  " . $fieldlists .
                               " where id   = " . $ctrl->MYSQLQ($l_record_id) ;
             $l_resid = $ctrl->execute($sql);
         }
         else
         {
             $fields =   " formname, fieldname, fieldvalue, user_id  ";
             $values =    $ctrl->MYSQLQ($p_formname) . "," .
                          $ctrl->MYSQLQ($p_fieldname) . "," .
                          $ctrl->MYSQLQ($p_fieldvalue) . "," .
                          $ctrl->MYSQLQ($l_user_id);
             $sql= "insert into user_setting ( $fields ) values ( $values ) ";
             $l_resid = $ctrl->execute($sql);
         }
         return $l_resid;;
    }  //
	public function geteusersettting($p_formname,$p_fieldname)
  {
         $ctrl = Controller::get();
         $l_user_id= $ctrl->getUserID();
         $l_resid=0;
         $k_sql = "select fieldvalue from user_setting where user_id = " .
                        $ctrl->MYSQLQ($l_user_id) .
                     " and fieldname = " .$ctrl->MYSQLQ($p_fieldname) .
                     " and formname  = " .$ctrl->MYSQLQ($p_formname );

         $l_res = $ctrl->getRecordTEXT($k_sql);

         return $l_res;
    }  //
}
