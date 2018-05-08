<?php
class CodeModel
{ 
   public function __construct() 
   { 
   } 

   public static $user_email_verified_text="Email Verified";
   public static $user_phone_verified=3;

   static $st_instance = null;

   public static function get()
   {
      if ( CodeModel::$st_instance == null  )
         CodeModel::$st_instance = new CodeModel();
      return CodeModel::$st_instance;
   }
 
   public function getAllRecord($p_type)
   {
         $ctrl = Controller::get(); 
         $sql = "select *  from code where type = " . $ctrl->MYSQLQ($p_type) .
                " order by name ";
         return $ctrl->query($sql);
   }


   public function getList($p_type)
   {
         $ctrl = Controller::get(); 
         $sql = "select *  from code where type = " . $ctrl->MYSQLQ($p_type) .
                " and isactive = 'Y' order by name ";
         $all_data= $ctrl->query($sql);
         $tot_rows=count($all_data);
         $dlim="";
         $l_str="";
         for ($r_idx=0;$r_idx<$tot_rows ;$r_idx++)
         {
            $p_data=$all_data[$r_idx];
            $name  = $p_data["name"];
            $l_str .= $dlim .$name;
            $dlim=",";
         }
         return $l_str;
   }

   public function getRecord($p_id)
   {
         $ctrl = Controller::get(); 
         $sql = "select *  from code where id = " . $ctrl->MYSQLQ($p_id) .
                " order by name ";
         return $ctrl->getRecord($sql);
   }

   public function saveCode()
   {
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         $p_data = array();
         $tm =  date('Y-m-d H:i:s');
         $l_id=$_POST["code_id"];
         $l_type=$_POST["code_type"];
         $l_name    =$_POST["code_name"];
         $l_description=$_POST["code_description"];
         $l_isactive=$_POST["code_isactive"];
         $c_isactive ="N"; 
         if ( $l_isactive == "true" )
             $c_isactive ="Y"; 

         if ( $l_id > 0  )
         {
                $fieldlists =  
                     " isactive = " .  $ctrl->MYSQLQ($c_isactive). "," .
                     " updated_ts = " .  $ctrl->MYSQLQ($tm). "," .
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     " description = " .  $ctrl->MYSQLQ($l_description);
                 $sql= "update code set  " . $fieldlists . 
                               " where id  = " . $ctrl->MYSQLQ($l_id);
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
         }  
         else 
         {  
              $fields= "type,name,description,created_user_id,updated_user_id,isactive,created_ts";
               $values= $ctrl->MYSQLQ($l_type) . "," .
                        $ctrl->MYSQLQ($l_name) . "," .
                        $ctrl->MYSQLQ($l_description) . "," .
                        $ctrl->MYSQLQ($l_user_id) . "," .
                        $ctrl->MYSQLQ($l_user_id) . "," .
                        $ctrl->MYSQLQ($c_isactive) . "," .
                        $ctrl->MYSQLQ($tm);
             $sql = "insert into code ( $fields ) values ( $values ) ";
             $l_id = $ctrl->execute($sql);
             if ( $l_id == 0 ) 
             { 
                   $p_data["message"]= "Agreement save error ";
                   $p_data["success"]= "false";
                   return $p_data;
             }
         }  
         $p_data["message"]= "Saved" ;
         $p_data["success"]= "true";
         $p_data["type"]= $l_type;
         return $p_data;
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

}
