
<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
     $l_fromdt = $ctrl->getPostParamValue("param1");
     $l_todt   = $ctrl->getPostParamValue("param2");
     $l_rows   = $ctrl->getPostParamValue("param3");
     $l_cur_page   = $ctrl->getPostParamValue("param4");
     $l_next       = $ctrl->getPostParamValue("param5");
     $l_called_by       = $ctrl->getPostParamValue("called_by");
     $l_called_by = $_SESSION["patient_called_by"];
     $p_patient_id = $_SESSION["view_patient_id"];
     if ( $ctrl->isEmpty($l_rows) )
        $l_rows= 16;
     if ( $ctrl->isEmpty($l_cur_page) )
        $l_cur_page= 1;

     if ( $ctrl->isEmpty($l_fromdt) )
     {
        $l_todt = date("m/d/Y"); 
        $l_x1 = date("Y-m-d"); 
        $sql="select DATE_FORMAT(DATE_ADD(\"".$l_x1."\",INTERVAL -12 MONTH),'%m/%d/%Y') ";
        $l_fromdt = $ctrl->getRecordText($sql);
     }
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $data='{"patient_id":"' . $p_patient_id . '"}';
      $x_obj=json_decode($data);
      $x_obj->{"fromdt"} = $l_fromdt;
      $x_obj->{"todt"} = $l_todt;
      $x_obj->{"rows_limit"} = $l_rows;
      $x_obj->{"pageno"} = $l_cur_page;
      $x_obj->{"next"} = $l_next;
      $p_form_name="woundimage.php";
      $l_obj = $dx->getWoundImages_1($x_obj);
      $l_total_rows = $l_obj->{"total_records"};
      $l_cur_page=$l_obj->{"cur_page"};
      $l_end_page=$l_obj->{"end_page"};
?>
<style>
input[type="text"]
{
height:30px;
}
input[type="button"]
{
height:30px;
margin-bottom: 9px;
}
</style>
<div class="row">
<div class="span5">
<h4> Wound Images</BR>Page : <?php echo $l_cur_page."/".$l_end_page; ?></h4>
<?php
if  ( $l_called_by == "myhome" ) {
?>
<input type="button" height="40px" value="Go back" onclick="parent.gobackdashboard()" />
<?php
   }
?>
</div>
</div>
<div class="row">
    <div class="span6">
        <ul class='nav nav-tabs' id='myTab' style="margin-top: 10px;">
        <li class="span2">From Date
             <input onchange="callmywoundimage('date')" value="<?php echo $l_fromdt; ?>"  type="text" class="span2" id="record_from_date" />
        </li>
        <li class="span2">To Date
            <input  onchange="callmywoundimage('date')" value="<?php echo $l_todt; ?>"  class="span2" type="text" id="record_to_date" /> 
        </li>
        <li class="span2">View Rows
        <select onchange="callmywoundimage('count')" class="span2" id="record_rows">
<?php
   $l_str="16,24,32,48,96"; // SCATTER commented 
   $myar = explode(",",$l_str);
   $l_tot = count($myar);
   for ( $idx=0;$idx< $l_tot; $idx++)
   {
       $l_nm=$myar[$idx];
       $lselected = "";
       if ( $ctrl->isEmpty($l_rows) )
         $l_rows = $l_nm;
       if ( $l_rows == $l_nm )
         $lselected = "SELECTED";
       echo "<option value=\"$l_nm\" $lselected >$l_nm</option>";
   }
?>
        </select>
</li>
</ul>
    </div>
<div class="pagination span5">
<ul class="">
<li><a href="#" onclick="callmywoundimage('start')">Start</a></li>
<li><a href="#" onclick="callmywoundimage('prev')">Prev</a></li>
<li><a href="#" onclick="callmywoundimage('next')">Next</a></li>
<li><a href="#" onclick="callmywoundimage('end')">End</a></li>

</ul>
</div>
    
</div>

<div class="row">
<div class='span10'>
<input id="imagerecord_patient_id" type="hidden" value="<?php echo $p_patient_id; ?>" />
<input style="width:200px;height:30px;margin-left:20px" type="text" placeholder="Push Message"  id="imagerecord_pushmessage" />
<input type="button" onclick="callme('imagerecord,pushimage,')"  value="Send Push Notification" />
</div>
</div>


<style type='text/css'>
    #table-wrapper {
  position:relative;
}
#table-scroll {
  height:500px;
  overflow:auto;  
  margin-top:0px;
}
#table-wrapper table {
  width:100%;
    
}
#table-wrapper table * {
  background:transparent;
  color:black;
}
#table-wrapper table thead th .text {
  position:absolute;   
  top:-20px;
  z-index:2;
  height:15px;
  width:35%;
}
</style>

</head>

<input type="hidden" id="record_total_rows" value="<?php echo $l_total_rows; ?>" />
<input type="hidden" id="record_cur_page" value="<?php echo $l_cur_page; ?>" />

<?php  if ( $l_total_rows  > 0 ) { ?>
<div class="span10" >
<table border=1 calss="span12" >
<tr valign="top" >
<td class="span3">
<div id="table-wrapper">
  <div >
<table border=1 >
<tr>
<?php
      $ictr=0;
      for ( $idx =0; $idx <$l_total_rows; $idx++)
      {
         $rec=$l_obj->{"record"}[$idx];
         $l_patient_id  =  $rec["id"];
         $l_url =   $rec["url"];
		 $l_note =   $rec["reviewer_note"];
		 $l_reviewer_id =   $rec["reviewer_id"];
		 $l_rdate =   $rec["reviewed"];
		 $lastSpace = strrpos($l_url, '/');
		 $str1 = trim(substr($l_url, 0, $lastSpace));
		 $str2 = trim(substr($l_url, $lastSpace));
		 $fileName = substr($str2, 1);
		 $fileName = "/t_" . $fileName;
		 $thumbnail = $str1 . $fileName;
		 $l_dt =   $rec["created_ts_fmt"];
         $l_name =   $rec["first_name"]." " .$rec["last_name"];
		 $title = "";
		 if($l_reviewer_id == null || $l_reviewer_id == "")
		 {
			$title = "Has not been reviewed.";
	  	 }
		 else
		 {
		 	$sqlforname = "SELECT first_name FROM user WHERE id=$l_reviewer_id";
		 	$l_reviewer = $ctrl->getRecordTEXT($sqlforname); 
			$title = "$l_rdate - $l_reviewer : $l_note";
		 }
?>
<td class="span2">
<img title="<?php echo "$title"; ?>"  onmouseover="showwoundimageimage('<?php echo $l_patient_id; ?>','<?php echo $l_name; ?>', '<?php echo $l_url; ?>' )"  
   style="width:60px;height:60px" src="<?php echo $thumbnail; ?>" />
<BR/>
<p title="<?php echo "$title"; ?>">
<?php echo $l_dt; ?>
</p>
</td>
<?php
      $ictr++;
      if ( $ictr == 4  ) 
      {
            $ictr=0;
            echo "</tr><tr>";
      }
?>
      <?php
}
?>
</tr>
</table>
</div>
<?php  } ?>
</div>
</td>
<td class="span9"  width="600" >
  <img id="image_url" src="" width="600" height="300"  />
  <input type="hidden" id="image_patient_id" />
</td>
</tr>
</table>
</div>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script type="text/javascript">
  $(function() {
    $( "#record_from_date" ).datepicker();
    $( "#record_to_date" ).datepicker();
  });
  </script>
