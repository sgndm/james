<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get();
      $ux = UserModel::get();
      $dx = DataModel::get();
      $whattoshow= $_POST["show"];
      $user_id= $ctrl->getUserID();
      $user_type = $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
	  if($whattoshow == "active")
	  {
	  	$p_obj->{"isactive"}='Y';
	  }

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

<?php
      $title="Patients";
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


<table id="myTable" class="tablesorter" border=1>
<thead>
<tr >
<th  class="span2"> Name</th>

<th  class="span2"> Med Compliance<BR/>(click % for HX)</th>
<th  class="span1"> Narcotic Intake</th>
<th class="span2"> Symptoms Report</th>
<th class="span1"> Vitals</th>
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

         $data='{"user_id":"' . $l_record_id . '"}';
         $k1_obj=json_decode($data);
         $k1_obj->{"response"}="Y";
         $k1_obj->{"reviewed"}="N";
         $l_compliancepercent = $dx->getMedicalCompliancePercent($k1_obj);
         if ( $l_compliancepercent > 80 )
           $l_color = "green";
         else if ($l_compliancepercent <= 80 && $l_compliancepercent > 0)
           $l_color = "#B50128";
         else {
             $l_compliancepercent = "00.00";
             $l_color = "#B50128";
         }
         $heartPhoto = "assets/images/blackheart.png";
         $heartColor = $dx->getVitalColor($k1_obj);
         if($heartColor == 3)
         {
             $heartPhoto = "assets/images/redheart.png";
         }
         else if($heartColor == 2)
         {
             $heartPhoto = "assets/images/yellowheart.png";
         }
         else if($heartColor == 1)
         {
             $heartPhoto = "assets/images/greenheart.png";
         }
         $pillPhoto = "assets/images/greenPill.png";
         $pillColor = $dx->getPillColor($k1_obj);
         if($pillColor == 1)
         {
             $pillPhoto = "assets/images/redPill.png";
         }

?>
<tr style="bgcolor:white;cursor: pointer;">
<td align="left" onclick="callpatientform('patient.php',<?php echo $l_record_id; ?>,'profile')" >
<img style="width:40px; height:40px" src="<? echo $l_url ; ?>"
    onclick="callpatientform('patient.php',<?php echo $l_record_id; ?>,'profile')"
/> <?php echo $l_name; ?></td>


<td align="center" onclick="callpatientform('patient.php',<?php echo $l_record_id; ?>,'compliance')" ><font color="<?php echo $l_color; ?>"><?php echo $l_compliancepercent; ?>%</font></td>

<td align="center"> <img style="width:40px; height:40px" src="<? echo $pillPhoto ; ?>"
    onclick="callpatientform('patient.php',<?php echo $l_record_id; ?>,'compliance')"
/></td>

<td align="left"  >
    <?php
         $r1_obj = $dx->getDistinctSymptomsUserResponse($k1_obj, "today");
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $symptomArray = array();
         echo "<ul class='unstyled'><li>Today:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            //if ( $s_idx > 0 )
                //echo "</br> ";
            $s_rec=$r1_obj->{"record"}[$s_idx];
            $l_symptoms  =  $s_rec["symptom"];
            $l_symptom_id = $s_rec["id"];
            $l_prefix = "symptomrecord_". $l_symptom_id . "_symptomID";
            $l_prefixToSend = "symptomrecord_". $l_symptom_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_symptom_id' />";
            if ($l_symptoms === "No Symptoms")
            {
                echo "<li><font color='green'> $l_symptoms </font></li>";
            }
            else
            {
                ?>
                <li class="symptomsList" title="Go to red flags to mark as reviewed." id="<?php echo $l_symptom_id; ?>" ><font color='#B50128'> <?php echo $l_symptoms; ?> </font></li>
                <?php
            }
            $symptomArray[] = $l_symptoms;
         }
         if(empty( $symptomArray[0] ))
         {
              //$lastSymptomText = $ctrl->getLastSymptomText($l_record_id);
              echo "<font color='black'> --- </font>";
         }
         //echo "</br></ul>";
         $r1_obj = $dx->getDistinctSymptomsUserResponse($k1_obj, "yesterday");
         $t_sym = $r1_obj->{"total_records"};
         $l_symp_res="";
         $symptomArray = array();
         echo "<li>Yesterday:</li>";
         for ( $s_idx =0; $s_idx <$t_sym; $s_idx++)
         {
            //if ( $s_idx > 0 )
                //echo "</br> ";
            $s_rec=$r1_obj->{"record"}[$s_idx];
            $l_symptoms  =  $s_rec["symptom"];
            $l_symptom_id = $s_rec["id"];
            $l_prefix = "symptomrecord_". $l_symptom_id . "_symptomID";
            $l_prefixToSend = "symptomrecord_". $l_symptom_id;
            echo "<input type='hidden' id='$l_prefix' value='$l_symptom_id' />";
            if ($l_symptoms === "No Symptoms")
            {
                echo "<li><font color='green'> $l_symptoms </font></li>";
            }
            else
            {
                ?>
                <li class="symptomsList" title="Go to red flags to mark as reviewed." id="<?php echo $l_symptom_id; ?>" onhover="symptomHover('<?php echo "$l_symptom_id"; ?>')"><font color='#B50128'> <?php echo $l_symptoms; ?> </font></li>
                <?php
            }
            $symptomArray[] = $l_symptoms;
         }
         if(empty( $symptomArray[0] ))
         {
              //$lastSymptomText = $ctrl->getLastSymptomText($l_record_id);
              echo "<font color='black'> --- </font>";
         }
         echo "</ul>";
    ?></td>
<td align="center"> <img src="<? echo $heartPhoto ; ?>"
    onclick="callpatientform('patient.php',<?php echo $l_record_id; ?>,'vitals')"
/></td>
</tr>
<?php
      }
?>

</tbody>
</table>
