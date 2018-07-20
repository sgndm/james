<?php

class Controller
{
	public function __construct()
   	{
   	}
	static $st_instance = null;
	public static function get()
   	{
      if ( Controller::$st_instance == null  )
         Controller::$st_instance = new Controller();
      return Controller::$st_instance;
   	}
	public function Q($val)
   	{
      return "'". $val. "'";
   	}
	public function MYSQLQ($val)
    {
        $ct = ConstantModel::get();
        $p1= $ct->getMYSQL();
        $server = $p1["server"]; // $host
        $user=$p1["user"];
        $password=$p1["password"];
        $database=$p1["database"];
        try
        {
            $con=mysqli_connect($server,$user,$password,$database);
            if (mysqli_connect_errno())
            {
                throw new Exception("Failed to connect to MySQL: " . mysqli_connect_error());
            }
            $val=mysqli_real_escape_string($con, $val);
        }
        catch (Exception $e)
        {
            $contr_result=  "ERROR ".  $e->getMessage() .  "\n";
            $db_result = null;
        }
        return "'". $val. "'";
    }
	public function AddHours($hour1, $hour2)
   	{
       $hour = 25;
       $sum = $hour1 + $hour2;
       if ($sum > 23)
       {
           if($sum == 24)
           {
               $hour = 0;
           }
           else
           {
               $hour = $sum - 24;
           }
       }
       else
       {
           $hour = $sum;
       }
       return $hour;
   	}
	public function execute($p_sql)
   	{
       return $this->calldb("EXECUTE",$p_sql);
   	}
	public function query($p1_sql)
   	{
       return $this->calldb("SELECT",$p1_sql);
   	}
	public function getRecordID($px_sql)
   	{
       return $this->getRecordField($px_sql);
   	}
	public function getRecordField($p1_sql)
   	{
       $l_reply="0";
       $l_result = $this->getRecord($p1_sql);
       if ( count($l_result) > 0  )
       {
          $l_reply = $l_result[0];
       }
       return $l_reply;
   	}
	public function getRecordTEXT($p1_sql)
   	{
       $l_reply="";
       $l_result = $this->getRecord($p1_sql);
       if ( count($l_result) > 0  )
       {
          $l_reply = $l_result[0];
       }
       return $l_reply;
   	}
	public function getRecord($p1_sql)
   	{
       $l_reply = null;
       $l_result = $this->calldb("SELECT",$p1_sql);
       if ( count($l_result) > 0  )
       {
          $l_reply  = $l_result[0];
       }
       return $l_reply;
   	}
	public function getRecordIds($p1_sql)
   	{
		$l_result = $this->calldb("SELECT",$p1_sql);
		return $l_result;
   	}
	public function calldb($action,$p1_sql)
   	{
		$ct = ConstantModel::get();
		$p1= $ct->getMYSQL();
		$server=$p1["server"];
		$user=$p1["user"];
		$password=$p1["password"];
		$database=$p1["database"];
		try
    	{
       		$contr_result= "";
			$con=mysqli_connect($server,$user,$password,$database);
			if (mysqli_connect_errno())
       		{
           		throw new Exception("Failed to connect to MySQL: " . mysqli_connect_error());
			}
       		$db_result = mysqli_query($con,$p1_sql);
			if ( $db_result == false )
       		{
          		throw new Exception("Query failed ...please contact support");
								$message = "SQL Error Found \n SQL : $p1_sql";
	          	  // $this->sendDataBaseError($message);
								$contr_result.=$message;
								//print_r($message);
       		}
       		if ( $action == "EXECUTE" )
       		{
                  $contr_result.=$db_result;
			}
       		else if ( $action == "SELECT" )
       		{
               $all_record= array();
				$rid=0;
				while($row = mysqli_fetch_array($db_result))
               	{
                    $all_record[$rid]= $row;
					$rid++;
				}
               $contr_result=$all_record;
       		}
    	}
    	catch (Exception $e)
    	{
        	$contr_result=  "ERROR ".  $e->getMessage() .  "\n";
        	$db_result = null;
					$message = "SQL Error Found \n SQL : $p1_sql";
	       // $this->sendDataBaseError($message);
				 $contr_result.=$message;
    	}
    	mysqli_close($con);
    	return $contr_result;
  	}
	public function sendMail($p_dt)
  	{
       $to = $p_dt->{"to"} ;
       $subject = $p_dt->{"subject"};
       $message = $p_dt->{"message"};
       require_once("include_sendgrid.php");
       $ct = ConstantModel::get();
       $p1= $ct->getSendGrid();
       $from = $p1["mail_from"];
       $cc   = $p1["mail_cc"];
       $headers = "From:" . $from . ", Content-type: text/html,";
       $p_user=$p1["user"];
       $p_password=$p1["password"];
       $sendgrid = new SendGrid($p_user,$p_password);
       $mail     = new SendGrid\Mail();
       $mail->addTo($to)->
              setFrom($from)->
              setSubject($subject)->
              setText($message);
       $res=$sendgrid->web->send($mail);
  	}
	public function sendRequest($to, $subject, $message, $from)
  	{
       require_once("include_sendgrid.php");
       $ct = ConstantModel::get();
       $p1= $ct->getSendGrid();
       $headers = "From:" . $from . ", Content-type: text/html,";
       $p_user=$p1["user"];
       $p_password=$p1["password"];
       $sendgrid = new SendGrid($p_user,$p_password);
       $mail     = new SendGrid\Mail();
       $mail->addTo($to)->
              setFrom($from)->
              setSubject($subject)->
              setText($message);
       $res=$sendgrid->web->send($mail);
  	}
	public static function createUniqueID($allowed = 32)
  	{
    $baseStr = time() . rand(0, 1000000) . rand(0, 1000000);
    $md5Hash = md5($baseStr);
    if($allowed < 32)
    {
      $md5Hash = substr($md5Hash, 0, $allowed);
    }
    return $md5Hash;
  	}
	public function verifyUser($p_sid)
  	{
         $sql = "select COALESCE(max(id) ,0) from user where sid = " . $this->MYSQLQ($p_sid);
         $l_id = $this->getRecordID($sql);
         if ( $l_id > 0 )
         {
             $sql = "update user set  verified = 'Y' where sid = " . $this->MYSQLQ($p_sid);
             $l_res  = $this->execute($sql);
         }
         else
         {
             $l_res  = "0";
         }

         return $l_res;
  	}
	public function isvaliduser($p_sid)
  	{
         $this->session_reset() ;
         $data = array();
         $sql = "select id, email ,first_name,last_name, phone, user_type from user where  sid =  " . $this->MYSQLQ($p_sid) ;
         $l_record  = $this->getRecord($sql);
         if ( count($l_record) > 0  && $l_record[0]  > 0 )
         {
            $c = new config();
            $l_id=$l_record["id"];
            $l_email=$l_record["email"];
            $l_role=$l_record["user_type"];
            $l_first_name=$l_record["first_name"];
            $l_last_name=$l_record["last_name"];
            $l_phone=$l_record[4];
            $logindata = array();
            $c->user_type = $l_role;
            $c->sid = "";
            $c->user_id =  $l_id;
            $c->user_email =  $l_email;
            $c->user_first_name = $l_first_name;
            $c->user_last_name =  $l_last_name;
            $c->user_phone =  $l_phone;
            $c->user_name =  $l_first_name . " " . $l_last_name;
            $st = json_encode($c);
            $_SESSION["config"]=$st;
            $l_res = $l_id;
       }
       else
       {
             $l_res = "0";
             $this->session_reset();
       }

       $l_res =$sql;

       return $l_res;
  	}
	public function getSessionParamValue($p_id)
  	{
      $l_res="";
      if ( isset( $_SESSION[$p_id] ) )
          $l_res .= $_SESSION[$p_id];
      return $l_res;
  	}
	public function getGetParamValue($p_id)
  	{
      if ( isset( $_GET[$p_id] ) )
          return $_GET[$p_id];
      else
          return "";
  	}
	public function getPostParamValue($p_id)
  	{
      if ( isset( $_POST[$p_id] ) )
          return $_POST[$p_id];
      else
          return "";
  	}
	public function formatPhone($p_phone)
  	{
        $l_res=$p_phone;
        if ( strlen($p_phone) > 0 )
        {
           $p_phone .= "       ";
           $l_res = substr($p_phone, 0, 3) . "-" . substr($p_phone, 3, 3) . "-" . substr($p_phone,6);
        }
        return $l_res;
  	}
	public function logme($mess)
  	{
      $file = "check.log";
      file_put_contents($file, "\n".$mess."\n", FILE_APPEND | LOCK_EX);
 	}
	public function getServerError()
  	{
     	return "Server Error ...please contact support ";
  	}
	public function usdfmt($val)
  	{
    if ( is_numeric($val) )
       return "$". $this->fmt($val);
    else
       return $val;
  	}
	public function fmt($val)
  	{
    if ( is_numeric($val) )
       return number_format($val, 2, '.', ',');
    else
       return $val;
  	}
	public function getLastSymptomText($id)
  	{
      $logText = array();
      $logText[0] = "black";
      $sql = "SELECT TIMESTAMPDIFF(DAY, MAX(created_ts), now()) FROM user_activity WHERE log_type = 'symptom' AND patient_id = '$id'";
      $days = $this->getRecord($sql);
      if(is_numeric($days[0]))
      {
          if($days[0] >= 3)
          {
              $logText[0] = "#B50128";
          }
          else if($days[0] < 3)
          {
              $logText[0] = "black";
          }
          $logText[1] = "$days[0] days since last response";
      }
      else
      {
          $logText[0] = "#B50128";
          $logText[1] = "Never sent a response";
      }
      return $logText;
  	}
	public function getFileExtension($pfl)
  	{
       $info = pathinfo($pfl);
       return $info['extension'];
  	}
	public function checkSessionValue($key, $val)
   	{
        $retval = false;
        if((isset($_SESSION[$key])) && ($_SESSION[$key]==$val))
        {
            $retval = true;
        }
        return $retval;
    }
	public function session_reset()
    {
           $c = new config();
           $c->user_id = "0";
           $c->user_email = "";
           $c->user_phone = "";
           $c->user_first_name  = "";
           $c->user_last_name = "";
           $c->user_name = "";
           $c->user_type = "";
           $st = json_encode($c);
           $_SESSION["config"]=$st;
    }
	public function getConfigValue($id)
    {
         $res="";
         $lx="";
         $config=$this->getConfig();
         if ( $id == "user_id" )
           $lx=$config->user_id;
         if ( $id == "user_email" )
           $lx=$config->user_email;
         if ( $id == "user_name" )
           $lx=$config->user_name;
         if ($id == "user_type")
           $lx=$config->user_type;
         return $lx;
    }
	public function getUserID()
    {
         return $this->getConfigValue("user_id");
    }
	public function getUserType()
    {
        return $this->getConfigValue("user_type");
    }
	public function getUserEmail()
    {
         return $this->getConfigValue("user_email");
    }
	public function getUserName()
    {
         return $this->getConfigValue("user_name");
    }
	public function getConfig()
    {
         $lx=$this->getSessionParamValue("config");
         if ( $lx==null || $lx=="")
         {
            $this->session_reset();
             $lx=$this->getSessionParamValue("config");
         }
         $config= json_decode($lx);
         return $config;
   }
	public function gohome()
   {
          header("Location: index.php");
   }
	public function go_home()
   {
          header("Location: home.php");
   }
	public function resetsession($p_user_id)
  	{
         $this->session_reset() ;
         $data = array();
         $sql = "select id, email ,first_name,last_name, phone, user_type from user where  id =  " . $this->MYSQLQ($p_user_id) ;
         $l_record  = $this->getRecord($sql);
         if ( count($l_record) > 0  && $l_record[0]  > 0 )
         {
            $c = new config();

            $l_id=$l_record["id"];
            $l_email=$l_record["email"];
            $l_first_name=$l_record["first_name"];
            $l_last_name=$l_record["last_name"];
            $l_phone=$l_record["phone"];
            $l_type=$l_record["user_type"];
            $c->sid = "";
            $c->user_id =  $l_id;
            $c->user_type = $l_type;
            $c->user_email =  $l_email;
            $c->user_first_name = $l_first_name;
            $c->user_last_name =  $l_last_name;
            $c->user_phone =  $l_phone;
            $c->user_name =  $l_first_name . " " . $l_last_name;

            $st = json_encode($c);
            $_SESSION["config"]=$st;
            $l_res = $l_id;
       }
       else
       {
             $l_res = "0";
             $this->session_reset();
       }

       $l_res =$sql;

       return $l_res;
  	}
	public function mytrim($p_str)
  {
      if ( $p_str !=  null  && strlen($p_str) > 0  )
          $p_str = trim($p_str);
       return $p_str;
   }
	public function downloadFile($p_file_name, $p_file_type)
   	{
                $p_base_file_name=basename($p_file_name);
                //$mime = 'application/force-download';
                $mime = 'text/csv';
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private',false);
                header('Content-Type: '.$mime);
                header("Content-Disposition: attachment; filename=$p_base_file_name");
                header('Content-Transfer-Encoding: binary');
                header('Connection: close');
                readfile($p_file_name);
                //unlink($p_file_name);
   	}
	public function lb_chkpwd($p_username,$p_pwd)
   	{
    $res="";
    $sql = "select id, sid,verified,first_name,last_name  " .
                     " from user where ( upper(email) = " . $this->MYSQLQ(strtoupper($p_username)) .
                     "  or  upper(username) = " . $this->MYSQLQ(strtoupper($p_username)) . ")" .
                      " and password = " . $this->MYSQLQ($p_pwd);
      $rec = $this->getRecord($sql);
    $ln = count($rec);
    if ( $ln > 0 )
    {
      $i=1;
      $userid=$rec["id"];
      $username=$rec["email"];
      $email=$rec["email"];
      $salt=$rec["salt"];
      $pwd=$rec["password"];
      $first_name=$rec["first_name"];
      $last_name=$rec["last_name"];
      $ha = hash('sha256', $salt . $p_pwd);
      $res["userid"] = $userid;
      $res["user_id"] = $userid;
      $res["username"] = $username;
      $res["status"] = "success";
      $res["email"] = $email;
      $res["message"] = "matched";
      $res["first_name"] = $first_name;
      $res["last_name"] = $last_name;
      }
      else
      {
         $res["username"] = $p_username;
         $res["status"] = "failure";
         $res["message"] = "please enter valid password";
      }
   		return $res;
 	}
	public function hasValue($_str)
   	{
     if ( is_null ( $_str ) || strlen(trim($_str)) == 0 || trim($_str) == "" || $_str == "none" )
         return FALSE;
     return TRUE;
   	}
	public function isEmpty($_str)
   	{
     if ( $this->hasValue($_str) )
        return FALSE;
     else
        return TRUE;
   	}
	public function getval($t9_obj,$pval)
   	{

     $t_res="";
     if ( isset($t9_obj->{$pval} ))
     {
            $t_res=$t9_obj->{$pval};
     }
     return $t_res;
   	}
	public function sendError($l_message)
	{
	   $to = "john@promd.co" ;
       $subject = "Post Error Caught";
       $message = $l_message;
       require_once("include_sendgrid.php");
       $ct = ConstantModel::get();
     //   $p1= $ct->getSendGrid();
     //   $from = $p1["mail_from"];
     //   $cc   = $p1["mail_cc"];
     //   $headers = "From:" . $from . ", Content-type: text/html,";
     //   $p_user=$p1["user"];
     //   $p_password=$p1["password"];
     //   $sendgrid = new SendGrid($p_user,$p_password);
     //   $mail     = new SendGrid\Mail();
     //   $mail->addTo($to)->
     //          setFrom($from)->
     //          setSubject($subject)->
     //          setText($message);
     //   $res=$sendgrid->web->send($mail);
	   // $to = "kylie@promd.co" ;
	   // $mail     = new SendGrid\Mail();
     //   $mail->addTo($to)->
     //          setFrom($from)->
     //          setSubject($subject)->
     //          setText($message);
     //   $res=$sendgrid->web->send($mail);
	}
	public function sendDataBaseError($l_message)
	{
	   $to = "webmaster@promd.co" ;
       $subject = "Error Caught";
       $message = $l_message;
       require_once("include_sendgrid.php");
       $ct = ConstantModel::get();
       $p1= $ct->getSendGrid();
       $from = $p1["mail_from"];
       $cc   = $p1["mail_cc"];
       $headers = "From:" . $from . ", Content-type: text/html,";
       $p_user=$p1["user"];
       $p_password=$p1["password"];
       $sendgrid = new SendGrid($p_user,$p_password);
       $mail     = new SendGrid\Mail();
       $mail->addTo($to)->
              setFrom($from)->
              setSubject($subject)->
              setText($message);
       $res=$sendgrid->web->send($mail);
	}
	public function getUTC($start_time, $time_zone)
	{
		$utc_time = date("Y-m-d H:i:s", strtotime($start_time) - ($time_zone * 3600));
		return $utc_time;
	}
	public function getLocal($start_time, $time_zone)
	{
		$local_time = date("Y-m-d H:i:s", strtotime($start_time) + ($time_zone * 3600));
		return $local_time;
	}
} // controller
?>
