<?php require_once("init_setup.php") ?>
<?php include('head.php') ?>
<?php
    $ctrl = Controller::get(); 
    $l_user_id= $ctrl->getUserID();

    $p_curtab = $ctrl->getPostParamValue("current_tab");
    $p_called_by = $ctrl->getPostParamValue("called_by");
	$p_patient_id = $ctrl->getPostParamValue("record_id");
	//$p_curtab = $ctrl->getGetParamValue("tab");
    //$p_called_by = $ctrl->getGetParamValue("called");
	//$p_patient_id = $ctrl->getGetParamValue("patient");
    $_SESSION["patient_called_by"]=$p_called_by;

    if ( $p_curtab  == null || $p_curtab  == "none" ) 
       $p_curtab  = "compliance";
    if ( $p_called_by == "addpatient" )
       $p_curtab  = "medication";

    $p_old_patient=1;
    if ( $p_called_by == "addpatient" )
      $p_old_patient=0;
    $_SESSION["old_patient"]=$p_old_patient ;

    $p_active_compliance= "";
    $p_active_profile  = "";
    $p_active_medication = "";
    $p_active_vitals= "";
	$p_active_vitalgraph= "";
    $p_active_woundcare  = "";
    $p_active_woundimage  = "";
    $p_active_task  = "";
    $p_active_diet  = "";
    $p_active_videos   = "";
    $p_active_appt     = "";
    $p_active_symptom  = "";
    $p_active_phyact   = "";

    if ( $p_curtab  == "compliance" )
        $p_active_compliance  = "active";
    if ( $p_curtab  == "profile" )
        $p_active_profile  = "active";
    if ( $p_curtab  == "medication" )
        $p_active_medication = "active";
    if ( $p_curtab  == "vitals" )
        $p_active_vitals  = "active";
	if ( $p_curtab  == "vitalgraph" )
        $p_active_vitalgraph  = "active";
    if ( $p_curtab  == "woundimage" )
        $p_active_woundimage  = "active";
    if ( $p_curtab  == "woundcare" )
       $p_active_woundcare  = "active";
    if ( $p_curtab  == "diet" )
    $p_active_diet  = "active";
    if ( $p_curtab  == "task" )
    $p_active_task  = "active";
    if ( $p_curtab  == "videos" )
    $p_active_videos   = "active";
    if ( $p_curtab  == "appt" )
    $p_active_appt     = "active";
    if ( $p_curtab  == "symptom" )
    $p_active_symptom  = "active";
    if ( $p_curtab  == "phyact" )
    $p_active_phyact   = "active";

    
    if ( $p_patient_id == null || $p_patient_id == "" )
       $p_patient_id = "0";
    if ( $p_patient_id > 0 ) 
    {
        $_SESSION["view_patient_id"]=$p_patient_id ;
    }
    $p_patient_id = $_SESSION["view_patient_id"];
    if ( $p_patient_id == null || $p_patient_id == "" )
       $p_patient_id = "0";
    if ( $p_patient_id == 0 ) 
    {
        header("Location: myhome.php");
        return;
    }
    $user_id= $ctrl->getUserID();

    $data='{"segment":"pid"}';
    $ux = UserModel::get(); 
    $p_rec = $ux->getPatient($p_patient_id);
    
    $l_photourl = $p_rec["photourl"];

    $l_name = $p_rec["first_name"]. " ".
                $p_rec["last_name"];
                
    $l_discharge_diagnosis = $p_rec["discharge_diagnosis"];
    $l_discharge_diagnosis = $ux->getDiagnosis($l_discharge_diagnosis);
    $l_surgical_procedure = $p_rec["surgical_procedure"];
    $l_medical_record_url = $p_rec["medical_record_url"];
    $l_date_of_discharge = $p_rec["date_of_discharge"];
    $l_date_of_discharge_fmt = $p_rec["date_of_discharge_fmt"];
    if ( $l_date_of_discharge_fmt == "00/00/0000" )
       $l_date_of_discharge_fmt ="";

?>
<input type="hidden" id="record_patient_id" value="<?php echo $p_patient_id; ?>">
	
				
<BR/>
<div class="row" >
<div class="span2"> 

   <img style="height:10%;width:100%"  src="<?php echo $l_photourl; ?>" />
   <p align="center"> <font size=3 color="#B50128">
       <?php echo $l_name; ?></font></p>

   <p align="left"> <font size=3 color="black">
   Discharge Diagnosis </font></p>
   <p align="center"> <font size=3 color="#B50128">
       <?php echo $l_discharge_diagnosis; ?></font></p>

   <p align="left"> <font size=3 color="black">
   Surgical Procedure</font></p>
   <p align="center"> <font size=3 color="#B50128">
       <?php echo $l_surgical_procedure; ?></font></p>

   <?php if ($ctrl->hasValue($l_date_of_discharge) ) { ?>
   <p align="left"> <font size=3 color="black">
   Date of Discharge </font></p>
   <p align="center"> <font size=3 color="#B50128">
       <?php echo $l_date_of_discharge_fmt; ?></font></p>
   <?php } ?>

   <a href="uploads/medicalrecord.html" target="_blank" ><font size=2 color="blue">Medical Record</font></a>
   <BR/>
   <BR/>
   <BR/>


   <!--<img style="height:10%;width:100%"  src="assets/images/addpatient.png" onClick="callmenuitem('addpatient.php','')" />
   <BR/>
   <img style="height:10%;width:100%"  src="assets/images/analytics.png"
 onclick="callmenuitem('dashboard.php','')"  />-->
 

   <button class="btn btn-primary btn-block" style="height: 60px;" type="button"
   onClick="callmenuitem('addpatient.php','')" >
   	<i class="icon-plus"></i><i class="icon-user"></i> add patient
   </button>
   </BR>
   <button class="btn btn-primary btn-block" style="height: 60px;" type="button"
   onclick="callmenuitem('dashboard.php','')" >
   	<i class="icon-signal"></i> analytics
   </button>

   <!--img style="height:10%;width:100%"  src="assets/images/surgicaldatabase.png"
 onclick="callmenuitem('surgicaldatabase.php','')"  />
   <img style="height:10%;width:100%"  src="assets/images/meddatabase.png"
 onclick="callmenuitem('meddatabase.php','')"  />
   <BR/>
   <BR/>
   <img style="height:10%;width:100%"  src="assets/images/team.png"
 onclick="callmenuitem('team.php','')"  /-->
</div>
<div class="span10">
<div class="span10">
				<ul class='nav nav-tabs' id='myTab' style="margin-top: 10px;">

<?php if ( $p_old_patient == 1 ) { ?>
					<li class='<?php echo $p_active_compliance; ?> '>
						<a href="#tab-compliance" data-toggle="tab">Compliance</a>
					</li>
<?php } ?>
					<li id="li-tab-profile" class='<?php echo $p_active_profile; ?> '>
						<a href="#tab-profile" data-toggle="tab">Profile</a>
					</li>
					<li id="li-tab-medication" class='<?php echo $p_active_medication; ?> '>
						<a href="#tab-medication" data-toggle="tab">Medication</a>
					</li>
					<li id="li-tab-vitals" class='<?php echo $p_active_vitals; ?> '>
                        <a href="#tab-vitals" data-toggle="tab">Vitals</a>
                    </li>
                    <li id="li-tab-vitalgraph" class='<?php echo $p_active_vitalgraph; ?> '>
                        <a href="#tab-vitalgraph" data-toggle="tab">Vital Charts</a>
                    </li>
					<li id="li-tab-woundcare" class='<?php echo $p_active_woundcare; ?> '>
						<a href="#tab-woundcare" data-toggle="tab">WoundCare</a>
					</li>
<?php if ( $p_old_patient == 1 ) { ?>
					<li class='<?php echo $p_active_woundimage; ?> '>
						<a href="#tab-woundimage" data-toggle="tab">Wound Image</a>
					</li>
<?php } ?>
					<!--<li id="li-tab-task" class='<?php echo $p_active_task; ?> '>
						<a href="#tab-task" data-toggle="tab">Tasks</a>
					</li>-->
					<li id="li-tab-diet" class='<?php echo $p_active_diet; ?> '>
						<a href="#tab-diet" data-toggle="tab">Diet</a>
					</li>
					<li id="li-tab-phyact" class='<?php echo $p_active_phyact; ?> '>
						<a href="#tab-phyact" data-toggle="tab">Phy Act</a>
					</li>
					<li id="li-tab-symptom" class='<?php echo $p_active_symptom; ?> '>
						<a href="#tab-symptom" data-toggle="tab">Symptom</a>
					</li>
					<li id="li-tab-appt" class='<?php echo $p_active_appt; ?> '>
						<a href="#tab-appt" data-toggle="tab">Appt</a>
					</li>
					<li id="li-tab-videos" class='<?php echo $p_active_videos; ?> '>
						<a href="#tab-videos" data-toggle="tab">Videos</a>
					</li>
				</ul>
</div>
<BR/>
<BR/>
<div class="tab-content span10"  >
<?php if ( $p_old_patient == 1 ) { ?>
<div class="tab-pane" id="tab-compliance">
<?php
     $l_div_id="mydashboard";
     $l_idx="#mydashboard";
     $l_src_url="mydashboard.php";
     include('load_form.php');
?>
</div>
<?php } ?>
     <div class="tab-pane" id="tab-profile">
     <?php include('patient_pages/profile.php'); ?>
</div>
					<div class='tab-pane' id='tab-medication'>
<?php
     $l_div_id="medication";
     $l_idx="#medication";
     $l_src_url="mymedication.php";
     include('load_form.php');
?>
</div>
<div class='tab-pane' id='tab-vitals'>
<?php
     $l_div_id="myvitals";
     $l_idx="#myvitals";
     $l_src_url="myvitals.php";
     include('load_form.php');
?>
</div>
<div class='tab-pane' id='tab-vitalgraph'>
<?php
     $l_div_id="myvitalgraph";
     $l_idx="#myvitalgraph";
     $l_src_url="myvitalgraphs.php";
     include('load_form.php');
?>
</div>

					<div class='tab-pane' id='tab-woundcare'>
<?php
     $l_div_id="mywoundcare";
     $l_idx="#mywoundcare";
     $l_src_url="mywoundcare.php";
     include('load_form.php');
?>
					</div>
<?php if ( $p_old_patient == 1 ) { ?>
					<div class='tab-pane' id='tab-woundimage'>
<?php
    $l_div_id="woundiamge";
    $l_idx="#woundiamge";
    $l_src_url="mywoundimage.php";
    include('load_form.php');
?>
</div>
<?php } ?>
<div class='tab-pane' id='tab-task'>
<?php
     $l_div_id="mytask";
     $l_idx="#mytask";
     $l_src_url="mytask.php";
     include('load_form.php');
?>
</div>
					<div class='tab-pane' id='tab-diet'>
<?php
     $l_div_id="mydiet";
     $l_idx="#mydiet";
     $l_src_url="mydiet.php";
     include('load_form.php');
?>
					</div>
					<div class='tab-pane' id='tab-videos'>
<?php
     $l_div_id="myvideo";
     $l_idx="#myvideo";
     $l_src_url="myvideo.php";
     include('load_form.php');
?>

					</div>

					<div class='tab-pane' id='tab-appt'>
						<?php include('patient_pages/appointments.php'); ?>
					</div>
					<div class='tab-pane' id='tab-symptom'>
<?php
     $l_div_id="mysymptom";
     $l_idx="#mysymptom";
     $l_src_url="mysymptom.php";
     include('load_form.php');
?>
					</div>
					<div class='tab-pane' id='tab-phyact'>
<?php
     $l_div_id="myphyact";
     $l_idx="#myphyact";
     $l_src_url="myphyact.php";
     include('load_form.php');
?>
					</div>
</div>
</div>

<?php include('foot.php') ?>
<script>
    $("#tab-profile").attr('class',"tab-pane");
    $("#tab-<?php echo $p_curtab; ?>").attr('class',"tab-pane active");
</script>
