
<?php require_once("init_setup.php") ?>
<?php include('head.php') ?>

<div class="row" >

<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $s_obj=json_decode("{}");
      $l_obj = $dx->getPushNotificationlog($s_obj);

      $l_total_rows = $l_obj->{"total_records"};
?>

<?php  if ( $l_total_rows  > 0 ) { ?>
<div class="span10"> 

<?
      $title="Push Notification Log";
      $show_pageno=1;
      $func_name="";
      $tot_rows=$l_total_rows;
      $default_view_rows=100;
      include("include/include_pageindex.php");
?>
<table border=1>
<tr >
<th >Patient</th>
<th >Email</th>
<th >Alert</th>
<th >Subject</th>
<th >Message</th>
<th >Date</th>
</tr>

<?php
      for ( $idx =0; $idx <$l_total_rows; $idx++)
      {
       $c_no=$idx + 1;
       if ( $c_no  < $start_rowid || $c_no > $end_rowid )
       {
          continue;
       }
         $krec=$l_obj->{"record"}[$idx];
         $l_to_email  =  $krec["to_email"];
         $l_name  =  $krec["first_name"]. " " . $krec["last_name"];
         $l_alert_type  =  $krec["alert_type"];
         $l_subject  =  $krec["subject"];
         $l_message  =  $krec["message"];
         $l_created_ts  =  $krec["created_ts_fmt"];
?>
<tr bgcolor="white"
>
<td style="width:15%" align="left" ><?php echo $l_name; ?> </td>
<td style="width:15%" align="left" ><?php echo $l_to_email; ?> </td>
<td style="width:15%" align="left" ><?php echo $l_alert_type; ?> </td>
<td style="width:15%" align="left" ><?php echo $l_subject; ?></td>
<td style="width:15%" align="left" ><?php echo $l_message; ?> </td>
<td style="width:15%" align="left" ><?php echo $l_created_ts; ?> </td>
</tr>
<?php
      }
?>
</table>
</div><!--span-->
</div> <!--row-->
<?php  } else { ?>
<h4>Sorry no messages </h4>
<?php  } ?>
<?php include("foot.php") ?>
