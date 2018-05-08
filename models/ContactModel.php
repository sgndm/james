<?php
class ContactModel
{ 

   public function __construct() 
   { 
   } 

   static $st_instance = null;
   public static function get()
   {
      if ( ContactModel::$st_instance == null  )
         ContactModel::$st_instance = new ContactModel();
      return ContactModel::$st_instance;
   }

   public function save()
   {
         $db = Controller::get();
         $p_data = array();
         $l_type= $db->getPostParamValue("contact_type");
         $l_res=false;
         $l_phone="";
         if ( $l_type == "contact" )
         {
              $l_name= $db->getPostParamValue("contact_name");
              $l_email= $db->getPostParamValue("contact_email");
              $l_message= $db->getPostParamValue("contact_message");
              $l_user_id= $db->getPostParamValue("contact_user_id");
              $l_phone= $db->getPostParamValue("contact_phone");
              $l_phone_db= str_replace("-","",$l_phone);

              $fields= "user_id,phone,name,email,message" ;
              $values= $db->MYSQLQ($l_user_id) .  "," .
                        $db->MYSQLQ($l_phone_db) . "," .
                        $db->MYSQLQ($l_name ) . "," .
                        $db->MYSQLQ($l_email ) . "," .
                        $db->MYSQLQ($l_message) ;
         }
         else
         {
              $l_name= $db->getPostParamValue("minicontact_name");
              $l_email= $db->getPostParamValue("minicontact_email");
              $l_message= $db->getPostParamValue("minicontact_message");
              $l_user_id= $db->getPostParamValue("minicontact_user_id");

              $fields= "user_id,name,email,message" ;
              $values= $db->MYSQLQ($l_user_id) .  "," .
                        $db->MYSQLQ($l_name ) . "," .
                        $db->MYSQLQ($l_email ) . "," .
                        $db->MYSQLQ($l_message) ;
         }

         $sql = "insert into support_request  ( $fields ) values ( $values ) ";
         $l_id = $db->execute($sql);
         if ( $l_id == 1 ) 
         {
            $p_data["message"]= "good one ";
            $p_data["success"]= "true";

            // send email
            $dlim="\r\n\r\n";
            $dt=array();
            $dt["to"] = $l_email;
            $dt["subject"] = "Support Request created " ;
            $dt["message"] = "Dear $l_name, " . $dlim.  $dlim ;

            if ( strlen($l_phone)  > 0 ) 
                $dt["message"] .= "phone : " . $l_phone . $dlim ;

            $dt["message"] .= "message : " . $l_message . $dlim .
                              "thanks your your online submission, someone will be in touch within 1 business day" . $dlim.
                             "http://" . $_SERVER['SERVER_NAME']. "/index.php" . $dlim.
                             "Aclaime Support Team"  . $dlim;

            $db->sendmail($dt);
         }
         else
         {
            $p_data["message"]= $db->getServerError();
            $p_data["success"]= "false";
         }
         return $p_data;
  }
} // ContactModal
?>
