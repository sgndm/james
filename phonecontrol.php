<?php require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
    if($_POST['type'] === "login")
    { 
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        $sql = "SELECT * FROM patient WHERE username = '$username' AND password = '$password'";
        $query = $ctrl->query($sql);
        foreach ($query as $key => $value) 
        {
            if($value[0] != null)
            {
                $id = $value[0];
                $response = array(
                    'logged' => true,
                    'id' => $id 
                );
                echo json_encode($response);
            }
            else
            {
                // Else the username and/or password was invalid! Create an array, json_encode it and echo it out
                $response = array(
                    'logged' => false,
                    'message' => 'Invalid Username and/or Password'
                );
                echo json_encode($response);
            }
        
        }
    }
         
?>