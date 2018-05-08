<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_patient_id=0;
      $p_id = $_GET["id"];
      if ( $p_id == null || $p_id == "" )
         $p_id = "0";

      $user_id= $ctrl->getUserID();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
      $user_type= $ctrl->getUserType();
?>

<input type="hidden" id="patient_id" value="<?php echo $p_id; ?>" />
<div style="width:100%">
<div id="tableHolder">
	<!-- Will have data from updateCCTable.php -->
</div>
</div>
<BR/>
<?php if($dx->doIHavePermission($user_type, "add_cc")) { ?>
            <div class='login-actions'>
<input type="button" onclick="callform('cc.php',0,'patient')" value="New Entry" />
            </div>
<?php } ?>
<?php include("foot.php") ?>
<script type="text/javascript">
$(document).ready(function() 
    { 
    	$.post( 
    	'updateCCTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
); 
</script>