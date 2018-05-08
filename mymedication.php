<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
      $l_prefix = "medicationrecord_";
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_old_patient=$_SESSION["old_patient"];

      $p_patient_id = $_SESSION["view_patient_id"];
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $p_hours = $dx->getHours($x_obj);
      $hourstotalrows = $p_hours->{"total_records"};
      $l_starttime_id = $dx->getStartTime($x_obj);

?>
<script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="css/surgical.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/patient.css" media="all" rel="stylesheet" type="text/css" />
<h4>Medication
&nbsp;&nbsp;&nbsp;<input type="button" onclick="callrefresh('mymedication.php')"  value="Refresh" />
&nbsp;&nbsp;&nbsp;<div class="btn-group" style="margin-left: 5px;">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Show
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="callchange('My Medication', 'all')">All</a></li>
    <li><a href="#" onclick="callchange('My Medication', 'active')">Only Active</a></li>
  </ul>
</div>
</h4>

<script type="text/javascript">
$(document).ready(function() 
    { 
    	$.post( 
    	'updateMyMedicationTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
);
function medicationsave()
{
   callme('medicationrecord,allmedication,');
}
function localallmedication(pdata)
{
  kobj = JSON.parse(pdata);
  if ( kobj.success == "false" )
  {
     show_error  ( kobj.message);
  }
  else if ( kobj.success == "true" )
  {
     show_info("Successfully updated")
     for ( var idx=0; idx<kobj.INSERTED_ROWS;idx++)
     {
        l_x  = kobj.INSERTED_DATA[idx];
        l_name  = "#"+l_x.fld_name;
        l_val  = l_x.fld_value;
        $(l_name).val(l_val);
     }
<?php if ( $p_old_patient == "0" ) { ?>
     parent.resettab("wc");
<?php } ?>
  }
}
function medicationaddrow()
{
  l_row = $("#medicationrecord_" +  "maxrow").val();
  l_maxrow = parseInt(l_row) +1 ; 
  $("#medicationrecord_maxrow").val(l_maxrow);
  var dt = $("#medicationTable tr").eq(1);
  l_st= "medicationrecord_" + l_row + "_";
  dt1 = "<tr>" + dt.html().split('medicationrecord_0_').join(l_st) + "</tr>";
  $("#medicationTable").append(dt1);

  l_x= "medicationrecord_" + l_row + "_id";
 $('<input type="hidden" id="' + l_x + '" value="0" />').appendTo($("#medication_div"));

  l_x= "#medicationrecord_" + l_row + "_medicine";
  $(l_x).val("");
  
  l_x= "#medicationrecord_" + l_row + "_medication_frequency";
  $(l_x).val("");

  l_x= "#medicationrecord_" + l_row + "_start_date";
  $(l_x).val("");
  l_a=l_x;
  l_x= "#medicationrecord_" + l_row + "_end_date";
  $(l_x).val("");
  l_b=l_x;
  l_x= "#medicationrecord_" + l_row + "_morning";
  $(l_x).attr('checked',null);
  l_x= "#medicationrecord_" + l_row + "_afternoon";
  $(l_x).attr('checked',null);
  l_x= "#medicationrecord_" + l_row + "_evening";
  $(l_x).attr('checked',null);
  $(l_a).removeClass('hasDatepicker')
  $(l_a).datepicker();
  $(l_b).removeClass('hasDatepicker')
  $(l_b).datepicker();
  /*$(l_a).click(function() {
      $(l_a).datepicker('show');
  });

  $(l_b).click(function() {
      $(l_b).datepicker('show');
  });*/
}
</script>


<div id="medication_div" class="row">
<div class='span12'>
<label>Medication Start Time</label>
<select style="width:30%" id="medicationrecord_starttime_id" >
<?php
   for ( $i =0; $i <$hourstotalrows; $i++)
   {
      $rr_rec=$p_hours->{"record"}[$i];
      $p_nm=$rr_rec["caption"];
      $p_id=$rr_rec["fld_hour"];
      $p_sel="";
      if ( $p_id == $l_starttime_id ) 
         $p_sel="SELECTED";
      echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
   }
?>
  </SELECT>
 </div>
</div>
<div id="medication_div" class="row">
<div class='span12'>
<div id="tableHolder" >

</div>
 </div>
</div>
<BR/>
<div class="row">
<div class='span12'>
<input type="button" onclick="medicationaddrow()"  value="New Medication" />
<input type="button" onClick="medicationsave()" value="Save"/>
</div>
</div>

<?php if ( $p_old_patient == "1" ) {  ?>
<div class="row">
<div class='span12'>
<input style="width:200px;height:30px" type="text" placeholder="Push Message"  id="medicationrecord_pushmessage" />
<input type="button" onclick="callme('medicationrecord,pushmedication,')"  value="Send Push Notification" />
</div>
</div>
<?php } else {  ?>
<div class="row">
<div class='span2'>
     <img src="assets/images/left_pp.png" onClick='parent.resettab("pp");'  />
</div>
<div class='span6'>
</div>
<div class='span2'>
     <img src="assets/images/right_wc.png" onClick='medicationsave()' />
</div>
</div>
<?php }  ?>

<?php
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $med_obj = $dx->getMedicationUserResponse($x_obj);
      $meduserresprows = $med_obj->{"total_records"};
?>
<?php if ( $p_old_patient == "1" && $meduserresprows  > 0 ) { ?>

<h4>User Response</h4>
<table border=2>
<thead>
<tr style="align:center" >
<th class="span2" >Medication</th>
<th class="span2" >Created Date</th>
<th class="span2" >Reviewed Date</th>
<th class="span2" >Reviewed By</th>
<th class="span3" >Reviewer Note</th>
<th class="span1" >Choice/ Answer</th>
</tr>
</thead>
<tbody>
<?php
      for ( $idx =0; $idx <$meduserresprows; $idx++)
      {
         $rec=$med_obj->{"record"}[$idx];
         $l_medicine  =  $rec["medicine"];
         $l_date  =  $rec["created_ts"];
         $l_rdate  =  $rec["reviewed_ts"];
         $l_response  =  $rec["reviewer_note"];
		 $l_reviewer_id = $rec["reviewer_id"];
		 $l_reviewer = "N/A";
		 if($l_reviewer_id == null || $l_reviewer_id == "")
		 {
			$l_response = "N/A";
	  	 }
		 else
		 {
		 	$sqlforname = "SELECT first_name FROM user WHERE id=$l_reviewer_id";
		 	$l_reviewer = $ctrl->getRecordTEXT($sqlforname); 
		 }
		 if($l_rdate == null || $l_rdate == "")
		 {
			$l_rdate = "N/A";
	  	 }
         $l_total_choice  =  $rec["total_choice"];
         $l_total_answer  =  $rec["total_answer"];
      ?>
<tr>
<td align="left" > <?php echo $l_medicine; ?></td>

<td align="center" > <?php echo $l_date; ?></td>
<td align="center" > <?php echo $l_rdate; ?></td>
<td align="center" > <?php echo $l_reviewer; ?> </td>
<td align="center" > <?php echo $l_response; ?> </td>
<td align="center"> <?php echo "$l_total_choice / $l_total_answer"; ?></td>
</tr>
<?php
      }

?>
</tbody>
</table>
<BR/>
<?php } ?>
</div>
</div>
