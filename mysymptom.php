<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
      $l_prefix = "symptomrecord_";
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_old_patient=$_SESSION["old_patient"];

      $p_patient_id = $_SESSION["view_patient_id"];
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
?>
<script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="css/surgical.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/patient.css" media="all" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(document).ready(function() 
    { 
    	$.post( 
    	'updateMySymptomTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
);
function symptomsave()
{
   callme('symptomrecord,allsymptom,');
}
function localallsymptom(pdata)
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
     parent.resettab("videos");
<?php } ?>
  }
}
function symptomaddrow()
{
  l_row = $("#symptomrecord_" +  "maxrow").val();
  l_maxrow = parseInt(l_row) +1 ; 
  $("#symptomrecord_maxrow").val(l_maxrow);
  var dt = $("#symptomTable tr").eq(1);
  l_st= "symptomrecord_" + l_row + "_";
  dt1 = "<tr>" + dt.html().split('symptomrecord_0_').join(l_st) + "</tr>";
  $("#symptomTable").append(dt1);

  l_x= "symptomrecord_" + l_row + "_id";
 $('<input type="hidden" id="' + l_x + '" value="0" />').appendTo($("#symptom_div"));

  l_x= "#symptomrecord_" + l_row + "_symptom_id";
  $(l_x).val("");

  l_x= "#symptomrecord_" + l_row + "_start_date";
  $(l_x).val("");
  l_a=l_x;
  l_x= "#symptomrecord_" + l_row + "_end_date";
  $(l_x).val("");
  l_b=l_x;
  $(l_a).removeClass('hasDatepicker')
  $(l_a).datepicker();
  $(l_b).removeClass('hasDatepicker')
  $(l_b).datepicker();
}
</script>

<h4>Symptoms
&nbsp;&nbsp;&nbsp;<input type="button" onclick="callrefresh('mysymptom.php')"  value="Refresh" />
&nbsp;&nbsp;&nbsp;<div class="btn-group" style="margin-left: 5px;">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Show
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="callchange('My Symptom', 'all')">All</a></li>
    <li><a href="#" onclick="callchange('My Symptom', 'active')">Only Active</a></li>
  </ul>
</div>
</h4>
<div id="symptom_div" class="row">
<div class='span12'>
<div id="tableHolder">
	
</div>
<BR/>
</div>
</div>

<div id="symptom_div" class="row">
<div class='span12'>
<input type="button" onclick="symptomaddrow()"  value="New symptom" />
<input type="button" onClick="symptomsave()" value="Save"/>
</div>
</div>
<?php if ( $p_old_patient == "1" ) {  ?>
<input style="width:200px;height:30px" type="text" placeholder="Push Message"  id="symptomrecord_pushmessage" />
<input type="button" onclick="callme('symptomrecord,pushsymptom,null')"  value="Send Push Notification" />
<?php }  ?>
<BR/>
<?php
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $la_obj = $dx->getSymptomsUserResponse($x_obj);
      $symtotalrows = $la_obj->{"total_records"};
?>
<?php if ( $p_old_patient == "1" && $symtotalrows  > 0 ) { ?>
<h4>User Response</h4>

<table border=2>
<tr style="align:center" >
<th class="span2" >Symptom</th>
<th class="span2" >Created Date</th>
<th class="span2" >Reviewed Date</th>
<th class="span2" >Reviewed By</th>
<th class="span3" >Reviewer Note</th>
</tr>
<?php
      for ( $idx =0; $idx <$symtotalrows; $idx++)
      {
         $rec=$la_obj->{"record"}[$idx];
         $l_symptom  =  $rec["symptom"];
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
      ?>
<tr align="left">
<td align="left" style="width:30%"> <?php echo $l_symptom; ?> </td>
<td align="center" > <?php echo $l_date; ?></td>
<td align="center" > <?php echo $l_rdate; ?></td>
<td align="center" > <?php echo $l_reviewer; ?> </td>
<td align="center" > <?php echo $l_response; ?> </td>
</tr>
<?php
      }
?>
</table>
<?php } ?>
<BR/>
</div>
</div>
