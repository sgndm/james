<?php require_once("init_setup.php") ?>
<?php
    $ctrl = Controller::get();
    $type=$ctrl->getPostParamValue("type");
    $data = array();
    $data["message"]= "None";
    $data["success"]= "false";

    if ( $type == "compliance" )
    {
         $ux = UserModel::get(); 
         $data = $ux->PatientCompliance();
    }

    if ( $type == "contact" )
    {
         $kx = new ContactModel(); 
         $data = $kx->save();
    }

    if ( $type == "pushmedication" )
    {
         $ux = UserModel::get(); 
         $data = $ux->pushmedication();
    }
	if ( $type == "ignore" )
    {
         $ux = UserModel::get(); 
		 $id=$ctrl->getPostParamValue("patient");
         $data = $ux->ignore($id);
    }
    if ( $type == "pushsymptom" )
    {
         $ux = UserModel::get(); 
         $data = $ux->pushsymptom();
    }
	if ( $type == "pushimage" )
    {
         $ux = UserModel::get(); 
         $data = $ux->pushimage();
    }
    if ( $type == "reviewsymptoms" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething('symptomrecord_', 'user_activity', 'symptom_');
    }
	if ( $type == "reviewsymptomsnotify" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething_Notify('symptomrecord_', 'user_activity', 'symptom_', 'Your symptom has been reviewed.');
    }
	if ( $type == "reviewpainmeds" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething('painrecord_', 'user_activity', 'pain_');
    }
	if ( $type == "reviewpainmedsnotify" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething_Notify('painrecord_', 'user_activity', 'pain_', 'Your narcotics intake has been reviewed.');
    }
	if ( $type == "reviewvitals" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething('vitalrecord_', 'vitals_reported', 'vital_');
    }
	if ( $type == "reviewvitalsnotify" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething_Notify('vitalrecord_', 'vitals_reported', 'vital_', 'Your vitals have been reviewed.');
    }
	if ( $type == "reviewwounds" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething('woundrecord_', 'user_activity', 'wound_');
    }
	if ( $type == "reviewwoundsnotify" )
    {
         $ux = UserModel::get(); 
         $data = $ux->reviewSomething_Notify('woundrecord_', 'user_activity', 'wound_', 'Your images have been reviewed.');
    }
    if ( $type == "authenticate" )
    {
         $ux = UserModel::get(); 
         $data = $ux->authenticate();
    }

    if ( $type == "profile" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveProfile();
    }
    if ( $type == "allwoundcare" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllWoundcare();
    }
    if ( $type == "allphyact" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllPhyact();
    }
    if ( $type == "allsymptom" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllSymptom();
    }
    if ( $type == "allvideo" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllVideos();
    }
    if ( $type == "alldiet" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllDiet();
    }
    if ( $type == "alltask" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllTasks();
    }
    if ( $type == "allvitals" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllVitals();
    }
    if ( $type == "allmedication" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveAllMedication();
    }
    if ( $type == "organization" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveOrganization();
    }
    if ( $type == "clinic" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveClinic();
    }
    if ( $type == "savevideolist" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveVideoList();
    }
    if ( $type == "userappt" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveUserAppt();
    }
    if ( $type == "uservideo" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveUserVideo();
    }
    if ( $type == "video" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveVideo();
    }
	if ( $type == "vital" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveVital();
    }
    if ( $type == "symptom" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveSymptom();
    }
    if ( $type == "doctor" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveDoctor();
    }
    if ( $type == "cc" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveCC();
    }
    if ( $type == "saveuserdiet" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveUserDiet();
    }
    if ( $type == "savemedicationlist" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveMedicationList();
    }
    if ( $type == "savemedicationclasslist" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveMedicationClassList();
    }
    if ( $type == "savediagnosislist" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveDiagnosisList();
    }
    if ( $type == "savedefault" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveDefault();
    }
    if ( $type == "savephyactlist" )
    {
         $ux = UserModel::get(); 
         $data = $ux->savePhysicalActivityList();
    }
    if ( $type == "savewoundlist" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveWoundList();
    }
    if ( $type == "savedietlist" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveDietList();
    }
    if ( $type == "woundcare" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveWoundcare();
    }
    if ( $type == "saveuser" )
    {
         $ux = UserModel::get(); 
         $data = $ux->saveUser();
    }
	if ( $type == "resetpassword" )
    {
         $ux = UserModel::get(); 
         $data = $ux->resetPassword();
    }
    if ( $type == "savepatient" )
    {
         $ux = UserModel::get(); 
         $data = $ux->savePatient();
    }

   echo json_encode($data);
?>
