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
     $sql="select DATE_FORMAT(DATE_ADD(\"".$l_x1."\",INTERVAL -1 MONTH),'%m/%d/%Y') ";
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
<input type="hidden" value="<?php echo $l_curtab; ?>" id="record_tabname" /a>
<input type="hidden" value="mydashboard.php" id="record_dashboard_name" /a>
<input type="hidden" value="other" id="record_calledby" /a>
<?php
?>
<BR/>
<BR/>
<BR/>

<div id="dashboard_input" >
<ul class='nav nav-tabs' id='myTab' style="margin-top: 10px;">
        <li class="span2">From Date 
<input onchange="calldashboard_date('from')" value="<?php echo $l_fromdt; ?>" placeholder="Date"  class="span2" type="text" id="record_from_date" /></li>
        <li class="span2">To Date <input  onchange="calldashboard_date('to')" value="<?php echo $l_todt; ?>" placeholder="Date"  class="span2" type="text" id="record_to_date" /></li>

<input type="hidden" vaule="date" ?>
        <li class="span2">
        Frequency  <select   onchange="calldashboard()" class="span2" id="record_datatype">
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

        <li class="span2">
        Info Type <select onchange="calldashboard()" class="span2" id="record_information">
<?php
   $l_str="Cumulative,Non Cumulative";
   $myar = explode(",",$l_str);
   $l_tot = count($myar);
   for ( $idx=0;$idx< $l_tot; $idx++)
   {
       $l_nm=$myar[$idx];
       $lselected = "";
       if ( $ctrl->isEmpty($l_information) )
         $l_information = $l_nm;
       if ( $l_information == $l_nm )
         $lselected = "SELECTED";
       echo "<option value=\"$l_nm\" $lselected >$l_nm</option>";
   }
?>
        </select>
        </li>
        <li class="span2">
        Chart Type <select onchange="calldashboard()" class="span2" id="record_charttype">
<?php
   $l_str="LINE,BAR"; // SCATTER commented 
   $myar = explode(",",$l_str);
   $l_tot = count($myar);
   for ( $idx=0;$idx< $l_tot; $idx++)
   {
       $l_nm=$myar[$idx];
       $lselected = "";
       if ( $ctrl->isEmpty($l_charttype) )
         $l_charttype = $l_nm;
       if ( $l_charttype == $l_nm )
         $lselected = "SELECTED";
       echo "<option value=\"$l_nm\" $lselected >$l_nm</option>";
   }
?>
        </select>
        </li>
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
    <div id="chart6_div" style="height:500;"></div>
    <BR/>
    <div id="chart15_div" style="height:500;"></div>
    <BR/>
    <div id="chart2_div" style="height:500;"></div>
    <BR/>
    <div id="chart3_div" style="height:500;"></div>
    <BR/>
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
      $l_base_chart_options  .= "width: 1100, height: 450,";
      $l_base_chart_options  .= "chartArea: {left: '5%' } ,";
      $l_base_chart_options  .= 
           "hAxis: {title: 'Time $l_datatype', minValue :0 , slantedText:true, slantedTextAngle:60, position: 'bottom' },";
      $l_base_chart_options  .= 
      $l_base_chart_options  .= 
           " legend: { position : 'none', },";
      $l_base_chart_options  .= 
           " curveType: 'none',";

      $p_obj=json_decode("{}");
      $p_obj->{"frdt"}=$l_fromdt;
      $p_obj->{"todt"}=$l_todt;
      $p_obj->{"datatype"}=$l_datatype;
      $p_obj->{"charttype"}=$l_charttype;
      $p_obj->{"year"}=date("Y"); 
      $p_obj->{"information"}=$l_information;
      $p_obj->{"calledby"}=$l_calledby;

      $l_chart_data = "['Date','Signed Patient($l_label)'],";
      $p_obj->{"functype"}="signinpatient";
      $p_obj->{"tablename"}="user_activity_login";
      $p_obj->{"fieldname"}="patient_id";

      $l_chartid="chart2_div";
      $p_obj->{"chartid"}=$l_chartid;
      $p_obj->{"patient_id"}=$p_patient_id;
      $p_obj= $dx->mychart1($p_obj);
      $l_chart_data.= $p_obj->{"dataval"};
      $l_chart_options = $l_base_chart_options. "title: 'Signed In  Patient',";
      $l_chart_prog = get_chartprog($l_charttype,$l_chartid);
?>
        var data = google.visualization.arrayToDataTable([
          <?php echo $l_chart_data; ?> ]);

        var options = {
           <?php echo $l_chart_options; ?>
          //hAxis: {title: 'Time (<?php echo $l_datatype; ?>)', },
          //vAxis: {title: 'Patient', },
         };

        <?php echo $l_chart_prog; ?>
        chart.draw(data, options);

<?php
      $p_obj=json_decode("{}");
      $p_obj->{"frdt"}=$l_fromdt;
      $p_obj->{"todt"}=$l_todt;
      $p_obj->{"datatype"}=$l_datatype;
      $p_obj->{"charttype"}=$l_charttype;
      $p_obj->{"year"}=date("Y"); 
      $p_obj->{"information"}=$l_information;
      $p_obj->{"calledby"}=$l_calledby;
      $p_obj->{"groupby"}="date";

      $l_chart_data = "['Date','Videos Watched($l_label)'],";
      $p_obj->{"functype"}="videoswatched";
      $p_obj->{"tablename"}="user_activity_video";
      $p_obj->{"fieldname"}="patient_id";

      $l_chartid="chart3_div";
      $p_obj->{"chartid"}=$l_chartid;
      $p_obj->{"patient_id"}=$p_patient_id;
      $p_obj= $dx->mychart1($p_obj);
      $l_chart_data.= $p_obj->{"dataval"};
      $l_chart_prog = get_chartprog($l_charttype,$l_chartid);
      $l_chart_options = $l_base_chart_options. "title: 'Videos Watched',";

?>
        var data = google.visualization.arrayToDataTable([
          <?php echo $l_chart_data; ?> ]);

        var options = {
           <?php echo $l_chart_options; ?>
          //hAxis: {title: 'Time (<?php echo $l_datatype; ?>)', },
          //vAxis: {title: 'Patient', },
         };

        <?php echo $l_chart_prog; ?>
        chart.draw(data, options);


<?php
      $p_obj=json_decode("{}");
      $p_obj->{"frdt"}=$l_fromdt;
      $p_obj->{"todt"}=$l_todt;
      $p_obj->{"datatype"}=$l_datatype;
      $l_charttype = "COMBOCHART"; //$l_charttype;
      $p_obj->{"charttype"}=$l_charttype;
      $p_obj->{"year"}=date("Y"); 
      $p_obj->{"information"}=$l_information;
      $p_obj->{"calledby"}=$l_calledby;

      $p_obj->{"functype"}="painpills";
      $p_obj->{"tablename"}="user_narcotics";
      $p_obj->{"fieldname"}="distinct patient_id";
      $l_chartid="chart15_div";
      $p_obj->{"chartid"}=$l_chartid;
      $p_obj->{"patient_id"}=$p_patient_id;
      $p_obj= $dx->mychart1($p_obj);
      $med1 = $p_obj->{"med1"};
      $med2 = $p_obj->{"med2"};
      $med3 = $p_obj->{"med3"};
      $l_chart_data = "['Date','$med1','Limit','$med2','$med3' ],";
      $l_chart_data.= $p_obj->{"dataval"};
      $l_chart_options = $l_base_chart_options. "title: 'Narcotics Intake',";
      $l_chart_prog = get_chartprog($l_charttype,$l_chartid);
?>
        var data = google.visualization.arrayToDataTable([
          <?php echo $l_chart_data; ?> ]);

        var options = {
           <?php echo $l_chart_options; ?>
          //hAxis: {title: 'Time (<?php echo $l_datatype; ?>)', },
          vAxis: {title: 'Intake %', minValue :0},
          seriesType: 'bars', series: {1: {type: 'line'}},
         };

        <?php echo $l_chart_prog; ?>
        chart.draw(data, options);

<?php
      $p_obj=json_decode("{}");
      $p_obj->{"frdt"}=$l_fromdt;
      $p_obj->{"todt"}=$l_todt;
      $p_obj->{"datatype"}=$l_datatype;
      $l_charttype = "COMBOCHART"; //$l_charttype;
      $p_obj->{"charttype"}=$l_charttype;
      $p_obj->{"year"}=date("Y"); 
      $p_obj->{"information"}=$l_information;
      $p_obj->{"calledby"}=$l_calledby;

      $l_chart_data = "['Date','Medical Compliance($l_label)','Goal'],";
      $p_obj->{"functype"}="medicalcompliance";
      $p_obj->{"tablename"}="user_activity_medication";
      $p_obj->{"fieldname"}="distinct patient_id";
      $l_chartid="chart6_div";
      $p_obj->{"chartid"}=$l_chartid;
      $p_obj->{"patient_id"}=$p_patient_id;
      $p_obj= $dx->mychart1($p_obj);
      $l_chart_data.= $p_obj->{"dataval"};
      $l_chart_options = $l_base_chart_options. "title: 'Medication Compliance',";
      $l_chart_prog = get_chartprog($l_charttype,$l_chartid);
?>
        var data = google.visualization.arrayToDataTable([
          <?php echo $l_chart_data; ?> ]);

        var options = {
           <?php echo $l_chart_options; ?>
          //hAxis: {title: 'Time (<?php echo $l_datatype; ?>)', },
          vAxis: {title: 'Compliance %', minValue :0, maxValue : 100 },
          seriesType: 'bars', series: {1: {type: 'line'}},
         };

        <?php echo $l_chart_prog; ?>
        chart.draw(data, options);
      }
    </script>
  <script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="js/jquery.timepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="css/jquery.timepicker.css" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#record_from_date" ).datepicker();
    $( "#record_to_date" ).datepicker();
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

