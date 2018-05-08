<?php require_once("init_setup.php") ?>
<?php include("head.php") ?>
<?php
      $ctrl = Controller::get(); 
      $ux = UserModel::get();
      $l_unique_id= date('YmdHis').$ctrl->createUniqueID(8); 
      $p_record_id = $ctrl->getPostParamValue("record_id");
      if ( $p_record_id == null || $p_record_id == "" )
         $p_record_id = "0";
      $user_id= $ctrl->getUserID();
      $user_type = $ctrl->getUserType();
      $data='{"user_id":"' . $user_id . '"}';
      $p_obj=json_decode($data);
      $l_obj = $ux->getTableRecord($p_obj,"user","id",$p_record_id);

      $fieldtotal = $l_obj->{"total_records"};

      $p_rec =array();
      if ( $l_obj->{"total_records"} == 1 )
          $p_rec=$l_obj->{"record"}[0];

      $l_arr =array();
      $l_arr_ctr =0;

      $dlim=";_;";
      $l_arr[$l_arr_ctr++]="Name" . $dlim ."first_name". $dlim ." text". $dlim ."1" . $dlim . $p_rec["first_name"];
      $l_arr[$l_arr_ctr++]="User Name" . $dlim ."username". $dlim ." text". $dlim ."1" . $dlim . $p_rec["username"];
      $l_arr[$l_arr_ctr++]="Password" . $dlim ."password". $dlim ." text". $dlim ."0" . $dlim . "";
      $l_arr[$l_arr_ctr++]="Address1" . $dlim ."address1". $dlim ."text". $dlim ."1" . $dlim . $p_rec["address1"];
      $l_arr[$l_arr_ctr++]="Address2" . $dlim ."address2". $dlim ."text". $dlim ."0" . $dlim . $p_rec["address2"];
      $l_arr[$l_arr_ctr++]="City" . $dlim ."city". $dlim ."text". $dlim ."1" . $dlim . $p_rec["city"];
      $l_arr[$l_arr_ctr++]="State" . $dlim ."state". $dlim ."text". $dlim ."1" . $dlim . $p_rec["state"];
      $l_arr[$l_arr_ctr++]="Zipcode" . $dlim ."zipcode". $dlim ."text". $dlim ."1" . $dlim . $p_rec["zipcode"];
      //$l_arr[$l_arr_ctr++]="Phone" . $dlim ."phone". $dlim ."text". $dlim ."1" . $dlim . $p_rec["phone"];
      $l_arr[$l_arr_ctr++]="Operation Hours" . $dlim ."operation_hours". $dlim ."text". $dlim ."1" . $dlim . $p_rec["operation_hours"];
      $l_arr[$l_arr_ctr++]="Email" . $dlim ."email". $dlim ."text". $dlim ."1" . $dlim . $p_rec["email"];
      $fieldtotal = $l_arr_ctr;
      $l_photourl = $p_rec["photourl"];

?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js">
</script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js">
</script>
<link rel="stylesheet" href="js/telinput/css/intlTelInput.css">
<script src="js/telinput/js/intlTelInput.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    var tz = jstz.determine(); // Determines the time zone of the browser client
    var timezone = tz.name(); //'Asia/Kolhata' for Indian Time.
    var mydate = new Date();
    var offset = (mydate.getTimezoneOffset() / 60);
    callTel();
    var phone = $("#record_phone").val();
    var dial = $("#record_dial_code").val();
    var stringToAddToMobileNumber = "+" + dial + phone;
    $("#mobile-number").intlTelInput("setNumber", stringToAddToMobileNumber);
  });
  function clinicsave()
  {
  	var cleanNumber = $('#mobile-number').intlTelInput("getCleanNumber");
	var countryData = $("#mobile-number").intlTelInput("getSelectedCountryData");
	var dialCode = countryData["dialCode"]; //does not include +
	var withoutDialCode = cleanNumber.replace("+" + dialCode, "");
	$("#record_phone").val(withoutDialCode);
	$("#record_dial_code").val(dialCode);
  	
  	callme('record,clinic,,cliniclist.php');
  }
</script>
<h4 class='login-box-head'>Clinic</h4>
<div class="row">
<div class='span6'>
<input type="hidden" id="record_id" value="<?php echo $p_record_id; ?>" />

<input type="hidden" id="record_formname" value="clinic" />
<input type="hidden" id="record_copied_file" value="" />
<input type="hidden" id="record_uniqueid" value="<?php echo $l_unique_id; ?>" />
<input type="hidden" id="record_filecreated" value="0" />
<input type="hidden" id="record_user_type" value="2" />
<input type="hidden" id="record_file_type" value="profile" />
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
  title="<?php echo $l_fld_title; ?>" class='span6' placeholder='<?php $l_fld_title; ?>...' type='text'>
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
	<input type="text" id="mobile-number" class="form-control span5" />
</div>
</div>

<div class="row">
  <div class='span6'>
  <label>Time Zone</label><select  class="span5" id="record_time_zone">
<?php
     $l_time_id  = $p_rec["time_zone"];
     $ct = ConstantModel::get(); 
     $data='{}';
     $p_obj=json_decode($data);
     $dx = DataModel::get();
     
         $sql = "SELECT * FROM timezones";
         $ss_obj = $dx->getRecords($p_obj, $sql);
         
    $s_fieldtotal = $ss_obj->{"total_records"};
     for ( $s_idx =0; $s_idx <$s_fieldtotal; $s_idx++)
     {
        $rr_rec=$ss_obj->{"record"}[$s_idx];
		$p_nm=$rr_rec["name"];
        $p_id=$rr_rec["GMT"];
        $p_sel="";
        if ( $p_id == $l_time_id ) 
           $p_sel="SELECTED";
        echo "<OPTION $p_sel value='". $p_id . "'>$p_nm</OPTION>";
     }
     
?>
  </select>
  </div>
  </div>
  <BR/>
  <p>
  	<?php
  $l_dst = $p_rec["daylight_savings_time"];
  $l_checked="";
  if ( $l_dst == "Y" )
      $l_checked="CHECKED=1";
?>
  <input <?php echo $l_checked; ?> type="checkbox"  id="record_daylight" />
  &nbsp;&nbsp;Daylight Savings Time
  </p>
  <BR/>
  <p>
  <input CHECKED=1 type="checkbox"  id="record_isactive" />
  &nbsp;&nbsp;Is Active
  </p>
  <BR/>
<div class="row">
<div class='span6'>
<label>Image</label>
<input   onChange="call_image()" id="profileimage" class='span4' placeholder='Select image ...' type='file'/>
</div>
</div>
  <BR/>
</div>
<div class='span6'>
<img style="width:30%; height:30%" src="<? echo $l_photourl ; ?>"  />
</div>
</div>




			<div class='login-actions'>
<?php if($ux->doIHavePermission($user_type, "save_clinic")) { ?>    
<input type="button" onClick="clinicsave()" value='Save' />
<?php } ?>
<input type="button" onClick="callform('cliniclist.php')" value="Go Back " />
<?php
if ( $p_record_id > 0 ) 
{
?>
<input type="button" onclick="callform('clinic.php',<?php echo $p_record_id; ?>,'')" value="Refresh " />
<?php } ?>
			</div>
</div>
<?php include("foot.php") ?>
