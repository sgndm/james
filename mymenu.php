<?php require_once("init_setup.php") ?>
<?php include('head.php') ?>
<script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
<style type="text/css">
.investmentstiny {
    width: 30px;
    height: 30px;
}
</style>

<style type="text/css">
table.tablesorter { 
  background-color: BBD6BF;
  font-size: 12px; 
  width: 650px; 
} 

table.tablesorter th { 
  background: BBD6BF url("assets/premium_icons/glyphicons_pro 3/glyphicons/png/glyphicons_212_down_arrow.png") no-repeat 0 -3px; 
  cursor: pointer; 
  height: auto; 
  padding: 5px 0 5px 30px; 
  text-align: left; 
} 
table.tablesorter td { 
  color: black; 
  padding: 5px; 
  text-align: left; 
} 
table.tablesorter .headerSortUp { 
  background-image: url("assets/premium_icons/glyphicons_pro 3/glyphicons/png/glyphicons_213_up_arrow.png");
} 
table.tablesorter .headerSortDown { 
  background-image: url("assets/premium_icons/glyphicons_pro 3/glyphicons/png/glyphicons_212_down_arrow.png");
}
</style>

<style type="text/css">
.summary {
    height: 85px;
}
.investmentstiny {
    width: 30px;
    height: 30px;
}
.investmentsicon {
    width: 100px;
    height: 70px;
}
.investments {
    width: 300px;
    height: 200px;
}
.opportunitytitle {
    width: 300px;
    height: 20px;                 
}
.opportunitytitle {
    width: 300px;
    height: 20px;                 
}
</style>

<div style="height:600px;" >
<?php
    $ctr=0;

    if ( ! isset($l_st_menu) ) 
       $l_st_menu =  $_GET["menuid"];
    $menu_id =  $l_st_menu;
    $handle = fopen("menu.list", "r");
    if ($handle) {
         $i=0;
         while (($line = fgets($handle)) !== false) {

         $line .=",,,,";
         $fld=explode(",",$line);
         $obj = array();
         if ( $fld[0] == $menu_id ) 
         {
            $l_image ="assets/images/". $fld[1];
            $l_name = $fld[2];
            $l_callfunc = $fld[3];
            $l_callparam = $fld[4];
?>
   <div class="row">
   <div class="span2> 
&nbsp;
&nbsp;
   </div>
   <div 
 style="background-color:white;width:100%;height:50px;overflow:hidden;" class="span4 white-card ">
<p 
 onclick="callmenuitem('<?php echo $l_callfunc;?>','<?php echo $l_callparam;?>')" 
style="height:100%;width:"100%;">
<img style="height:20px;width:20px"  src="<?php echo $l_image; ?>" />
&nbsp;
&nbsp;
&nbsp;
<font color="green" size=4> <?php echo $l_name; ?></font>
    </p>
    </div>
			</div>
<?php
echo "<BR/>";
         }
    }
    }
?>
    </div>
<?php include("foot.php") ?>
