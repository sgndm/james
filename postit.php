<?php require_once("init_setup.php") ?>
<?php
   require_once  ( 'include/kb_include.php');
   $ctrl = Controller::get();
   $l_user_id = $_POST["user_id"];
   $ux = UserModel::get();
   $dx = DataModel::get();
   $l_dir = "/var/www/mobimd/uploads/" . $l_user_id . "p";
   if (!is_dir($l_dir)) 
       mkdir($l_dir, 0775);
   
   try{
   $l_dir .= "/";
   $l_file_name = basename( $_FILES['file']['name']) ;
   $l_url = "/uploads/" . $l_user_id ."p/". $l_file_name;
   $target = $l_dir . $l_file_name;
   $thumbTarget = $l_dir . "t_" . $l_file_name;
   $info = pathinfo($_FILES['file']['name']);
   $ext = substr( $l_file_name, strrpos( $l_file_name, '.' )+1 ); 
   $name = $_FILES['file']['tmp_name'];
   $error = $_FILES['file']['error'];
   
   if(move_uploaded_file($name, $target))
   {
         $px = array();
         $px["file_name"]  = $l_file_name;
         $px["file_path"]  = $target;
         $px["file_type"]  = "wound";
         $px["url"]  = $l_url;
         $px["description"]  = "wound";
         $px["user_id"]  = $l_user_id;
         $ux->saveUploadfile($px);
		 $dx->make_thumb($target, $thumbTarget, $ext);
         echo "Image Successfully Uploaded";
   }
   else {
      echo "Sorry, there was a problem moving your file to the server.";
   }
   }
   catch(Exception $e)
   {
       echo "Sorry, there was a problem uploading your file.";
   }
?>

