<?php require_once("init_setup.php") ?>
<?php
   require_once  ( 'include/kb_include.php');
   $ctrl = Controller::get();
   $l_id = $ctrl->getSessionParamValue("mobile_patient_id");
   $l_id = $ctrl->getSessionParamValue("mobile_user_id");
   $datamodel = DataModel::get();

   $data=$_POST['data'];
   $data=base64_decode($data);
   $obj=json_decode($data);
   $type =  $obj->{'type'};
   $type= strtoupper($type);

  $obj->{"status"}="";
  $obj->{"message"}="";
  $type =  $obj->{'type'};
  $type =  $obj->{'type'};
  $type= strtoupper($type);


  if ( $type == "ISALIVE" )
     $obj = $datamodel->isAlive($obj);

  if ( $type == "AUTHENTICATE" )
     $obj = $datamodel->mobileauthenticate($obj);

  if ( $type == "SAVEVITALS" )
     $obj = $datamodel->savevitals($obj);

  if ( $type == "NEWSAVEVITALS" )
     $obj = $datamodel->savevitals02($obj);

  if ( $type == "GETHL7DATA" )
     $obj = $datamodel->getHl7Data($obj);

  if ( $type == "SAVEUSER" )
     $obj = $datamodel->saveUser($obj);

  if ( $type == "SAVESYMPTOMNOTE" )
  {
     $obj = $datamodel->createSymptomLog($obj);
  }
  if ( $type == "SAVEMEDICALNOTE" )
  {
     $obj = $datamodel->createMedicalLog($obj);
  }

  if ( $type == "SAVEUSER" )
     $obj = $datamodel->saveUser($obj);

  if ( $type == "GETPATIENT" )
     $obj = $datamodel->getPatient($obj);

  if ( $type == "GETZONE" )
     $obj = $datamodel->getZone($obj);

  if ( $type == "SAVEZONE" )
     $obj = $datamodel->saveZone($obj);

  if ( $type == "VIDEOLOG" )
     $obj = $datamodel->createVideoLog($obj);

  if ( $type == "GETAPPOINTMENTS" )
     $obj = $datamodel->getAppointments($obj);

  if ( $type == "GETNOTIFICATIONS" )
     $obj = $datamodel->getPushNotificationlog($obj);

  if ( $type == "GETMOBILEHOMEPAGEALERT" )
     $obj = $datamodel->getmobilehomepagealert($obj);

  if ( $type == "GETMEDICATIONS" )
  {
     	$isactive = "Y";
     	$obj->{'isactive'} = $isactive;
     $obj = $datamodel->getMedications($obj);
  }

  if ( $type == "GETPUSHMEDICATIONS" )
     $obj = $datamodel->getPushMedications($obj);

  if ( $type == "GETMEDS" )
     $obj = $datamodel->getMedications($obj);

  if ( $type == "GETPHYSICALACTIVITY" )
  {

  	$isactive = "Y";
    $obj->{'isactive'} = $isactive;
    $obj = $datamodel->getPhysicalActivityMobile($obj);
  }

  if ( $type == "GETDIAGNOSIS" )
     $obj = $datamodel->getDiagnosis($obj);

  if ( $type == "GETDIET" )
     $obj = $datamodel->getDiet($obj);

  if ( $type == "GETVIDEOS" )
     $obj = $datamodel->getVIdeos($obj);

  if ( $type == "GETVITALS" )
     $obj = $datamodel->getVitals($obj);

  if ( $type == "GETWOUNDIMAGES" )
     $obj = $datamodel->getWoundImages($obj);

  if ( $type == "GETFAQS" )
     $obj = $datamodel->getFaqs($obj);

  if ( $type == "GETSYMPTOMS" )
     $obj = $datamodel->getSymptomsmobile($obj);

  if ( $type == "GETWOUNDCARE" )
     $obj = $datamodel->getWoundcare($obj);

  if ( $type == "GETMENUITEM11" )
  {
     $obj->{"status"}="success";
     $obj->{"message"}="success";
     $menu_id =  $obj->{'menu_id'};
     $handle = fopen("menu.list", "r");
     if ($handle) {
         $i=0;
         while (($line = fgets($handle)) !== false) {

         $line .=",,,,";
         $fld=explode(",",$line);
         if ( $fld[0] == $menu_id )
         {
            $obj->{"imagefilename"}[$i]= $fld[1];
            $obj->{"menuitem"}[$i]= $fld[2];
            $obj->{"callfunc"}[$i]= $fld[3];
            $obj->{"callparam"}[$i]= $fld[4];
            $i++;
         }
         }
     }
  }

  if ( $type == "RECOVER" )
     $obj = $datamodel->mobilerecoverpwd($obj);

  if ( $type == "VERSION" )
     $obj = $datamodel->getVersion($obj);

  if ( $type == "GETMENUITEM" )
  {
     $obj->{"status"}="success";
     $obj->{"message"}="success";
     $menu_id =  $obj->{'menu_id'};
     $handle = fopen("menu.list", "r");
     if ($handle) {
         $i=0;
         while (($line = fgets($handle)) !== false) {

         $line .=",,,,";
         $fld=explode(",",$line);
         if ( $fld[0] == $menu_id )
         {
            $obj->{"imagefilename"}[$i]= $fld[1];
            $obj->{"menuitem"}[$i]= $fld[2];
            $obj->{"callfunc"}[$i]= $fld[3];
            $obj->{"callparam"}[$i]= $fld[4];
            $i++;
         }
         }
     }
  }
  $dt = json_encode($obj) ;
  echo "$dt";
?>
