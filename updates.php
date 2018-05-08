<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      
      $sql="SELECT id FROM patient";
      $patient_ids = $dx->getRecordIds($sql);
	  
      $sql="SELECT id FROM user";
      $user_ids = $dx->getRecordIds($sql);
	  
	  /*
	  
      foreach ($patient_ids as $key => $value) 
      {
      	$currentID = $value[0];
		$sql = "SELECT phone FROM patient WHERE id = $currentID";
		$currentPhone = $ctrl->getRecordTEXT($sql);
		//Change to phone with only numbers
		$newPhone = preg_replace('/\D/', '', $currentPhone);
		$sql = "UPDATE patient SET phone =  " . $ctrl->Q($newPhone) . 
				" WHERE id = $currentID";
		$success = $ctrl->execute($sql);
		if($success == 1)
		{
			echo "Patient ID $currentID </br>";
			echo "Phone Before : $currentPhone </br>";
			echo "Phone After : $newPhone </br> </br>";
			
		}
		else 
		{
			echo "EXECUTE FAILED : $sql </br>";
		}
      }
	  
	  foreach ($user_ids as $key => $value) 
      {
      	$currentID = $value[0];
		$sql = "SELECT phone FROM user WHERE id = $currentID";
		$currentPhone = $ctrl->getRecordTEXT($sql);
		$newPhone = preg_replace('/\D/', '', $currentPhone);
		$sql = "UPDATE user SET phone =  " . $ctrl->Q($newPhone) . 
				" WHERE id = $currentID";
		$success = $ctrl->execute($sql);
		if($success == 1)
		{
			echo "User ID $currentID </br>";
			echo "Phone Before : $currentPhone </br>";
			echo "Phone After : $newPhone </br> </br>";
			
		}
		else 
		{
			echo "EXECUTE FAILED : $sql </br>";
		}
      }
	  
   */   
?>