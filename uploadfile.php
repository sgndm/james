<?php 
   require_once("init_setup.php");
{
   require_once  ( 'include/kb_include.php');
   $ctrl = Controller::get();
   $l_record_id = $_POST["record_id"];
   $l_formname = $_POST["formname"];
   $l_unique_id = $_POST["unique_id"];


   $l_file_type = $_POST["file_type"];
   $ux = UserModel::get();
   $l_patient = "";
   if($l_formname == "patient")
   {
   	 $l_patient = "p";
   }
   $const = ConstantModel::get();
   $l_uploaddir = $const->getUploaddir();
   $l_dir = $l_uploaddir . $l_record_id . $l_patient;
   if ( $l_record_id == 0 ) 
      $l_dir = $l_uploaddir . $l_unique_id . $l_patient;
   if (!is_dir($l_dir)) 
   {
       mkdir($l_dir, 0775);
   }
       
   
   $l_dir .= "/";
   $info = pathinfo($_FILES['file']['name']);
   $ext = $info['extension']; 
   $newname = "photo.".$ext; 
   $l_url = "/uploads/" . $l_record_id ."/". $newname;
   $target = $l_dir . $newname;
   $fileToMove = $_FILES['file']['tmp_name'];
   
 
   if (file_exists($target))
   {
       rename($target, $l_dir."oldPhoto.".$ext);
   }
   
   if(move_uploaded_file($fileToMove, $target))
   {
         $px = array();
         $px["record_id"]  = $l_record_id;
         $px["file_path"]  = $target;
         $px["formname"]  = $l_formname;
         $px["url"]  = $l_url;
         if ( $l_record_id > 0 ) 
            $ux->saveUserImage($px);
   }
   else {
       $l_dt =  date('Y-m-d H:i:s');
   $ctrl->logme("$l_dt file copied failed move_uploaded_file( ".$_FILES['file']['tmp_name'] ." ,  ".$target );
      echo "Sorry, there was a problem uploading your file.";
   }
}
?>
