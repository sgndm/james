<?php
class UserModel
{ 
   public function __construct() 
   { 
   } 

   public static $user_created=1;
   public static $user_created_text="User Created";
   public static $user_email_verified=2;
   public static $user_email_verified_text="Email Verified";
   public static $user_phone_verified=3;
   public static $user_phone_verified_text="Phone Call Verified";
   public static $user_terms_accepted=4;
   public static $user_terms_accepted_text = "Terms Accepted";
   public static $user_account_locked=7;
   public static $user_account_locked_text = "Account Locked";
   public static $user_account_inactive=8;
   public static $user_account_inactive_text = "Account Inactive";
   public static $user_account_closed=9;
   public static $user_account_closed_text = "Account Closed";

   static $st_instance = null;

   public static function get()
   {
      if ( UserModel::$st_instance == null  )
         UserModel::$st_instance = new UserModel();
      return UserModel::$st_instance;
   }
 
   public function getStatusList()
   {
        return    self::$user_created_text . "," .
                  self::$user_email_verified_text . "," .
                  self::$user_phone_verified_text . "," .
                  self::$user_terms_accepted_text  . "," .
                  self::$user_account_locked_text  . "," .
                  self::$user_account_inactive_text  . "," .
                  self::$user_account_closed_text;
   }

   public function getStatusID()
   {
        return self::$user_created . "," .
               self::$user_email_verified . "," .
               self::$user_phone_verified . "," .
               self::$user_terms_accepted . "," .
               self::$user_account_locked . "," .
               self::$user_account_inactive . "," .
               self::$user_account_closed;
   }
   public function status_decode_id($l_status)
   { 
      if ( $l_status == self::$user_created_text )
          return self::$user_created;
      if ( $l_status == self::$user_email_verified_text )
         return self::$user_email_verified;
      if ( $l_status == self::$user_phone_verified_text )
         return self::$user_phone_verified;
      if ( $l_status == self::$user_terms_accepted_text )
         return self::$user_terms_accepted;
      if ( $l_status == self::$user_account_locked_text )
         return self::$user_account_locked;
      if ( $l_status == self::$user_account_inactive_text )
         return self::$user_account_inactive;
      if ( $l_status == self::$user_account_closed_text )
         return self::$user_account_closed;
      return $l_status;
   }
   public function status_decode($l_status)
   { 
      if ( $l_status == self::$user_created )
          return self::$user_created_text;
      if ( $l_status == self::$user_email_verified )
         return self::$user_email_verified_text;
      if ( $l_status == self::$user_phone_verified )
         return self::$user_phone_verified_text;
      if ( $l_status == self::$user_terms_accepted )
         return self::$user_terms_accepted_text;
      if ( $l_status == self::$user_account_locked )
         return self::$user_account_locked_text;
      if ( $l_status == self::$user_account_inactive )
         return self::$user_account_inactive_text;
      if ( $l_status == self::$user_account_closed )
         return self::$user_account_closed_text;
      return "Unknowstatus $l_status";
   }
   public function chkduplicateemail()
   {
         $ctrl = Controller::get(); 
         $p_data = array();
         $email=$_POST["email"];
         $l_res=false;
         $sql = "select COALESCE(max(id) ,0) from user where email = " . $ctrl->MYSQLQ($email); 
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
         $p_data = array();
         $email=$_POST["login_email"];
         $pwd=$_POST["login_password"];
         $l_res=false;
         $sql = "select id, sid,verified,first_name    from user where upper(email) = " . $ctrl->MYSQLQ(strtoupper($email)) . 
                                                    " and password = " . $ctrl->MYSQLQ($pwd); 
         $l_record  = $ctrl->getRecord($sql);
         if ( count($l_record) > 0  && $l_record[0]  > 0 ) 
         {
            $l_id=$l_record[0];
            $l_sid=$l_record[1];
            $l_verified=$l_record[2];
            $first_name=$l_record[3];
            if ( $l_verified == "Y" ) 
            {
               $p_data["sid"]= $l_sid;
               $p_data["message"]= "good one ";
               $p_data["success"]= "true";
            }
            else 
            {
               $p_data["message"]= "Verification is required to use the system ... verification email sent to your email address" ;
               $p_data["success"]= "false";
               // send email
               $dt=array();
               $dt["to"] = $email;
               $dt["subject"] = "Verification email from Aclaime" ;
               $dlim="\r\n\r\n";

               $l_url="http://" . $_SERVER['SERVER_NAME']. "/index.php?verifyid=" . $l_sid;

            $dt["message"] = "Hi $first_name, " . $dlim.  
"Thank you for registering with Aclaime Online and for expressing interest in Borrowers on our Site. Aclaime Online must verify each Lender on our site and you may be restricted in terms of access to Borrower information or functionality until this process is completed. You may also be required to provide additional personal or business information and identification prior to viewing information on Aclaime Online. Aclaime Online, partner or affiliate may be required to contact you as part of the verification process." . $dlim.
"Please click the following link to verify the account "   . $dlim . 
$l_url .  $dlim.
"Thank you, ". $dlim. 
"The Aclaime Online Team ". $dlim . $dlim;
   
               $ctrl->sendmail($dt);
            }
         }
         else
         {
            $p_data["message"]= "Please check the credential ";
            $p_data["success"]= "false";
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
         $sql = "select id, password ,first_name  from user where email = " . $ctrl->MYSQLQ($email) ;
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
                       "Aclaime Support Team"  . $dlim;

            $ctrl->sendmail($dt);
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

   public function signup()
   {
         $ctrl = Controller::get(); 
         $p_data = array();
         $first_name=$_POST["signup_firstname"];
         $last_name=$_POST["signup_lastname"];
         $email=$_POST["signup_email"];
         $phone=$_POST["signup_phone"];
         $phone_db= str_replace("-","",$phone);
         $pwd=$_POST["signup_password1"];
         $notify=$_POST["signup_notify_me_email"];
         $notify_email ="N"; 
         if ( $notify == "true" )
             $notify_email ="Y"; 

         $notify=$_POST["signup_notify_me_text"];
         $notify_text ="N"; 
         if ( $notify == "true" )
             $notify_text ="Y"; 

         $l_res=false;

         $sql = "select COALESCE(max(id) ,0) from user where email = " . $ctrl->MYSQLQ($email); 
         $l_id = $ctrl->getRecordID($sql);
         if ( $l_id > 0 ) 
         {
            $p_data["message"]= "Email Address already Registered ";
            $p_data["success"]= "false";
         }
         else
         {
         $sid = $ctrl->createUniqueID();
         $salt = $ctrl->createUniqueID();
         $n_pwd = hash('sha256', $salt . $pwd);
         $fields= "first_name,last_name,status,email,phone,password,notify_email,notify_text,salt,sid,verified";
         $values= $ctrl->MYSQLQ($first_name) .  "," .
                   $ctrl->MYSQLQ($last_name) . "," .
                   $ctrl->MYSQLQ('1' ) . "," .
                   $ctrl->MYSQLQ($email ) . "," .
                   $ctrl->MYSQLQ($phone_db ) . "," .
                   $ctrl->MYSQLQ($pwd ) . "," .
                   $ctrl->MYSQLQ($notify_email) . "," .
                   $ctrl->MYSQLQ($notify_text) . "," .
                   $ctrl->MYSQLQ($salt) . "," .
                   $ctrl->MYSQLQ($sid) . "," .
                   $ctrl->MYSQLQ("N") ;


         $sql = "insert into user ( $fields ) values ( $values ) ";
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         {
            $p_data["message"]= "good one ";
            $p_data["success"]= "true";

            // send email
            $dt=array();
            $dt["to"] = $email;
            $dt["subject"] = "Verification email from Aclaime" ;
            $dlim="\r\n\r\n";
            $l_url="http://" . $_SERVER['SERVER_NAME']. "/index.php?verifyid=" . $sid;
            $dt["message"] = "Hi $first_name, " . $dlim.  
"Thank you for registering with Aclaime Online and for expressing interest in Borrowers on our Site. Aclaime Online must verify each Lender on our site and you may be restricted in terms of access to Borrower information or functionality until this process is completed. You may also be required to provide additional personal or business information and identification prior to viewing information on Aclaime Online. Aclaime Online, partner or affiliate may be required to contact you as part of the verification process." . $dlim.
"Please click the following link to verify the account "   . $dlim . 
$l_url .  $dlim.
"Thank you, ". $dlim. 
"The Aclaime Online Team ". $dlim . $dlim;
            $ctrl->sendmail($dt);
         }
         else
         {
            $p_data["message"]= $ctrl->getServerError();
            $p_data["success"]= "false";
         }
         } // no duplicate
     return $p_data;
     }  // signup

     public function verifyUser($p_sid)
     {
         $ctrl = Controller::get(); 
         $sql = "select COALESCE(max(id) ,0) from user where sid = " . $ctrl->MYSQLQ($p_sid); 
         $l_id = $ctrl->getRecordID($sql);
         if ( $l_id > 0 ) 
         {
             $l_rec = $this->getUserRecord($l_id);
             $l_verified = $l_rec["verified"];
             if ( $l_verified  ==  "Y" ) 
             {
                    $l_res=-1;
             }
             else
             {
                  $sql = "update user set  status = " . 
                         $ctrl->MYSQLQ($this::$user_email_verified) .", verified = 'Y' where sid = " . 
                         $ctrl->MYSQLQ($p_sid); 
                  $l_res  = $ctrl->execute($sql);
             }
         }
         else
         {
             $l_res  = "0";
         }

         return $l_res;
  }
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
         //$l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');
         $l_first_name=$_POST["profile_firstname"];
         $l_user_id =$_POST["profile_user_id"];
         $l_calledby=$_POST["profile_calledby"];
         $l_last_name=$_POST["profile_lastname"];
         $l_email=$_POST["profile_email"];
         $l_phone=$_POST["profile_phone"];
         $l_phone_db= str_replace("-","",$l_phone);
         $notify=$_POST["profile_notify_email"];
         $notify_email ="N"; 
         if ( $notify == "true" )
             $notify_email ="Y"; 

         $notify=$_POST["profile_notify_text"];
         $notify_text ="N"; 
         if ( $notify == "true" )
             $notify_text ="Y"; 

         $l_res=false;
         $pwd=$_POST["profile_password1"];
         //$salt = $ctrl->createUniqueID();
         //$ha = hash('sha256', $salt . $pwd);
         $l_password=$ha;

          $l_isdup = $this->isduplicateemail($l_user_id,$l_email);
          if ( $l_isdup == 1 )
          {
               $p_data["message"]= "Duplicate email ....please check";
               $p_data["success"]= "false";
               return$p_data;
          }


         if ( $l_calledby == "admin" )
         { 
                $l_user_id=$_POST["profile_user_id"];
                $fieldlists =  
                     " updated_ts = " .  $ctrl->MYSQLQ($tm). "," .
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     " phone = " .  $ctrl->MYSQLQ($l_phone_db). "," .
                     " email = " .  $ctrl->MYSQLQ($l_email). "," .
                     " first_name = " .  $ctrl->MYSQLQ($l_first_name). "," .
                     " last_name = " .  $ctrl->MYSQLQ($l_last_name). "," .
                     $this->buildwithpost("status","profile_status") . "," .
                     $this->buildwithpost("role","profile_role") . "," .
                     $this->buildwithpost("address1","profile_address1") . "," .
                     $this->buildwithpost("address2","profile_address2") . "," .
                     $this->buildwithpost("city","profile_city") . "," .
                     $this->buildwithpost("state","profile_state") . "," .
                     $this->buildwithpost("zipcode","profile_zipcode") . "," .
                     $this->buildwithpost("country","profile_country") . "," .
                     " notify_email = " .  $ctrl->MYSQLQ($notify_email). "," .
                     " notify_text = " .  $ctrl->MYSQLQ($notify_text) ;

         $l_status=$_POST["profile_status"];
         if ( $l_status == 2 || $l_status == 3 || $l_status == 4 )
            $fieldlists .=  ",".  " verified  = " .  $ctrl->MYSQLQ("Y");
         if ( $l_status == 1 )
            $fieldlists .=  ",".  " verified  = " .  $ctrl->MYSQLQ("N");

         if ( strlen($l_password) > 0 ) 
         {
            $sid = $ctrl->createUniqueID();
            $salt = $ctrl->createUniqueID();
            $n_pwd = hash('sha256', $salt . $l_password);
            $fieldlists .=  ",".
              " sid = " .  $ctrl->MYSQLQ($sid). "," .
              " salt = " .  $ctrl->MYSQLQ($salt). "," .
              " password = " .  $ctrl->MYSQLQ($l_password);
         }

         $sql= "update user set  " . $fieldlists . 
                       " where id  = " . $ctrl->MYSQLQ($l_user_id);
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
         }


         }  // if calledby user 
         if ( $l_calledby == "user" )
         { 
              $sql = "select COALESCE(max(id) ,0) from user where email = " . $ctrl->MYSQLQ($l_email); 
              $l_id = $ctrl->getRecordID($sql);
              if ( $l_id > 0 && $l_user_id != $l_id ) 
              {
                 $p_data["message"]= "Email Address already Registered ";
                 $p_data["success"]= "false";
                 return $p_data;
              }
                $fieldlists =  
                     " updated_ts = " .  $ctrl->MYSQLQ($tm). "," .
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     " phone = " .  $ctrl->MYSQLQ($l_phone_db). "," .
                     " email = " .  $ctrl->MYSQLQ($l_email). "," .
                     " first_name = " .  $ctrl->MYSQLQ($l_first_name). "," .
                     " last_name = " .  $ctrl->MYSQLQ($l_last_name). "," .
                     $this->buildwithpost("address1","profile_address1") . "," .
                     $this->buildwithpost("address2","profile_address2") . "," .
                     $this->buildwithpost("city","profile_city") . "," .
                     $this->buildwithpost("state","profile_state") . "," .
                     $this->buildwithpost("zipcode","profile_zipcode") . "," .
                     $this->buildwithpost("country","profile_country") . "," .
                     " notify_email = " .  $ctrl->MYSQLQ($notify_email). "," .
                     " notify_text = " .  $ctrl->MYSQLQ($notify_text) ;
         if ( strlen($l_password) > 0 ) 
         {
            $sid = $ctrl->createUniqueID();
            $salt = $ctrl->createUniqueID();
            $n_pwd = hash('sha256', $salt . $l_password);
            $fieldlists .=  ",".
              " sid = " .  $ctrl->MYSQLQ($sid). "," .
              " salt = " .  $ctrl->MYSQLQ($salt). "," .
              " password = " .  $ctrl->MYSQLQ($l_password);
         }

         $sql= "update user set  " . $fieldlists . 
                       " where id  = " . $ctrl->MYSQLQ($l_user_id);
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
         }
         }  // if calledby user 


         return $p_data;
    }  // signup
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
           $sql = "select *  from user order by last_name";
           return $ctrl->query($sql);
    }
    public function  buildwithpost($fld,$id)
    {
         $ctrl = Controller::get(); 
         return $this->buildwithval($fld,$ctrl->getPostParamValue($id));
    }
    public function  buildwithval($fld,$val)
    {
         return " $fld = "  . $this->MYSQLQ($val);
    }
   public function MYSQLQ($val) 
   {     
      //$val=mysql_real_escape_string($val);
      return "'". $val. "'";
   }

  public function saveUserAgreement()
  {
         $l_res="";
         $p_data = array();
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();

         $sql = "select * from agreement ";
         $l_record  = $ctrl->getRecord($sql);
         $l_versionno=$l_record["versionno"];

         $l_cur_page = $ctrl->getPostParamValue("useragreement_cur_page");
           
         $sql = "select COALESCE(max(user_agreement_id) ,0) from user_agreement where "  . 
                 " user_id = " . $this->MYSQLQ($l_user_id) .
                 " and versionno = " . $this->MYSQLQ($l_versionno) ;
         $l_user_agreement_id  = $ctrl->getRecordID($sql);
         $tm =  date('Y-m-d H:i:s');
         if ( $l_user_agreement_id  == 0 ) 
         {
             $tm =  date('Y-m-d H:i:s');
             $fields= "created_user_id,versionno,updated_user_id,user_id,created_ts";
             $values= $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($l_versionno) . "," .
                      $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($tm);
             $sql = "insert into user_agreement ( $fields ) values ( $values ) ";
             $l_user_agreement_id = $ctrl->execute($sql);
             if ( $l_user_agreement_id == 0 ) 
             { 
                   $p_data["message"]= "Agreement save error ";
                   $p_data["success"]= "false";
                   return $p_data;
             }
             $sql = "select COALESCE(max(user_agreement_id) ,0) from user_agreement where "  . 
                     " user_id = " . $this->MYSQLQ($l_user_id) .
                     " and versionno = " . $this->MYSQLQ($l_versionno) ;
             $l_user_agreement_id  = $ctrl->getRecordID($sql);
             if ( $l_user_agreement_id == 0 ) 
             { 
                   $p_data["message"]= "Agreement save error ";
                   $p_data["success"]= "false";
                   return $p_data;
             }
         }
         $l_sql=" delete from user_agreement_fields ". 
                 " where user_agreement_id  = " . $ctrl->MYSQLQ($l_user_agreement_id) .
                 "   and page_no   = " . $ctrl->MYSQLQ($l_cur_page) ;
         $lid =  $ctrl->execute($l_sql);

         foreach ($_POST as $key => $value) {
                  if (strpos($key,"useragreement_fld_") !== false)
                  {

             $fields= "created_user_id,updated_user_id,created_ts,user_agreement_id,page_no,field_name,field_value";
             $values= $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($tm) . "," .
                      $ctrl->MYSQLQ($l_user_agreement_id) . "," .
                      $ctrl->MYSQLQ($l_cur_page) . "," .
                      $ctrl->MYSQLQ($key) . "," .
                      $ctrl->MYSQLQ($value);
             $sql = "insert into user_agreement_fields  ( $fields ) values ( $values ) ";
             $l_id = $ctrl->execute($sql);
             if ( $l_id == 0 ) 
             { 
                   $p_data["message"]= "User Agreement fields  save error ";
                   $p_data["success"]= "false";
                   return $p_data;
             }
             } // save fields
         }

         $fieldlists =  
               " updated_ts = " .  $ctrl->MYSQLQ($tm). "," .
               " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id);
         if ( $l_cur_page == 1 ) 
         {
                  $fieldlists .=   " ,agreement_1_accepted  =  'Y' " .
                     " ,agreement_1_date  =  " .  $ctrl->MYSQLQ($tm) ;
         }
         if ( $l_cur_page == 2 ) 
         {
                  $fieldlists .=   " ,agreement_2_accepted  =  'Y' " .
                     " ,agreement_2_date  =  " .  $ctrl->MYSQLQ($tm) ;
         }
         if ( $l_cur_page == 3 ) 
         {
                  $fieldlists .=   " ,agreement_3_accepted  =  'Y' " .
                     " ,isallaccepted  =  'Y' " .
                     " ,agreement_3_date  =  " .  $ctrl->MYSQLQ($tm) ;
         }
         $sql= "update user_agreement set  " . $fieldlists . 
                           " where user_agreement_id  = " . $ctrl->MYSQLQ($l_user_agreement_id) ;
         $l_id = $ctrl->execute($sql);

         if ( $l_id == 0 ) 
         { 
                   $p_data["message"]= "Agreement save error ";
                   $p_data["success"]= "false";
                   return $p_data;
         }
         if ( $l_cur_page == 3 ) 
         {
            $sql= "update user set status =  " . $ctrl->MYSQLQ($this::$user_terms_accepted) .
                           " where id  = " . $ctrl->MYSQLQ($l_user_id);
            $l_id = $ctrl->execute($sql);
            if ( $l_id == 0 ) 
            { 
                $p_data["message"]= "Agreement save error ";
                $p_data["success"]= "false";
                return $p_data;
            }
            $ctrl->resetsession($l_user_id);
         }
         $p_data["message"]= "Good one";
         $p_data["success"]= "true";
         return $p_data;
  }
  public function saveAgreement()
  {
         $l_res="";
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $l_versionno = $ctrl->getPostParamValue("agreement_versionno");
         $l_comments  = $ctrl->getPostParamValue("agreement_comments");
         $tm =  date('Y-m-d H:i:s');
         $l_res=false;

         $sql = "select * from agreement ";
         $l_record  = $ctrl->getRecord($sql);
         if ( count($l_record) == 0 ) 
         {
             $fields= "created_user_id,updated_user_id,versionno,comments,created_ts";
             $values= $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($l_versionno) . "," .
                      $ctrl->MYSQLQ($l_comments) . "," .
                      $ctrl->MYSQLQ($tm);
             $sql = "insert into agreement ( $fields ) values ( $values ) ";
             $l_id = $ctrl->execute($sql);
             if ( $l_id == 0 ) 
             { 
                   $p_data["message"]= "Agreement save error ";
                   $p_data["success"]= "false";
                   return $p_data;
             }
         }
         $fieldlists =  
               " comments = " .  $ctrl->MYSQLQ($l_comments). "," .
               " versionno = " .  $ctrl->MYSQLQ($l_versionno). "," .
               " updated_ts = " .  $ctrl->MYSQLQ($tm). "," .
               " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id);
         $sql= "update agreement set  " . $fieldlists ;
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 0 ) 
         { 
                $p_data["message"]= "Agreement save error ";
                $p_data["success"]= "false";
                return $p_data;
         }
         $fields= "created_user_id,updated_user_id,versionno,comments,created_ts";
         $values= $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($l_user_id) . "," .
                      $ctrl->MYSQLQ($l_versionno) . "," .
                      $ctrl->MYSQLQ($l_comments) . "," .
                      $ctrl->MYSQLQ($tm);
         $sql = "insert into agreement_history ( $fields ) values ( $values ) ";
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 0 ) 
         { 
                   $p_data["message"]= "Agreement save error ";
                   $p_data["success"]= "false";
                   return $p_data;
         }
         $p_data["message"]= "Good one";
         $p_data["success"]= "true";
         return $p_data;
    }
    public function getAgreementHistory()
    {
         $ctrl = Controller::get(); 
         $sql ="select a.created_user_id, a.created_ts, a.versionno, a.comments," .
               " u.first_name, u.last_name from user u, agreement_history a " .
                   " where u.id = a.created_user_id ".
                   " order by a.agreement_id desc ";
         return $ctrl->query($sql);
   }
   public function isduplicateemail($p_user_id,$p_email)
   {
         $ctrl = Controller::get(); 
         $p_data = array();
         $l_res=false;
         $sql = "select COALESCE(max(id) ,0) from user where email = " . $ctrl->MYSQLQ($p_email).
                " and id != $p_user_id " ;
         $l_id = $ctrl->getRecordID($sql);
         if ( $l_id  > 0 ) 
            return 1 ;
         else
            return 0 ;
    }

}
