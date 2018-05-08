<?php require_once("init_setup.php") ?>
<?php $NAV_OVERRIDE='Investments'; ?>
<?php include('head.php') ?>
<?php
    $ctrl = Controller::get(); 
    $l_patient_id = $ctrl->getPostParamValue("param1");
    $ux = UserModel::get(); 
    $ux->cleanuplog($l_patient_id,"symptom");
?>
