<?php
class ConstantModel
{
   public function __construct()
   {
   }

   public static $user_email_verified_text="Email Verified";
   public static $user_phone_verified=3;

   static $st_instance = null;

   public static function get()
   {
      if (ConstantModel::$st_instance == null)
         ConstantModel::$st_instance = new ConstantModel();
      return ConstantModel::$st_instance;
   }

   public function getMYSQL()
   {
        $out["server"] ="localhost";
        $out["user"] = "root";
        $out["password"] = "dinu@IS#054#";
        // $out["password"] = "";
        $out["database"]= "mobimbd";

        return $out;
   }

    public function getTitle()
   {
        return "Mobimd";
   }

   // added by dinesh 0n 11/05/2018
   public function getsmsgatewayaddress() {

   }

   public function getUploaddir() {
     $path = "photo/patient_img/";
     return $path;
   }

   public function getSendGrid() {

   }



}
