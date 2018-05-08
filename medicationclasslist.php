<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_patient_id=0;
      $user_id= $ctrl->getUserID();
      $data='{"user_id":"' . $user_id . '"}';

?>
<div style="width:100%">
<div id="tableHolder">
	
</div>
</div>
<BR/>
			<div class='login-actions'>
<input type="button" onclick="callform('medicationclass.php',0,'patient')" value="New Entry" />
			</div>
<?php include("foot.php") ?>
<script type="text/javascript">
$(document).ready(function() 
    { 
    	$.post( 
    	'updateMedicationClassTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
); 
</script>