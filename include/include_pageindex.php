<style type="text/css">
table.tablesorter { 
  font-size: 14px; 
  overflow:auto;
} 
table.tablesorter th { 
 background: url("assets/premium_icons/glyphicons_pro 3/glyphicons_halflings/png/glyphicons_halflings_149_sort.png");
 background-position: right 10px;
 font-size: 14px; 
 background-repeat:no-repeat;
 cursor: pointer; 
 height: auto; 
 padding: 5px 5px 5px 20px; 
 font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
}
} 
table.tablesorter td { 
 font-size: 14px; 
  color: black; 
  padding: 5px; 
 font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
} 
table.tablesorter .headerSortUp { 
  background-image: url("assets/premium_icons/glyphicons_pro 3/glyphicons_halflings/png/glyphicons_halflings_149_sort.png");
} 
table.tablesorter .headerSortDown { 
  background-image: url("assets/premium_icons/glyphicons_pro 3/glyphicons_halflings/png/glyphicons_halflings_149_sort.png");
}
</style>
<?php

    $incl_sort_image="&nbsp;<img src='assets/premium_icons/glyphicons_pro 3/glyphicons_halflings/png/glyphicons_halflings_149_sort.png'/>&nbsp;";
    $incl_sort_image="";
   $download_file = 0;
   $cur_page=$ctrl->getPostParamValue("param1");
   if ( $cur_page == null ) 
      $cur_page=$ctrl->getGetParamValue("param1");
   if ( $cur_page == -1 )  
   {
      $download_file = 1;
      $cur_page = 1;
   }

   if ( $cur_page == null || $cur_page == 0 )  
      $cur_page = 1;

   $rows_limit=$ctrl->getPostParamValue("param2");
   if ( $rows_limit == null || $rows_limit == 0 )  
      $rows_limit = $default_view_rows;

   $cur_page = intval($cur_page);
   $rows_limit = intval($rows_limit);

   $end_page=$tot_rows / $rows_limit;
   if ( $end_page > intval($end_page) ) 
      $end_page = intval($end_page)  + 1;

   if ( $end_page <  1 ) 
      $end_page = 1 ;

   $next_page = $cur_page  + 1;
   if ( $next_page > $end_page )
      $next_page = $end_page;

   $prev_page = $cur_page  - 1;
   if ( $prev_page < 1 ) 
      $prev_page = 1 ;

   $start_rowid = ( ( $cur_page -1 ) * $rows_limit  ) + 1 ;
   $end_rowid = $cur_page  * $rows_limit;
?>

<input id="end_page" name="end_page" value="<?php echo $end_rowid; ?>" type="hidden">
<input id="start_page" name="start_page" value="<?php echo $start_rowid; ?>" type="hidden">
<input name="next_page" value="<?php echo $next_page; ?>" type="hidden">
<input name="prev_page" value="<?php echo $prev_page; ?>" type="hidden">
<input name="cur_page" value="<?php echo $cur_page; ?>" type="hidden">
<?php
   if ( $show_pageno == 1  )
   {
?>
<div class="row" >
<div class="span4" >
<h5><?php echo $title; ?>&nbsp;&nbsp;Page : <?php echo $cur_page ." / ". $end_page ; ?></h5>
</div>
</div>
<?php if($title != "Red Flags") 
{ ?>
<div class="row">
<div class="btn-group" style="margin-left: 25px;">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Show
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="callchange('<?php echo "$title"; ?>', 'all')">All</a></li>
    <li><a href="#" onclick="callchange('<?php echo "$title"; ?>', 'active')">Only Active</a></li>
  </ul>
</div>
</div>
<?php 
} ?>

<div class="row">

<div class="pagination span5" style="margin-left: 25px;">
<ul class="pagination">
        <li><a href="#" onClick="goto_page('<?php echo $func_name; ?>','1','<?php echo $rows_limit; ?>')" >Start</a></li>
        <li><a href="#" onClick="goto_page('<?php echo $func_name; ?>','<?php echo $prev_page; ?>','<?php echo $rows_limit; ?>')" >Prev</a></li>
        <li><a href="#" onClick="goto_page('<?php echo $func_name; ?>','<?php echo $next_page; ?>','<?php echo $rows_limit; ?>')" >Next</a></li>
        <li><a href="#" onClick="goto_page('<?php echo $func_name; ?>','<?php echo $end_page; ?>','<?php echo $rows_limit; ?>')" >End</a></li>

</ul>
</div>



<div class="span3">
    <ul class='nav nav-tabs' style="margin-top: 25px;">
        <li class="span2" >View Rows
            <select onchange="goto_page('<?php echo $func_name; ?>','1',this.value)"  id="rows_limit" >
<option <?php if ( $rows_limit == $default_view_3_rows ) echo "SELECTED"; ?> value=<?php echo $default_view_3_rows; ?> ><?php echo $default_view_3_rows; ?></option>
<option <?php if ( $rows_limit == $default_view_10_rows ) echo "SELECTED"; ?> value=<?php echo $default_view_10_rows; ?> ><?php echo $default_view_10_rows; ?></option>
<option <?php if ( $rows_limit == $default_view_15_rows ) echo "SELECTED"; ?> value=<?php echo $default_view_15_rows; ?> ><?php echo $default_view_15_rows; ?></option>
<option <?php if ( $rows_limit == $default_view_20_rows ) echo "SELECTED"; ?> value=<?php echo $default_view_20_rows; ?> ><?php echo $default_view_20_rows; ?></option>
<option <?php if ( $rows_limit == $default_view_50_rows ) echo "SELECTED"; ?> value=<?php echo $default_view_50_rows; ?> ><?php echo $default_view_50_rows; ?></option>
<option <?php if ( $rows_limit == $default_view_100_rows ) echo "SELECTED"; ?> value=<?php echo $default_view_100_rows; ?> ><?php echo $default_view_100_rows; ?></option>
</select></li>
    </ul>
</div>

</div>
<BR/>
<?php
   } //show pageno
?>
<script type="text/javascript" src="assets/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript"> 
$(document).ready(function() { 
    $("table").tablesorter({ 
        sortList: [[0,0],[2,0]] 
    }); 
}); 
</script>
