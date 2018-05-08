<?php include("include/kb_include.php") ?>
<?php

class Base 
{ 
	public function __construct() 
   { 
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
      $ct = ConstantModel::get(); 
     $p1= $ct->getMYSQL();
     $host = $p1["server"];
     $user=$p1["user"];
     $password=$p1["password"];
     $database=$p1["database"];
     try 
     {
     $con=mysqli_connect($server,$user,$password,$database);
       if (mysqli_connect_errno())
       {
           throw new Exception("Failed to connect to MySQL: " . mysqli_connect_error());
       }
      $val=mysqli_real_escape_string($con, $val);
     }
     catch (Exception $e) 
    {
        $contr_result=  "ERROR ".  $e->getMessage() .  "\n";
        $db_result = null;
    } 
      return "'". $val. "'";
   }
	public function getRecords($p_obj,$t_sql)
   {
     $res="";
     $sql = $t_sql;
     $ctrl = Controller::get();
     $recall = $ctrl->query($sql);
     $t_rec=count($recall);
     $l_str = "";
     if ( $t_rec > 0 )
     {
        for ($r_idx = 0; $r_idx < $t_rec; $r_idx++)
        {
          $rec  = $recall[$r_idx];
          $p_obj->{"record"}[$r_idx]=$rec;
        }
        $p_obj->{"status"}="success";
        $p_obj->{"message"}= "success";
     }
     else
     {
        $p_obj->{"status"}="failure";
        $p_obj->{"message"}= "DB_ERROR";
     }
     $p_obj->{"total_records"}=$t_rec;
     return $p_obj;
   } // 
	public function getOnlyRecords($t_sql)
   {
       $res="";
     $sql = $t_sql;
     $ctrl = Controller::get();
     $recall = $ctrl->query($sql);
     $t_rec=count($recall);
     $l_str = "";
	 $data='{"total_records":"0"}';
     $p_obj=json_decode($data);
     if ( $t_rec > 0 )
     {
        for ($r_idx = 0; $r_idx < $t_rec; $r_idx++)
        {
          $rec  = $recall[$r_idx];
          $p_obj->{"record"}[$r_idx]=$rec;
        }
     }
     $p_obj->{"total_records"}=$t_rec;
     return $p_obj;
   }
	public function Q($val) 
   { 
      return "'". $val. "'";
   } 
	public function getAllFromUserType($userType)
   {
       $sql = "SELECT id FROM user WHERE user_type = $userType";
       return $this->getRecordIds($sql);
   }
	public function doIHavePermission($userType, $type) 
   {
       /* 
        * TYPES I HAVE MADE:
        * add_patient
        * add_clinic
        * add_doctor
        * save_doctor
        * save_clinic
        * clinic_list
        * doctor_list
        * admin_list
        * pushlog_list
        * organization_list
        * video_list
	    * vital_list
        * symptom_list
        * diet_list
        * activity_list
        * medication_list
	    * medication_class_list
        * diagnosis_list
        * cc_list
        * add_cc
        * save_cc
        * wound_list
        * defaults
        * */
        
       $permission = 0;
       if($userType == "1")//Admin
       {
           $permission = 1;
       }
       else if($userType == "2")//Clinic
       {
           if($type == "cc_list" ||
            $type == "doctor_list" || $type == "pushlog_list" || 
            $type == "organization_list" || $type == "save_cc" || 
            $type == "add_cc" || $type == "add_patient" || 
            $type == "wound_list" || $type == "diagnosis_list" || 
            $type == "defaults" || $type == "vital_list" || 
			$type == "medication_class_list" || $type == "add_doctor")
           {
               $permission = 1;
           }
       }
       else if($userType == "3")//Doctor
       {
           if($type == "cc_list" ||
            $type == "doctor_list" || $type == "pushlog_list" || 
            $type == "organization_list" || $type == "clinic_list"
            || $type == "add_patient" || $type == "wound_list" )
            {
                $permission = 1;
            }
       }
       else //Care Coordinator
       {
           if($type == "cc_list" ||
            $type == "doctor_list" || $type == "pushlog_list" || 
            $type == "organization_list" || $type == "add_patient" || 
            $type == "wound_list")
            {
                $permission = 1;
            }
       }
       return $permission;
   }
	public function logme($mess)
  {
     $ctrl = Controller::get();
     $ctrl->logme($mess);
  }
	public function squote($str)
{
   return "'".$str."'";
} # qry
	public function hasValue($str)
{
  if ( is_null ( $str ) || strlen(trim($str)) == 0 || trim($str) == "" )
      return FALSE;
  return TRUE;
}
	public function getSymptom_list($p_obj)
   {
     $ctrl = Controller::get();
     $t_id= $p_obj->{"record_id"};
     $sql = "select * from symptoms_list ";
     if ( $t_id !=null && $t_id > 0 ) 
             $sql .= " where id  = $t_id ";
     return $this->getRecords($p_obj,$sql);
   } // 
	public function getTableRecord($p_obj,$p_table,$p_keyname,$p_keyval,$p_where=null)
  {

         $ctrl = Controller::get(); 
         $sql = "select *  from $p_table  where 1=1 " ;
         if ( $p_keyname != null && $p_keyval != null )
            $sql .= " and $p_keyname = " . $ctrl->MYSQLQ($p_keyval);
         if ( $p_where != null && $p_where.length > 0 ) 
            $sql .= " and " . $p_where;
         return $this->getRecords($p_obj,$sql);
  }
	public function getRecord($k_1_sql)
  {
     $ctrl = Controller::get();
     return $ctrl->getRecord($k_1_sql);
  }
	public function getRecordIds($k_1_sql)
  {
     $ctrl = Controller::get();
     return $ctrl->getRecordIds($k_1_sql);
  }
	public function submit($sqlToCall)
  {
      $ctrl = Controller::get();
      return $ctrl->execute($sqlToCall);
  }
   
}
