<?php require_once("init_setup.php") ?>
<?php
   require_once("docusign_login.php");

   $l_envelope_id=$_POST["param1"];
   $IntegratorsKey = "KNOW-3c87c6af-0daa-41c8-9e17-23cfda488774";
   $ls= login();
   if ( $ls == 1 )
   { 
       $_SESSION["LoggedIn"] = True;
       $l_url="http://192.241.207.50:82/DocuSignSample/getpdf.php?envelopeid=$l_envelope_id";
       header("Location: $l_url");
   } 
