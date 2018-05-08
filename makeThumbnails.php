<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      
      $patientSql = "SELECT id FROM patient WHERE isactive = 'Y'";
	  $patient_ids = $ctrl->getRecordIds($patientSql);
	  
	  
	  foreach ($patient_ids as $key => $value) 
      {
        $patientID = $value[0];
		$dir = "/var/www/mobimd/uploads/" . $patientID . "p/";
		$dir2 = "/var/www/mobimd/uploads/" . $patientID . "/";
		if(file_exists($dir))
		{
			$images = glob($dir . "*");
			foreach ($images as $image) 
			{
				$imageName = substr( $image, strrpos( $image, '/' )+1 );
				echo "Found an image! : $imageName </BR>";
				if(substr( $imageName, 0, 2 ) != "t_")
				{
					if(substr( $imageName, 0, 5 ) != "photo")
					{
						$src = $dir . $imageName;
						$newImageName = "t_" . $imageName;
						$dest = $dir . $newImageName;
						$type = "";
						$type = substr( $imageName, strrpos( $imageName, '.' )+1 );
						$dx->make_thumb($src, $dest, $type);
						echo "Sent Image to be made into thumbnail. </BR>
						Source : $src </BR>
						Target : $dest </BR>
						Image Type : $type </BR>";
					}
				}
				
			}
		}
		
		if(file_exists($dir2))
		{
			$images = glob($dir2 . "*");
			foreach ($images as $image) 
			{
				$imageName = substr( $image, strrpos( $image, '/' )+1 );
				echo "Found an image! : $imageName </BR>";
				if(substr( $imageName, 0, 2 ) != "t_")
				{
					if(substr( $imageName, 0, 5 ) != "photo")
					{
						$src = $dir2 . $imageName;
						$newImageName = "t_" . $imageName;
						$dest = $dir2 . $newImageName;
						$type = "";
						$type = substr( $imageName, strrpos( $imageName, '.' )+1 );
						$dx->make_thumb($src, $dest, $type);
						echo "Sent Image to be made into thumbnail. </BR>
						Source : $src </BR>
						Target : $dest </BR>
						Image Type : $type </BR>";
					}
				}
				
			}
		}
		
	  }
?>