
<?php require_once("init_setup.php") ?>
<?php include('graph_dashboard.php') ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get(); 
      $dx = DataModel::get(); 

   $p_patient_id = $ctrl->getSessionParamValue("mobile_patient_id");

    $l_charttype = "LINE";
    $l_datatype = "DAILY";
    $l_calledby = "other";

    $l_todt = date("m/d/Y"); 
    $l_x1 = date("Y-m-d"); 
    $sql="select DATE_FORMAT(DATE_ADD(\"".$l_x1."\",INTERVAL -7 DAY),'%m/%d/%Y') ";
    $l_fromdt = $ctrl->getRecordText($sql);
   $l_label = "DAY";
?>
<link href="css/patient.css" media="all" rel="stylesheet" type="text/css" />
<div class="span12" id="interact" >
<div id="chart6_div" style="height:300;"></div>
</div>

    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
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

      $l_chart_data = "['Date','Compliance($l_label)','Goal'],";
      $p_obj->{"functype"}="medicalcompliance";
      $p_obj->{"tablename"}="user_activity_medication";
      $p_obj->{"fieldname"}="distinct patient_id";
      $l_chartid="chart6_div";
      $p_obj->{"chartid"}=$l_chartid;
      $p_obj->{"patient_id"}=$p_patient_id;
      $p_obj= $dx->mychart1($p_obj);
      $l_chart_data.= $p_obj->{"dataval"};


      $l_base_chart_options = "pointSize: 2,";
      $l_base_chart_options  .= "width: '100%', height: '80%',";
//      $l_base_chart_options  .= "chartArea: {left: '8%' } ,";
      $l_base_chart_options  .= "hAxis: {title: 'Time $l_datatype', minValue :0 , slantedText:true, slantedTextAngle:60, position: 'bottom' },";
      $l_base_chart_options  .= 
           " legend: { position : 'top', },";
      $l_base_chart_options  .= 
           " curveType: 'none',";


      $l_chart_options = $l_base_chart_options;
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

