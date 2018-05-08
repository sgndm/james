<?php require_once("init_setup.php") ?>
<?php

    $ctrl = Controller::get(); 
   
   $l_user_id= $ctrl->getUserID();
   $l_name =$_POST["param1"];
   $l_product_id =$_POST["param2"];
   if ( $l_product_id > 0 )
   { 
       $file_name="docusign.pdf";
       $l_url="http://" . $_SERVER["SERVER_NAME"] . "/uploads/".
               $l_product_id."/" .  $l_user_id."/".$file_name;
       $pdfTitle = "Signed document";
       $output = file_get_contents($l_url);
       header('Content-type: application/pdf');
       $l_flname = $l_name . ".pdf";
       header('Content-Disposition: attachment; filename=' . $l_flname);
       echo $output;
       exit;
   } 
?>
