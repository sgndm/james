<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $p_patient_id=0;
      $user_id= $ctrl->getUserID();
      $user_type= $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';

      $p_obj=json_decode($data);
	  
	  

?>

<div style="width:100%">
<div id="tableHolder">
	<!-- Will have data from updateDiagnosisTable.php -->
</div>
</div>
<BR/>
            <div class='login-actions'>
<input type="button" onclick="callform('diagnosis.php',0,'patient')" value="New Entry" />
            </div>
<?php include("foot.php") ?>
<script type="text/javascript">
$(document).ready(function() 
    { 
    	$.post( 
    	'updateDiagnosisTable.php', 
    	{ show: "active" }, 
    	function( data )
    	{  
        	$('#tableHolder').html(data);
    	});
        
    } 
); 
</script>