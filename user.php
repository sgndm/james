<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get();
      $ux = UserModel::get();
      $p_record_id = ''; //$p_record_id = $_POST["record_id"];
      if  ( $p_record_id == null )
         $p_record_id= $ctrl->getUserID();
	  $p_record_type= $ctrl->getUserType();
      $p_rec = $ux->getUserRecord($p_record_id);
      $l_arr =array();
      $l_arr_ctr =0;
	  $dlim=";_;";
      	$l_arr[$l_arr_ctr++]="Name" . $dlim ."first_name". $dlim ." text". $dlim ."1" . $dlim .  $p_rec["first_name"];
      	$l_arr[$l_arr_ctr++]="Email" . $dlim ."email". $dlim ."email". $dlim ."1" . $dlim . $p_rec["email"];
      	//$l_arr[$l_arr_ctr++]="Phone" . $dlim ."phone". $dlim ."text". $dlim ."1" . $dlim . $p_rec["phone"];
      	$l_arr[$l_arr_ctr++]="UserName" . $dlim ."username". $dlim ."text". $dlim ."1" . $dlim . $p_rec["username"];
      	$l_arr[$l_arr_ctr++]="Password" . $dlim ."password". $dlim ."password". $dlim ."0" . $dlim . "";
	  if($p_record_type == 1)//Admin
	  {

      }
	  else if($p_record_type == 2)//Clinic
	  {
      	$l_arr[$l_arr_ctr++]="Address1" . $dlim ."address1". $dlim ."text". $dlim ."1" . $dlim . $p_rec["address1"];
      	$l_arr[$l_arr_ctr++]="Address2" . $dlim ."address2". $dlim ."text". $dlim ."0" . $dlim . $p_rec["address2"];
      	$l_arr[$l_arr_ctr++]="City" . $dlim ."city". $dlim ."text". $dlim ."1" . $dlim . $p_rec["city"];
      	$l_arr[$l_arr_ctr++]="State" . $dlim ."state". $dlim ."text". $dlim ."1" . $dlim . $p_rec["state"];
      	$l_arr[$l_arr_ctr++]="Zipcode" . $dlim ."zipcode". $dlim ."text". $dlim ."1" . $dlim . $p_rec["zipcode"];
        $l_arr[$l_arr_ctr++]="Operation Hours" . $dlim ."operation_hours". $dlim ."text". $dlim ."1" . $dlim . $p_rec["operation_hours"];

      }
	  else if($p_record_type == 3)//Doctor
	  {
	  	$l_arr[$l_arr_ctr++]="Designation" . $dlim ."designation". $dlim ." text". $dlim ."1" . $dlim . $p_rec["designation"];
      	$l_arr[$l_arr_ctr++]="Hours" . $dlim ."operation_hours". $dlim ." text". $dlim ."1" . $dlim . $p_rec["operation_hours"];
	  }
	  else if($p_record_type == 4)//CC
	  {

	  }
      $fieldtotal = $l_arr_ctr;

?>
<script type="text/javascript" src="assets/js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="js/telinput/css/intlTelInput.css">
	<script src="js/telinput/js/intlTelInput.js"></script>
<script type="text/javascript">
function usersave()
{
	var cleanNumber = $('#mobile-number').intlTelInput("getCleanNumber");
	var countryData = $("#mobile-number").intlTelInput("getSelectedCountryData");
	var dialCode = countryData["dialCode"]; //does not include +
	var withoutDialCode = cleanNumber.replace("+" + dialCode, "");
	$("#record_phone").val(withoutDialCode);
	$("#record_dial_code").val(dialCode);

   callme('record,saveuser,null');
}
</script>
<div class="row span12 offset1">
<h4 class='login-box-head'>Account</h4>
<input type="hidden" id="record_id" value="<?php echo $p_record_id; ?>" />
<input type="hidden" id="record_type" value="<?php echo $p_record_type; ?>" />
<?php
      for ( $idx =0; $idx <$fieldtotal; $idx++)
{
      $rec=$l_arr[$idx];

      $lfld = explode($dlim,$rec);
      $l_fld_title=  $ctrl->mytrim($lfld[0]);
      $l_fld_id=  $ctrl->mytrim($lfld[1]);
      $l_fld_type=  $ctrl->mytrim($lfld[2]);
      $l_fld_minlength=  $ctrl->mytrim($lfld[3]);
      $l_fld_value=  $ctrl->mytrim($lfld[4]);
?>
<div class="row">
<div class='span6'>
<label><?php echo $l_fld_title; ?></label>
<input id="record_<?php echo $l_fld_id; ?>" value="<?php echo $l_fld_value; ?>"
       minlength="<?php echo $l_fld_minlength; ?>"
  title="<?php echo $l_fld_title; ?>" class='span6' placeholder='<?php $l_fld_title; ?>...' type='<?php echo $l_fld_type; ?>'>
</div>
</div>


<?php
   }
?>

<input type="hidden" id="record_phone" value="<?php echo $p_rec["phone"]; ?>" />
<input type="hidden" id="record_dial_code" value="<?php echo $p_rec["dial_code"]; ?>" />
  <div class="row">
<div class='span6'>
	<label>Phone Number</label>
	<input type="text" id="mobile-number" class="form-control span6" />
	<script type="text/javascript">
  		$(document).ready(function()
    	{
    		callTel();
    		var phone = $("#record_phone").val();
    		var dial = $("#record_dial_code").val();
    		var stringToAddToMobileNumber = "+" + dial + phone;
    		$("#mobile-number").intlTelInput("setNumber", stringToAddToMobileNumber);

    	});
	</script>
</div>
</div>

<div class="row">
<div class='span6'>
<label>Image</label>
<input   onChange="call_image()" id="profileimage" class='span4' placeholder='Select image ...' type='file'/>

<p>
<?php
  $l_isactive = $p_rec["isactive"];
  $l_checked="";
  if ( $l_isactive == "Y" )
      $l_checked="CHECKED=1";
?>
  <input <?php echo $l_checked; ?> type="checkbox"  id="record_isactive" />
  Is Active
  </p>
  <BR/>

</div>
</div>
  <BR/>

			<div class='login-actions'>

<input type="button" onClick="usersave()" value="Save" />
<input type="button"  onClick="callform('userlist.php')" value="Go Back" />
			</div>
</div>
</div>
<?php include("foot.php") ?>
