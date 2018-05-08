<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
      $l_prefix = "videorecord_";
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_old_patient=$_SESSION["old_patient"];

      $p_patient_id = $_SESSION["view_patient_id"];
      $data='{"user_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
?>
<link href="css/surgical.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/patient.css" media="all" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(document).ready(function() 
    { 
    	$.post( 
    	'updateMyVideoTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
);
function videosave()
{
   callme('videorecord,allvideo,');
}
function localallvideo(pdata)
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
     parent.call_home();
<?php } ?>
  }
}
function videoaddrow()
{
  l_row = $("#videorecord_" +  "maxrow").val();
  l_maxrow = parseInt(l_row) +1 ; 
  $("#videorecord_maxrow").val(l_maxrow);
  var dt = $("#videoTable tr").eq(1);
  l_st= "videorecord_" + l_row + "_";
  dt1 = "<tr>" + dt.html().split('videorecord_0_').join(l_st) + "</tr>";
  $("#videoTable").append(dt1);

  l_x= "videorecord_" + l_row + "_id";
 $('<input type="hidden" id="' + l_x + '" value="0" />').appendTo($("#video_div"));

  l_x= "#videorecord_" + l_row + "_video_id";
  $(l_x).val("");

}
</script>

<h4>Videos
&nbsp;&nbsp;&nbsp;<input type="button" onclick="callrefresh('myvideo.php')"  value="Refresh" />
&nbsp;&nbsp;&nbsp;<div class="btn-group" style="margin-left: 5px;">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Show
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="callchange('My Video', 'all')">All</a></li>
    <li><a href="#" onclick="callchange('My Video', 'active')">Only Active</a></li>
  </ul>
</div>
</h4>
<div id="video_div" class="row">
<div class='span12'>
<div id="tableHolder">
	
</div>
<BR/>
</div>
</div>

<div id="video_div" class="row">
<div class='span12'>
<div class='login-actions'>
&nbsp;
&nbsp;
<input type="button" onclick="videoaddrow()"  value="New video" />
&nbsp;
<?php if ( $p_old_patient == "1" ) {  ?>
     <input type="button" onClick="videosave()" value="Save"/>
<?php } else {  ?>
<div class="row">
<div class='span2'>
     <img src="assets/images/left_sym.png" onClick='parent.resettab("sym");'  />
</div>
<div class='span6'>
</div>
<div class='span2'>
     <input type="button" value="Finish" onClick="videosave()" />
</div>
</div>
<?php }  ?>
&nbsp;
</div>
<BR/>
</div>
</div>
