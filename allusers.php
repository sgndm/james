<script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get();
      $user_id= $ctrl->getUserID();
      $ux = UserModel::get(); 
      $all_data = $ux->getAllUsers();
    $tot_rows=count($all_data);
    $l_user_id= $ctrl->getUserID();
    $ctr=0;

    $title="All Users";
    $func_name="callallusers";
    include("include/include_pageindex.php");
?>

<div class="span12" style="overflow-x:hidden;overflow-y:hidden;height:700px;" >
<?php
    $ctr=0;
?>

<table id="my-table-sorter" class="tablesorter span11 table-striped table-bordered">
<thead>
  <th >Name<?php echo $incl_sort_image; ?></th>
  <th >Email<?php echo $incl_sort_image; ?></th>
  <th >Phone<?php echo $incl_sort_image; ?></th>
  <th >Role<?php echo $incl_sort_image; ?></th>
  <th >City/State<?php echo $incl_sort_image; ?></th>
  <th >Status<?php echo $incl_sort_image; ?></th>
</tr>
</thead>
<tbody>
<?php
    for ($r_idx=0;$r_idx<$tot_rows ;$r_idx++)
    {
       $c_no=$r_idx + 1;
       if ( $c_no  < $start_rowid || $c_no > $end_rowid )
       {
          continue;
        }
       $pdata =  $all_data[$r_idx];
       $l_user_id = $pdata["id"];
       $l_name = $pdata["first_name"] ." ".$pdata["last_name"];
       $l_email = $pdata["email"] ;
       $l_phone = $pdata["phone"] ; 
       $l_phone_fmt = $l_phone;
       if ( strlen($l_phone) >= 10 ) 
          $l_phone_fmt= 
              substr($l_phone,0,3) ."-".
              substr($l_phone,3,3) ."-".
              substr($l_phone,6);

       $l_role = $pdata["role"] ;
       $l_status = $pdata["status"] ;
       $l_city = $pdata["city"] ;
       $l_state = $pdata["state"] ;
       $l_status_fmt = $ux->status_decode($l_status);
       $ctr++;
       $show_profile="calluser('show','".$l_user_id."')";
       $dlim="<BR/>";
       $lspace="&nbsp;&nbsp;";
?>
     <tr>
     <td ><a href="#" onClick="<?php echo $show_profile; ?>" ><? echo $l_name; ?></a></td>
     <td ><?php echo $l_email; ?></td>
     <td ><?php echo $l_phone_fmt; ?></td>
     <td ><?php echo $l_role; ?></td>
     <td ><?php echo $l_city. "&nbsp;&nbsp;".$l_state; ?></td>
     <td ><?php echo $l_status_fmt; ?></td>
     </tr>
<?php
   }
?>
</tbody>
</table>
</div>

<?php include("foot.php") ?>
<script type="text/javascript" src="assets/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript"> 

$(document).ready(function() { 
    $("table").tablesorter({ 
        sortList: [[0,0],[2,0]] 
    }); 
}); 
</script>
