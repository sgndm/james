<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
      $l_prefix = "woundcarerecord_";
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_old_patient=$_SESSION["old_patient"];
      $p_patient_id = $_SESSION["view_patient_id"];
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $l_obj = $dx->getWoundcare($x_obj);
      $totalrows = $l_obj->{"total_records"};
      if ( $totalrows == 0 )
      $totalrows=1;
    
      $data='{}';       
      $s_obj=json_decode($data);       
      $ux = UserModel::get();          
      $ss_obj = $dx->getWoundList($s_obj);        
      $s_fieldtotal = $ss_obj->{"total_records"}; 

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
    	'updateMyWoundTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
);
function woundcaresave()
{
   callme('woundcarerecord,allwoundcare,');
}
function localallwoundcare(pdata)
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
     parent.resettab("diet");
<?php } ?>
  }
}
function woundcareaddrow()
{
  l_row = $("#woundcarerecord_" +  "maxrow").val();
  l_maxrow = parseInt(l_row) +1 ; 
  $("#woundcarerecord_maxrow").val(l_maxrow);
  var dt = $("#woundcareTable tr").eq(1);
  l_st= "woundcarerecord_" + l_row + "_";
  dt1 = "<tr>" + dt.html().split('woundcarerecord_0_').join(l_st) + "</tr>";
  $("#woundcareTable").append(dt1);

  l_x= "woundcarerecord_" + l_row + "_id";
 $('<input type="hidden" id="' + l_x + '" value="0" />').appendTo(woundcare_div);

  l_x= "#woundcarerecord_" + l_row + "_description";
  $(l_x).val("");

}
</script>

<h4>Woundcare
&nbsp;&nbsp;&nbsp;<input type="button" onclick="callrefresh('mywoundcare.php')"  value="Refresh" />
&nbsp;&nbsp;&nbsp;<div class="btn-group" style="margin-left: 5px;">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Show
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="callchange('My Wound', 'all')">All</a></li>
    <li><a href="#" onclick="callchange('My Wound', 'active')">Only Active</a></li>
  </ul>
</div>
</h4>
<div id="woundcare_div" class="row">
<div class='span12'>
<div id="tableHolder">

</div>
<BR/>
<div class='login-actions'>
&nbsp;
&nbsp;
<input type="button" onclick="woundcareaddrow()"  value="New woundcare" />
&nbsp;
<?php if ( $p_old_patient == "1" ) {  ?>
     <input type="button" onClick="woundcaresave()" value="Save"/>
<?php } ?>
&nbsp;
&nbsp;
</div>
<BR/>
<?php
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $la_obj = $dx->getAllWoundResponses($p_patient_id);
      $symtotalrows = $la_obj->{"total_records"};
?>
<?php if ( $p_old_patient == "1" && $symtotalrows  > 0 ) { ?>
<h4>User Response</h4>

<table border=2>
<tr style="align:center" >
<th class="span2" >Image</th>
<th class="span2" >Created Date</th>
<th class="span2" >Reviewed Date</th>
<th class="span2" >Reviewed By</th>
<th class="span3" >Reviewer Note</th>
</tr>
<?php
      for ( $idx =0; $idx <$symtotalrows; $idx++)
      {
         $rec=$la_obj->{"record"}[$idx];
         $l_date  =  $rec["created_ts"];
		 $l_rdate  =  $rec["reviewed_ts"];
         $l_response  =  $rec["reviewer_note"];
		 $l_reviewer_id = $rec["reviewer_id"];
		 $l_fileName = $rec["file_name"];
		 $l_thumb_url = "/uploads/". $p_patient_id . "p/t_" . $l_fileName;
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
<td align="left" style="width:30%"><img src="<?php echo "$l_thumb_url"; ?>" /> </td>
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
</div>
</div>


