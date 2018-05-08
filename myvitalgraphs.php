<?php require_once("init_setup.php") ?>
<?php include('head_dashboard.php') ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $dx = DataModel::get(); 

     $l_fromdt = $ctrl->getPostParamValue("param1");
     $l_todt   = $ctrl->getPostParamValue("param2");
     $l_charttype = $ctrl->getPostParamValue("param3");
     $l_datatype = $ctrl->getPostParamValue("param4");
     $l_information = $ctrl->getPostParamValue("param5");
     $l_curtab = $ctrl->getPostParamValue("param6");
     $l_calledby = $ctrl->getPostParamValue("param7");
     //$p_patient_id = 0;

    if ( $ctrl->isEmpty($p_patient_id) )
    {
       $p_patient_id = $_SESSION["view_patient_id"];
    }
       $sql = "SELECT discharge_diagnosis FROM patient where id = $p_patient_id";
       $diagnosis = $ctrl->getRecordTEXT($sql);
       $sql = "SELECT vital FROM vital_groups WHERE diagnosis_id = $diagnosis";
       $list = $dx->getRecordIds($sql);
	   $sql = "SELECT vital_id FROM vitals WHERE 
	   			patient_id = $p_patient_id AND 
	   			isactive = 'Y' ";
	   $patient_vitals = $dx->getOnlyRecords($sql);
	   $vital_total = $patient_vitals->{"total_records"};
	   $sql = "SELECT id FROM vitals_list";
	   $vital_list = $dx->getRecordIds($sql);
	   $htmlToAdd = "";
	   foreach ($vital_list as $k => $v) 
      {
          $value = $v[0];
		  $htmlToAdd .= "<div id='chart_$value'></div>
    		<BR/>";
	  }
     
    
    if ( $ctrl->isEmpty($l_charttype) )
        $l_charttype = "LINE";
    if ( $ctrl->isEmpty($l_datatype) )
        $l_datatype = "DAILY";

    if ( $ctrl->isEmpty($l_calledby) )
        $l_calledby = "other";

    if ( $ctrl->isEmpty($l_fromdt) )
        $l_fromdt = date("m/01/Y"); 

    if ( $ctrl->isEmpty($l_todt) )
        $l_todt = date("m/d/Y"); 


    // special logic for the date
    if ( $l_calledby == "other" ) 
    {
       if ( $l_datatype == "HOURLY" ) 
       {
          $l_fromdt = date("m/d/Y"); 
          $l_todt = date("m/d/Y"); 
       }
       $l_todt = date("m/d/Y"); 
       if ( $l_datatype == "DAILY" ) 
       {
          $l_x1 = date("Y-m-d"); 
     $sql="select DATE_FORMAT(DATE_ADD(\"".$l_x1."\",INTERVAL -15 DAY),'%m/%d/%Y') ";
          $l_fromdt = $ctrl->getRecordText($sql);
       }
       if ( $l_datatype == "WEEKLY" ) 
       {
          $l_x1 = date("Y-m-d"); 
     $sql="select DATE_FORMAT(DATE_ADD(\"".$l_x1."\",INTERVAL -3 MONTH),'%m/%d/%Y') ";
          $l_fromdt = $ctrl->getRecordText($sql);
       }
       if ( $l_datatype == "MONTHLY" ) 
       {
          $l_x1 = date("Y-m-d"); 
     $sql="select DATE_FORMAT(DATE_ADD(\"".$l_x1."\",INTERVAL -1 YEAR),'%m/%d/%Y') ";
          $l_fromdt = $ctrl->getRecordText($sql);
       }
       if ( $l_datatype == "QUARTERLY" ) 
       {
          $l_x1 = date("Y-m-d"); 
     $sql="select DATE_FORMAT(DATE_ADD(\"".$l_x1."\",INTERVAL -3 YEAR),'%m/%d/%Y') ";
          $l_fromdt = $ctrl->getRecordText($sql);
       }
    }
    $ctrl = Controller::get(); 
    $l_user_id= $ctrl->getUserID();

    $user_id= $ctrl->getUserID();
?>
<link href="css/patient.css" media="all" rel="stylesheet" type="text/css" />
<input type="hidden" value="<?php echo $l_curtab; ?>" id="recordvital_tabname" /a>
<input type="hidden" value="myvitalgraphs.php" id="recordvital_dashboard_name" /a>
<input type="hidden" value="other" id="recordvital_calledby" /a>
<div class="span12">
</div>
<?php
?>
<BR/>
<BR/>
<BR/>

<div id="dashboard_input" >
<ul class='nav nav-tabs' id='myTab' style="margin-top: 10px;">
        

<input type="hidden" vaule="date" ?>
        <li class="span3">
        Frequency  <select   onchange="calldashboardvital()" class="span3" id="recordvital_datatype">
<?php
   $l_str="HOURLY,DAILY,WEEKLY,MONTHLY,QUARTERLY";
   $myar = explode(",",$l_str);
   $l_tot = count($myar);
   for ( $idx=0;$idx< $l_tot; $idx++)
   {
       $l_nm=$myar[$idx];
       $lselected = "";
       if ( $ctrl->isEmpty($l_datatype) )
         $l_datatype = $l_nm;
       if ( $l_datatype  == $l_nm )
         $lselected = "SELECTED";
       echo "<option value=\"$l_nm\" $lselected >$l_nm</option>";
   }
?>
        </select>
        </li>
        <li class="span1"></li>
        <li class="span3">From Date 
<input onchange="calldashboardvital_date('from')" value="<?php echo $l_fromdt; ?>" placeholder="Date"  class="span2" type="text" id="recordvital_from_date" /></li>
        <li class="span3">To Date <input  onchange="calldashboardvital_date('to')" value="<?php echo $l_todt; ?>" placeholder="Date"  class="span2" type="text" id="recordvital_to_date" /></li>

</ul>
</div>

<?php
   $l_label = $l_datatype;

   if ( $l_datatype == "HOURLY" )
        $l_label = "HOUR";
   if ( $l_datatype == "MONTHLY" )
        $l_label = "MONTH";
   if ( $l_datatype == "DAILY" )
        $l_label = "DAY";
   if ( $l_datatype == "WEEKLY" )
        $l_label = "WEEK";
   if ( $l_datatype == "QUARTERLY" )
        $l_label = "QUARTER";
?>


<div class="span12" id="interact" >
    <?php echo $htmlToAdd; ?>
</div>

    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

<?php
      $l_chart_prog="";

      $l_base_chart_options = "pointSize: 2,";
      if ( $l_charttype == "SCATTER" )
         $l_base_chart_options  .= "hAxis: {title: 'Time $l_datatype', minValue :0 }, vAxis: {title: 'Patient',  minValue :0},";
      $l_base_chart_options  .= "width: 1000, height: 450,";
      $l_base_chart_options  .= "chartArea: {left: '7%' } ,";
      $l_base_chart_options  .= 
           "hAxis: {title: 'Time $l_datatype', minValue :0 , slantedText:true, slantedTextAngle:60, position: 'bottom' },";
      $l_base_chart_options  .= 
      $l_base_chart_options  .= 
           " legend: { position : 'none', },";
      $l_base_chart_options  .= 
           " curveType: 'none',";
           
	  	for ( $idx=0;$idx< $vital_total; $idx++)
   		{
   			$rec = $patient_vitals->{"record"}[$idx];
			$current_vital_id  =  $rec["vital_id"];
			$current_vital_name = "UNKNOWN";
			$current_graph_type = "BAR";
			$current_graph_min = 0;
			$current_graph_max = 0;
			$sql = "SELECT vital, graph_type, graph_min, graph_max FROM vitals_list WHERE id = $current_vital_id";
			$vital_details = $dx->getOnlyRecords($sql);
			$tot_details = $vital_details->{"total_records"};
			for($i=0;$i<$tot_details;$i++)
			{
				$r = $vital_details->{"record"}[$i];
				$current_vital_name = $r["vital"];
				$current_graph_type = $r["graph_type"];
				$current_graph_min = $r["graph_min"];
				$current_graph_max = $r["graph_max"];
			}
			$p_obj=json_decode("{}");
      		$p_obj->{"frdt"}=$l_fromdt;
      		$p_obj->{"todt"}=$l_todt;
      		$p_obj->{"diagnosis"}=$diagnosis;
      		$p_obj->{"datatype"}=$l_datatype;
      		$l_charttype = $current_graph_type; //$l_charttype;
      		$p_obj->{"charttype"}=$l_charttype;
      		$p_obj->{"year"}=date("Y"); 
      		$p_obj->{"information"}=$l_information;
      		$p_obj->{"calledby"}=$l_calledby;
      		$p_obj->{"graphvital"}=$current_vital_name;
      		$l_chart_data = "['Date','$current_vital_name'],";
      		$p_obj->{"functype"}="vitals";
      		$p_obj->{"tablename"}="vitals_reported";
      		$p_obj->{"fieldname"}="distinct patient_id";
      		$l_chartid="chart_" . $current_vital_id;
      		$p_obj->{"chartid"}=$l_chartid;
      		$p_obj->{"patient_id"}=$p_patient_id;
      		$p_obj= $dx->createVitalChart($p_obj);
      		$l_chart_data.= $p_obj->{"dataval"};
      		$l_chart_options = $l_base_chart_options. "title: '$current_vital_name',";
      		$l_chart_prog = get_chartprog($l_charttype,$l_chartid);
			
			
?> 
 	var data = google.visualization.arrayToDataTable([
          <?php echo $l_chart_data; ?> ]);
          
        var options = {
           <?php echo $l_chart_options; ?>
          //hAxis: {title: 'Time (<?php echo $l_datatype; ?>)', },
          vAxis: {title: '<?php echo $current_vital_name; ?>', minValue :<?php echo $current_graph_min; ?>, maxValue : <?php echo $current_graph_max; ?> },
          //seriesType: 'line', //series: {1: {type: 'line'}},
         };

        <?php echo $l_chart_prog; ?>
        chart.draw(data, options);
        
 
 <?php
   		}//end for loop
?>  

      }
      
    </script>
  <script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="js/jquery.timepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="css/jquery.timepicker.css" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#recordvital_from_date" ).datepicker();
    $( "#recordvital_to_date" ).datepicker();
  });
    </script>



<script>
    $("#tab-attract").attr('class',"tab-pane");
    $("#tab-<?php echo $l_curtab; ?>").attr('class',"tab-pane active");
</script>
<?php

  function get_chartprog($p_charttype,$p_chartid)
  {
      if ( $p_charttype == "BAR" )
        $res= "var chart = new google.visualization.ColumnChart(document.getElementById('$p_chartid'));";
      else if ( $p_charttype == "COMBOCHART" )
      {
        $res= "var chart = new google.visualization.ComboChart(document.getElementById('$p_chartid'));";
      }
      else if ( $p_charttype == "SCATTER"    )
        $res= "var chart = new google.visualization.ScatterChart(document.getElementById('$p_chartid'));";
      else
        $res= "chart = new google.visualization.LineChart(document.getElementById('$p_chartid'));";

      return $res;
   }
?>

</section>
        </div>
        </body>
</html>

