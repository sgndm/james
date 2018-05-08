<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
      $l_prefix = "dietrecord_";
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
    	'updateMyDietTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
);
function dietsave()
{
   callme('dietrecord,alldiet,');
}
function localalldiet(pdata)
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
     parent.resettab("pa");
<?php } ?>
  }
}
function dietaddrow()
{
  l_row = $("#dietrecord_" +  "maxrow").val();
  l_maxrow = parseInt(l_row) +1 ; 
  $("#dietrecord_maxrow").val(l_maxrow);
  var dt = $("#dietTable tr").eq(1);
  l_st= "dietrecord_" + l_row + "_";
  dt1 = "<tr>" + dt.html().split('dietrecord_0_').join(l_st) + "</tr>";
  $("#dietTable").append(dt1);

  l_x= "dietrecord_" + l_row + "_id";
 $('<input type="hidden" id="' + l_x + '" value="0" />').appendTo(diet_div);

  l_x= "#dietrecord_" + l_row + "_diet_id";
  $(l_x).val("");

}
</script>

<h4>Diet
&nbsp;&nbsp;&nbsp;<input type="button" onclick="callrefresh('mydiet.php')"  value="Refresh" />
&nbsp;&nbsp;&nbsp;<div class="btn-group" style="margin-left: 5px;">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Show
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="callchange('My Diet', 'all')">All</a></li>
    <li><a href="#" onclick="callchange('My Diet', 'active')">Only Active</a></li>
  </ul>
</div>
</h4>
<div id="diet_div" class="row">
<div class='span10'>
<div id="tableHolder">
	
</div>
<BR/>
<div class='login-actions'>
&nbsp;
&nbsp;
<input type="button" onclick="dietaddrow()"  value="New diet" />
&nbsp;
<?php if ( $p_old_patient == "1" ) {  ?>
     <input type="button" onClick="dietsave()" value="Save"/>
<?php } else {  ?>

<div class="row">
<div class='span2'>
     <img src="assets/images/left_wc.png" onClick="parent.resettab('wc');"  />
</div>
<div class='span6'>
</div>
<div class='span2'>
     <img src="assets/images/right_pa.png" onClick="dietsave()" />
</div>
</div>

<?php }  ?>
&nbsp;
&nbsp;
</div>
<BR/>
</div>
</div>
