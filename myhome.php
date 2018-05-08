<?php require_once("init_setup.php") ?>

<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $ux = UserModel::get();
      $user_id= $ctrl->getUserID();
      $user_type = $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
      $p_obj->{"isactive"}='Y';
      
      
?>
<?php include('head.php'); ?>
<input id="usertype" type="hidden" value="<?php echo $user_type ?>">
<input id="currentid" type="hidden" value="<?php echo $user_id ?>">
<?php 
	  if($user_id != 0)
	  {
?>
<div class="row" >
<div class="span2"> 
    
   	<img style="width:100%"  src="assets/images/photo1.png"/>
   </BR>
   <!--<?php if($ux->doIHavePermission($userType, "add_patient")){ ?>
   <img style="width:100%"  src="assets/images/addpatient.png" onClick="callmenuitem('addpatient.php','')" />
   <BR/>
   <?php } ?>
   <img style="width:100%"  src="assets/images/analytics.png"
 onclick="callmenuitem('dashboard.php','')"  />
   <BR/> -->
   </BR>
   <button class="btn btn-primary btn-block" style="height: 60px;" type="button"
   onClick="callmenuitem('addpatient.php','')" >
   	<i class="icon-plus"></i><i class="icon-user"></i> add patient
   </button>
   </BR>
   <button class="btn btn-primary btn-block" style="height: 60px;" type="button"
   onclick="callmenuitem('dashboard.php','')" >
   	<i class="icon-signal"></i> analytics
   </button>
   
   
   
   
   <!-- sprint # 134 img style="height:10%;width:100%"  src="assets/images/surgicaldatabase.png"
 onclick="callmenuitem('surgicaldatabase.php','')"  />
   <img style="height:10%;width:100%"  src="assets/images/meddatabase.png"
 onclick="callmenuitem('meddatabase.php','')"  />
   <BR/>
   <img style="height:10%;width:100%"  src="assets/images/team.png"
 onclick="callmenuitem('team.php','')"  / -->
</div>



<div class="span10"> 
<p>
<div class="input-append">
<!--<a href="#" class="brand">
	<img style="vertical-align: top;height: 35px;" 
	onclick="window.location.href='redflags.php'" src="assets/images/unrecognized.png" />
</a>-->

<button class="btn btn-bright btn-danger" onclick="window.location.href='redflags.php'">
	<i class="icon-flag"></i> UNRECONCILED RED FLAGS
</button>
	
<input class="span2" id="appendedInputButton" type="text">
<button class="btn" type="button"><i class="icon-search"></i></button>
</div>
</p>


<script type="text/javascript" src="js/jquery-latest.js"></script> 
<script type="text/javascript" src="js/jquery.tablesorter.js"></script> 

 <div id="tableHolder">   
 	<!-- Will be filled with table found in updateHomeTable.php -->
 </div>
</div><!--span-->
</div> <!--row-->
<?php include("foot.php"); 
}
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
    	var current_id = $('#currentid').val();
    	if(current_id == 0)
    	{
    		window.location = "login.php";
    	}
    	else
    	{
    		$.post( 
    		'updateHomeTable.php', 
    		{ show: "active" }, 
    		function( data )
    		{  
        		$('#tableHolder').html(data);
        		$("#myTable").tablesorter();
    		});
    	}
        
    } 
); 
 

</script>
