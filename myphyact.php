<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
      $l_prefix = "phyactrecord_";
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
    	'updateMyPATable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
);
function phyactsave()
{
   callme('phyactrecord,allphyact,');
}
function localallphyact(pdata)
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
     parent.resettab("sym");
<?php } ?>
  }
}
function phyactaddrow()
{
  l_row = $("#phyactrecord_" +  "maxrow").val();
  l_maxrow = parseInt(l_row) +1 ; 
  $("#phyactrecord_maxrow").val(l_maxrow);
  var dt = $("#phyactTable tr").eq(1);
  l_st= "phyactrecord_" + l_row + "_";
  dt1 = "<tr>" + dt.html().split('phyactrecord_0_').join(l_st) + "</tr>";
  $("#phyactTable").append(dt1);

  l_x= "phyactrecord_" + l_row + "_id";
 $('<input type="hidden" id="' + l_x + '" value="0" />').appendTo(phyact_div);

  l_x= "#phyactrecord_" + l_row + "_physicalactivity";
  $(l_x).val("");

}
</script>

<h4>Physical Activity
&nbsp;&nbsp;&nbsp;<input type="button" onclick="callrefresh('myphyact.php')"  value="Refresh" />
&nbsp;&nbsp;&nbsp;<div class="btn-group" style="margin-left: 5px;">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Show
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="callchange('My PA', 'all')">All</a></li>
    <li><a href="#" onclick="callchange('My PA', 'active')">Only Active</a></li>
  </ul>
</div>
</h4>
<div id="phyact_div" class="row">
<div class='span12'>
<div id="tableHolder">
	
</div>
<BR/>
<div class='login-actions'>
&nbsp;
&nbsp;
&nbsp;
<input type="button" onclick="phyactaddrow()"  value="New phyact" />
<?php if ( $p_old_patient == "0" ) {  ?>

<div class="row">
<div class='span2'>
     <img src="assets/images/left_diet.png" onClick='parent.resettab("diet");'  />
</div>
<div class='span6'>
</div>
<div class='span2'>
     <img src="assets/images/right_sym.png" onClick="phyactsave()" 
</div>
</div>
<?php } else {  ?>
<input type="button" onClick="phyactsave()" value="Save" />
<?php } ?>
&nbsp;
&nbsp;
</div>
<BR/>
</div>
</div>
