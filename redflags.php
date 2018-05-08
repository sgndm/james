<?php require_once("init_setup.php") ?>
<?php include('head.php') ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $ux = UserModel::get();
      $user_id= $ctrl->getUserID();
      $user_type = $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
      $p_obj->{"isactive"}='Y';
      
      
?>

<div class="row" >
<div class="span2"> 
   <img style="width:100%"  src="assets/images/photo1.png"/>
  </BR>
   <!--<?php if($ux->doIHavePermission($userType, "add_patient")){ ?>
   <img style="width:100%"  src="assets/images/addpatient.png" onClick="callmenuitem('addpatient.php','')" />
   <BR/>
   <?php } ?>
   <img style="width:100%"  src="assets/images/analytics.png"
 onclick="callmenuitem('dashboard.php','')"  />
   <BR/> -->
   
   </BR>
   <button class="btn btn-primary btn-block" style="height: 60px;" type="button"
   onClick="callmenuitem('addpatient.php','')" >
   	<i class="icon-plus"></i><i class="icon-user"></i> add patient
   </button>
   </BR>
   <button class="btn btn-primary btn-block" style="height: 60px;" type="button"
   onclick="callmenuitem('dashboard.php','')" >
   	<i class="icon-signal"></i> analytics
   </button>
   
</div>

<?php
      if($user_type == 1)//Admin
      {
        $l_obj = $dx->getPatients($p_obj);
      }
      else if($user_type == 2)//Clinic
      {
        $l_obj = $dx->getPatientsForClinic($p_obj);
      }
      else if($user_type == 3)//Doctor
      {
        $l_obj = $dx->getPatientsForDoctor($p_obj);
      }
      else // Care Coordinator
      {
        $l_obj = $dx->getPatientsForCC($p_obj);
      }

      $fieldtotal = $l_obj->{"total_records"};

?>

<div class="span10"> 
<p>
<!--<div class="input-append">
<input class="span2" id="appendedInputButton" type="text">
<button class="btn" type="button"><i class="icon-search"></i></button>
</div>-->
</p>

    

<?
      $title="Red Flags";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$fieldtotal;
      $ux = UserModel::get(); 


      $default_view_rows=$ux->geteusersettting("myhome","showrows");
      if ( $ctrl->isEmpty($default_view_rows) )
         $default_view_rows=100;
      include("include/include_pageindex.php");
      $l_res=$ux->saveusersettting("myhome","showrows",$rows_limit);
?>

<script type="text/javascript" src="js/jquery-latest.js"></script> 
<script type="text/javascript" src="js/jquery.tablesorter.js"></script> 
<!--link rel="stylesheet" href="css/tablesorter.css" type="text/css"  /-->
<script type="text/javascript"> 
function ignore(patient_id)
{
	$.post( 
    	'callserver.php', 
    	{ patient: patient_id, type: "ignore" }, 
    	function( data )
    	{  
        	$("#ignore_" + patient_id).html("<font color='black'>Ignored</font>");
    	});
}
</script>
<table id="myTable" class="tablesorter" border=1>
<thead>
<tr >
<th  class="span2"> Patient</th>

<th  class="span1"> Med Compliance</th>
<th  class="span2"> Narcotic Intake</th>
<th class="span2"> Symptoms Report</th>
<th class="span2"> Vitals</th>
<th class="span2"> Wound Images</th>
<th  class="span1"> Ignore</th>
</tr>
</thead>
<tbody>

<?php
      
      for ( $idx =0; $idx <$fieldtotal; $idx++)
      {
       $c_no=$idx + 1;
       if ( $c_no  < $start_rowid || $c_no > $end_rowid )
       {
          continue;
       }
         $rec=$l_obj->{"record"}[$idx];
         $l_record_id  =  $rec["id"];
         $l_url  =  $rec["photourl"];
         $l_name  =  $rec["first_name"] . " " .
                     $rec["last_name"];

         $data='{"user_id":"' . $l_record_id . '"}';
         $k1_obj=json_decode($data);
         $k1_obj->{"response"}="Y";
         $k1_obj->{"reviewed"}="N";
		 
		 $redflags = $dx->findRedFlags($l_record_id);
		 $vitalFlag = $redflags["vitals"];
		 $pillFlag = $redflags["pills"];
		 $medFlag = $redflags["meds"];
		 $woundFlag = $redflags["wound"];
		 $symptomTodayFlag = $redflags["symptomToday"];
		 $symptomYesterdayFlag = $redflags["symptomYesterday"];
		 $symptomsFlag = 0;
		 if($symptomTodayFlag == 1 || $symptomYesterdayFlag == 1)
		 	$symptomsFlag = 1;
		 $sql = "SELECT ignore_execptions FROM patient WHERE id = $l_record_id";
		 $ignore = $ctrl->getRecordTEXT($sql);
		 $ignoreFlag = 0;
		 if($ignore == "N")
		 	$ignoreFlag = 1;
		 
		 if(($vitalFlag == 0 && $pillFlag == 0 && 
		 	$medFlag == 0 && $symptomsFlag == 0 &&
		 	$woundFlag == 0) || $ignoreFlag == 0 )
		 {
		 	//do not add patient to red flags table.
		 }
		 else // add patient red flags to table
		 {
         
?>
<tr style="bgcolor:white;cursor: pointer;">  
<td align="left" onclick="callpatientform('patient.php',<?php echo $l_record_id; ?>,'profile')" > 
<img style="width:40px; height:40px" src="<? echo $l_url ; ?>"  
    onclick="callpatientform('patient.php',<?php echo $l_record_id; ?>,'profile')"  
/> <?php echo $l_name; ?></td>
<?php 
	if($medFlag == 1)
	{
		$l_compliancepercent = $dx->getMedicalCompliancePercent($k1_obj);
		if ( $l_compliancepercent > 80 ) 
           $l_color = "green";
         else if ($l_compliancepercent <= 80 && $l_compliancepercent > 0)
           $l_color = "#B50128";
         else {
             $l_compliancepercent = "00.00";
             $l_color = "#B50128";
         }
		 
	}
	else 
	{
		$l_compliancepercent = "---";
		$l_color = "black";
	}
?>
 <td align="center" ><font color="<?php echo $l_color; ?>"><?php echo $l_compliancepercent; ?>%</font></td>
			




    <td align="left"  >
    <?php 
         $r1_obj = $dx->getPainMedication("today", $l_record_id);
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $painArray = array();
         echo "<ul class='unstyled'><li>Today:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            $rec=$r1_obj->{"record"}[$s_idx];
            $l_name  =  $rec["medication"];
            $l_name_id = $rec["main_id"];
			$l_value = $ctrl->fmt($rec["max_percent"]);
			$l_whatToPrint = "$l_name - $l_value%";
            $l_prefix = "painrecord_". $l_name_id . "_painID";
            $l_prefixToSend = "painrecord_". $l_name_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_name_id' />";
     ?>
                <li class="PainMedsList" title="Click to mark as reviewed." id="pain_<?php echo $l_name_id; ?>" data-toggle="modal" href="#myModalPain<?php echo $l_name_id; ?>"><font color='#B50128'> <?php echo $l_whatToPrint; ?> </font></li>
                
                <div id="myModalPain<?php echo $l_name_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<h5>Reviewing "<?php echo "$l_whatToPrint"; ?>"</h5>
  						<input id="painrecord_<?php echo $l_name_id; ?>_note" class='span3' placeholder='Reviewer Notes' value=''/>
  					</div>
  					<div class="modal-footer">
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewpainmeds,null')">Mark as reviewed</button>
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewpainmedsnotify,null')">Mark and Notify</button>
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div> 
		<?php 
            
            $painArray[] = $l_name;
         }
         if(empty( $painArray[0] ))
         {
              echo "<font color='black'> --- </font>";
         }
         $r1_obj = $dx->getPainMedication("yesterday", $l_record_id);
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $painArray = array();
         echo "<li>Yesterday:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            $rec=$r1_obj->{"record"}[$s_idx];
            $l_name  =  $rec["medication"];
            $l_name_id = $rec["main_id"];
			$l_value = $ctrl->fmt($rec["max_percent"]);
			$l_whatToPrint = "$l_name - $l_value%";
            $l_prefix = "painrecord_". $l_name_id . "_painID";
            $l_prefixToSend = "painrecord_". $l_name_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_name_id' />";
     ?>
                <li class="PainMedsList" title="Click to mark as reviewed." id="pain_<?php echo $l_name_id; ?>" data-toggle="modal" href="#myModalPain<?php echo $l_name_id; ?>"><font color='#B50128'> <?php echo $l_whatToPrint; ?> </font></li>
                
                <div id="myModalPain<?php echo $l_name_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<h5>Reviewing "<?php echo "$l_whatToPrint"; ?>"</h5>
  						<input id="painrecord_<?php echo $l_name_id; ?>_note" class='span3' placeholder='Reviewer Notes' value=''/>
  					</div>
  					<div class="modal-footer">
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewpainmeds,null')">Mark as reviewed</button>
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewpainmedsnotify,null')">Mark and Notify</button>
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div>  
     <?php 
            
            $painArray[] = $l_name;
         }
         if(empty( $painArray[0] ))
         {
              echo "<font color='black'> --- </font>";
         }
         echo "</ul>";
    ?></td>

<td align="left"  >
    <?php 
         $r1_obj = $dx->getDistinctSymptomsUserResponse($k1_obj, "today");
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $symptomArray = array();
         echo "<ul class='unstyled'><li>Today:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            $s_rec=$r1_obj->{"record"}[$s_idx];
            $l_symptoms  =  $s_rec["symptom"];
            $l_symptom_id = $s_rec["id"];
            $l_prefix = "symptomrecord_". $l_symptom_id . "_symptomID";
            $l_prefixToSend = "symptomrecord_". $l_symptom_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_symptom_id' />";
            if ($l_symptoms === "No Symptoms") 
            {
                //do nothing
            }
            else 
            {
                ?>
                <li class="symptomsList" title="Click to mark as reviewed." id="symptom_<?php echo $l_symptom_id; ?>" data-toggle="modal" href="#myModalSymptom<?php echo $l_symptom_id; ?>"><font color='#B50128'> <?php echo $l_symptoms; ?> </font></li>
                
                <div id="myModalSymptom<?php echo $l_symptom_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<h5>Reviewing "<?php echo "$l_symptoms"; ?>"</h5>
  						<input id="symptomrecord_<?php echo $l_symptom_id; ?>_note" class='span3' placeholder='Reviewer Notes' value=''/>
  					</div>
  					<div class="modal-footer">
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewsymptoms,null')">Mark as reviewed</button>
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewsymptomsnotify,null')">Mark and Notify</button>
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div>
				<?php 
                $symptomArray[] = $l_symptoms;
            }
            
         }
         if(empty( $symptomArray[0] ))
         {
              echo "<font color='black'> -- </font>";
         }
         $r1_obj = $dx->getDistinctSymptomsUserResponse($k1_obj, "yesterday");
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $symptomArray = array();
         echo "<li>Yesterday:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            $s_rec=$r1_obj->{"record"}[$s_idx];
            $l_symptoms  =  $s_rec["symptom"];
            $l_symptom_id = $s_rec["id"];
            $l_prefix = "symptomrecord_". $l_symptom_id . "_symptomID";
            $l_prefixToSend = "symptomrecord_". $l_symptom_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_symptom_id' />";
            if ($l_symptoms === "No Symptoms") 
            {
                //do nothing
            }
            else 
            {
                ?>
                <li class="symptomsList" title="Click to mark as reviewed." id="symptom_<?php echo $l_symptom_id; ?>" data-toggle="modal" href="#myModalSymptom<?php echo $l_symptom_id; ?>"><font color='#B50128'> <?php echo $l_symptoms; ?> </font></li>
                
                <div id="myModalSymptom<?php echo $l_symptom_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<h5>Reviewing "<?php echo "$l_symptoms"; ?>"</h5>
  						<input id="symptomrecord_<?php echo $l_symptom_id; ?>_note" class='span3' placeholder='Reviewer Notes' value=''/>
  					</div>
  					<div class="modal-footer">
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewsymptoms,null')">Mark as reviewed</button>
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewsymptomsnotify,null')">Mark and Notify</button>
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div>
                <?php 
                $symptomArray[] = $l_symptoms;
            }
         }
         if(empty( $symptomArray[0] ))
         {
              echo "<font color='black'> -- </font>";
         }
         echo "</ul>";
    ?></td>


<td align="left"  >
    <?php 
         $r1_obj = $dx->getAllVitals("today", $l_record_id);
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $vitalArray = array();
         echo "<ul class='unstyled'><li>Today:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            $rec=$r1_obj->{"record"}[$s_idx];
            $l_name  =  $rec["vital"];
			$l_vital_id  =  $rec["vital_id"];
            $l_name_id = $rec["main_id"];
			$l_value = $ctrl->fmt($rec["value_entered"]);
			$l_whatToPrint = "$l_name - $l_value";
            $l_prefix = "vitalrecord_". $l_name_id . "_vitalID";
            $l_prefixToSend = "vitalrecord_". $l_name_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_name_id' />";
			$sql = "SELECT low_warning, high_warning FROM vitals WHERE id = $l_vital_id";
			$warnings = $dx->getOnlyRecords($sql);
			$warnRecord = $warnings->{"record"}[0];
			$low = $warnRecord["low_warning"];
			$high = $warnRecord["high_warning"];
			if($l_value > $low && $l_value < $high)
			{
				//do nothing.
			}
			else 
			{
     ?>
                <li class="VitalsList" title="Click to mark as reviewed." id="vital_<?php echo $l_name_id; ?>" data-toggle="modal" href="#myModalVital<?php echo $l_name_id; ?>"><font color='#B50128'> <?php echo $l_whatToPrint; ?> </font></li>
                
                <div id="myModalVital<?php echo $l_name_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<h5>Reviewing "<?php echo "$l_whatToPrint"; ?>"</h5>
  						<input id="vitalrecord_<?php echo $l_name_id; ?>_note" class='span3' placeholder='Reviewer Notes' value=''/>
  					</div>
  					<div class="modal-footer">
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewvitals,null')">Mark as reviewed</button>
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewvitalsnotify,null')">Mark and Notify</button>
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div>  
     <?php 
            
            $vitalArray[] = $l_name;
            }
         }
         if(empty( $vitalArray[0] ))
         {
              echo "<font color='black'> --- </font>";
         }
         $r1_obj = $dx->getAllVitals("yesterday", $l_record_id);
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $vitalArray = array();
         echo "<ul class='unstyled'><li>Yesterday:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            $rec=$r1_obj->{"record"}[$s_idx];
            $l_name  =  $rec["vital"];
			$l_vital_id  =  $rec["vital_id"];
            $l_name_id = $rec["main_id"];
			$l_value = $ctrl->fmt($rec["value_entered"]);
			$l_whatToPrint = "$l_name - $l_value";
            $l_prefix = "vitalrecord_". $l_name_id . "_vitalID";
            $l_prefixToSend = "vitalrecord_". $l_name_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_name_id' />";
			$sql = "SELECT low_warning, high_warning FROM vitals WHERE id = $l_vital_id";
			$warnings = $dx->getOnlyRecords($sql);
			$warnRecord = $warnings->{"record"}[0];
			$low = $warnRecord["low_warning"];
			$high = $warnRecord["high_warning"];
			if($l_value > $low && $l_value < $high)
			{
				//do nothing.
			}
			else 
			{
     ?>
                <li class="VitalsList" title="Click to mark as reviewed." id="vital_<?php echo $l_name_id; ?>" data-toggle="modal" href="#myModalVital<?php echo $l_name_id; ?>"><font color='#B50128'> <?php echo $l_whatToPrint; ?> </font></li>
                
                <div id="myModalVital<?php echo $l_name_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<h5>Reviewing "<?php echo "$l_whatToPrint"; ?>"</h5>
  						<input id="vitalrecord_<?php echo $l_name_id; ?>_note" class='span3' placeholder='Reviewer Notes' value=''/>
  					</div>
  					<div class="modal-footer">
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewvitals,null')">Mark as reviewed</button>
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewvitalsnotify,null')">Mark and Notify</button>
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div>
                
                
                
                
     <?php 
            
            $vitalArray[] = $l_name;
            }
         }
         if(empty( $vitalArray[0] ))
         {
              echo "<font color='black'> --- </font>";
         }
         echo "</ul>";
    ?></td>


<td align="left"  >
    <?php 
         $r1_obj = $dx->getAllWounds($l_record_id);
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $woundArray = array();
         echo "<ul class='unstyled'>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            $rec=$r1_obj->{"record"}[$s_idx];
            $l_name  =  $rec["created_ts"];
            $l_name_id = $rec["main_id"];
			$l_whatToPrint = "$l_name";
			$l_fileName = $rec["file_name"];
			$l_thumb_url = "/uploads/". $l_record_id . "p/t_" . $l_fileName;
			$thumbdir = "/var/www/mobimd" . $l_thumb_url;
			if(!file_exists($thumbdir))
			{
				$l_thumb_url = "/uploads/". $l_record_id . "/t_" . $l_fileName;
			}
			$l_main_url = $rec["url"];
            $l_prefix = "woundrecord_". $l_name_id . "_woundID";
            $l_prefixToSend = "woundrecord_". $l_name_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_name_id' />";
     ?>
                <li class="WoundList" title="Click to mark as reviewed." id="wound_<?php echo $l_name_id; ?>" data-toggle="modal" href="#myModalWound<?php echo $l_name_id; ?>"><font color='#B50128'> <?php echo $l_whatToPrint; ?> </font></li>
                
                <div id="myModalWound<?php echo $l_name_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<h5>Reviewing "<?php echo "$l_whatToPrint"; ?>"</h5>
  						<img src="<?php echo "$l_thumb_url"; ?>"
  						data-toggle="modal" href="#myModalWoundImage<?php echo $l_name_id; ?>" /> 
  						<h6>Click image to view full size</h6></BR> 
  						<input id="woundrecord_<?php echo $l_name_id; ?>_note" class='span3' placeholder='Reviewer Notes' value=''/>
  					</div>
  					<div class="modal-footer">
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewwounds,null')">Mark as reviewed</button>
    					<button class="btn btn-primary" data-dismiss="modal" onclick="callme('<?php echo "$l_prefixToSend"; ?>,reviewwoundsnotify,null')">Mark and Notify</button>
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div>  
				
				<div id="myModalWoundImage<?php echo $l_name_id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  					<div class="modal-body">
  						<img src="<?php echo "$l_main_url"; ?>" />
  					</div>
  					<div class="modal-footer">
    					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  					</div>
				</div> 
     <?php 
            
            $woundArray[] = $l_name;
         }
         if(empty( $woundArray[0] ))
         {
              echo "<font color='black'> --- </font>";
         }
         echo "</ul>";
    ?></td>


<td id="ignore_<?php echo $l_record_id; ?>" align="center"><button onclick="ignore(<?php echo $l_record_id; ?>)" class="btn">Ignore</button></td>


</tr>
<?php
	}//end else
      } //end for
?>

</tbody>
</table>
</div><!--span-->
</div> <!--row-->







<?php include("foot.php") ?>
<script>
$(document).ready(function() 
    { 
        $("#myTable").tablesorter();
    } 
); 
 

</script>
