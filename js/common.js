function kalert( msg)
{
   // none .
   // alert(msg);
}
function callchange(title, change)
{
	var url = "";
	if(title == "Patients")
		url = "Home";
	else if(title == "Clinics")
		url = "Clinic";
	else if(title == "Doctors")
		url = "Doctor";
	else if(title == "Care Coordinators")
		url = "CC";
	else if(title == "Admin User")
		url = "Admin";
	else if(title == "Diagnosis")
		url = "Diagnosis";
	else if(title == "Videos")
		url = "Video";
	else if(title == "Vitals")
		url = "Vital";
	else if(title == "Symptoms")
		url = "Symptom";
	else if(title == "Diet")
		url = "Diet";
	else if(title == "Wound Care")
		url = "Wound";
	else if(title == "Physical Activity")
		url = "PA";
	else if(title == "Medication")
		url = "Medication";
	else if(title == "Medication Classes")
		url = "MedicationClass";
	else if(title == "My Medication")
		url = "MyMedication";
	else if(title == "My Wound")
		url = "MyWound";
	else if(title == "My Diet")
		url = "MyDiet";
	else if(title == "My PA")
		url = "MyPA";
	else if(title == "My Symptom")
		url = "MySymptom";
	else if(title == "My Video")
		url = "MyVideo";
	else if(title == "My Vitals")
		url = "MyVitals";
	else if(title == "My Appointment")
		url = "MyAppointment";

	$.post(
    	'update' + url + 'Table.php',
    	{ show: change },
    	function( data )
    	{
        	$('#tableHolder').html(data);
    	});
}
function callTel()
{
	$("#mobile-number").intlTelInput({
		utilsScript: "js/telinput/lib/libphonenumber/build/utils.js"
	});
}
function call_home()
{
   window.location = "myhome.php";
}
function call_signout()
{
   window.location = "index.php?action=signout";
}

function message_clear()
{
       $("#alert_info_div").attr("style", "display: none");
       $("#alert_error_div").attr("style", "display: none");
}

function div_visible_show(id)
{
         $(id).attr("style", "visibility:none");
}
function div_visible_chk(id)
{
         if ( $(id).css("visibility") == "hidden"  )
         {
             $(id).attr("style", "visibility:none");
         }
         else
         {
             $(id).attr("style", "visibility:hidden");
         }
}
function div_visible_hide(id)
{
         //$(id).fadeOut('fast'); }, 4000);
         $(id).attr("style", "visibility: hidden");
}

function hide_div(id)
{
         $(id).attr("style", "display: none");
}

function show_div(id)
{
         $(id).attr("style", "display: block");
}

function show_error(mess)
{

 kalert( mess);
     //message_clear();
     $("#alert_error_message").text(mess);
     $("#alert_error_div").attr("style", "display: block");
}
function mysleep(milliseconds)
{
  a=1;
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
  message_clear();
  a=2;
}

function show_info(mess)
{
     message_clear();
     $("#alert_info_message").text(mess);
     $("#alert_info_div").attr("class", "alert alert-success");
     $("#alert_info_div").attr("style", "display: block");
     $("#alert_info_div").focus();
     setTimeout(function() {
     $('#alert_info_div').fadeOut('fast'); }, 4000);
     $('html,body').animate({ scrollTop: 0  }, 'slow'); // kumar
}

function flash_error(mess)
{
     show_error(mess);
     setTimeout(function() {
        $('#alert_error_div').fadeOut('fast'); }, 4000);
     $('html,body').animate({ scrollTop: 0  }, 'slow'); // kumar
}

function callme(param)
{
  var n=param.split(",");
  l_prefix=n[0];
  cmd=n[1];

  fldnm="#" + n[2];

  l_res =validatefields(l_prefix);
  // alert( "1 validation res="  + l_res + " prefix = " + l_prefix);
  if ( l_res  == false )
      return;
  if ( cmd == "contact" )
  {
     l_res =validatefields(l_prefix);
     if ( l_res )
     {
         return call_contact(cmd,fldnm,l_prefix,true,null);
     }
  }
  else if  ( cmd == "symptomsymptom" )
  {
     return call_symptomsymptom(cmd,fldnm,l_prefix,true,null);
  }
  else if  ( cmd == "pushmedication" )
  {
     return call_pushmedication(cmd,fldnm,l_prefix,true,null);
  }
  else if  ( cmd == "pushsymptom" )
  {
     return call_pushsymptom(cmd,fldnm,l_prefix,true,null);
  }
  else if ( cmd == "reviewsymptom" || cmd == "reviewpainmeds"
  			|| cmd == "reviewvitals")
  {
  	return call_reviewsomething(cmd,fldnm,l_prefix,true,null);
  }
  else if  ( cmd == "chkduplicateemail" )
  {
     return call_chkduplicateemail(cmd,fldnm,l_prefix,true,null);
  }
  else if ( cmd == "forgotpassword" )
  {
     p1 = $("#login_email").val();
     if ( p1 == "" || p1 == null || p1.length == 0 )
     {
        show_error( "Please enter Email Address");
        $("#login_email").focus();
        return;
     }

     return call_forgotpassword(cmd,fldnm,l_prefix,true,null);
  }
  else if ( cmd == "authenticate" )
  {
     l_res =validatefields(l_prefix);
     if ( l_res )
     {
         return call_authenticate(cmd,fldnm,l_prefix,true,null);
     }
  }
  else {
     if ( l_res )
     {
			 	 // alert('callme else');
         return call_common(cmd,fldnm,l_prefix,true,null);
     }
  }
}

function call_authenticate(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error ( obj.message);
                  $(fldname).focus();
            }
            else if ( obj.success == "true" )
            {
                  document.postmeform.action="myhome.php";
                  document.postmeform.sid.value= obj.sid;
                  document.postmeform.submit();
            }
        }
}

function call_chkduplicateemail(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
            param="type=" + cmd  + "&email=" + $(fldname).val();
            callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error ("Email already registered ...");
                  $(fldname).focus();
            }
        }
}
function validatefields(prefix)
{
  c_flag=true;
  c_field="";

  $.each($("input,textarea"),
      function (index, value) {
        id = $(value).attr('id');
        if ( id == null || id.length == 0 )
           return;
        idx = prefix + "_";
        fnd = id.indexOf(idx);
        if ( fnd == -1 )
           return;
        ty = $(value).attr('type');
        if ( ty == "hidden" )
              return;
        minlength = $(value).attr('minlength');
        if ( minlength == null || minlength.trim().length==0 )
           minlength=0;
        if ( minlength == 0 )
           return;
        datatype = $(value).attr('datatype');

        if ( ( fnd != -1 )   && ( c_flag == true ) )
        {
           val = $(value).val();
           if ( ty == "textarea" )
              val = $(value).text();
           if ( val == null )
               val="";
           else
                val=val.trim();

           if ( minlength >  0 && val.trim().length < minlength )
           {
                 tit = $(value).attr('title');
                 flash_error( "please enter valid " + tit );
                 c_field=value;
                 c_flag=false;
           } /// if val null


           if ( ( datatype == "percent"  ) && ( val > 100 ) )
           {
                 tit = $(value).attr('title');
                 show_error( "please enter valid (percentage) " + tit );
                 c_field=value;
                 c_flag=false;
           } /// if val null
           if ( ( datatype == "amount"  ) && ( val <= 0 ) )
           {
                 tit = $(value).attr('title');
                 show_error( "please enter valid (amount) " + tit );
                 c_field=value;
                 c_flag=false;
           } /// if val null
           if ( ( datatype == "int"  ) && ( val <= 0 ) )
           {
                 tit = $(value).attr('title');
                 show_error( "please enter valid (number) " + tit );
                 c_field=value;
                 c_flag=false;
           } /// if val null
           if ( ( datatype == "email"  ) && ( ! validate_email(val) ) )
           {
                 tit = $(value).attr('title');
                 show_error( "please enter valid " + tit );
                 c_field=value;
                 c_flag=false;
           } /// if val null
           if ( ( datatype == "phone" )  && ( ! validate_phone(val) ) )
           {
                 tit = $(value).attr('title');
                 show_error( "please enter valid " + tit );
                 c_field=value;
                 c_flag=false;
           } /// if val null
      } // if true and need check
   } // func
   );

   if ( c_flag == false )
   {
      $(c_field).focus();
   }
   return c_flag;
}
function getfields(p_prefix)
{
             str="";
             $.each($("select,input,textarea"),
                 function (index, value) {
                   id = $(value).attr('id');
                   if ( id == null || id.length == 0 )
                      return;
                   idx = p_prefix + "_";
                   fnd = id.indexOf(idx);
                   if ( fnd != -1 )
                   {
                   ty = $(value).attr('type');
                   val = "" ;
                   skip= 0;
                   if ( ty == "select" )
                   {
                      if ( $(value).is(':selected')  == true )
                          val = $(value).val();
                      else
                           skip= 1;
                   }
                   if ( ty == "radio" )
                   {
                      if ( $(value).is(':checked')  == true )
                          val = $(value).val();
                      else
                           skip= 1;
                   }
                   else if ( ty == "checkbox" )
                      val = $(value).is(':checked') ;
                   else if ( ty == "textarea" )
                        val = $(value).text();
                      else
                      {  val = $(value).val();
                         val = $.trim(val);
                      }
                      if ( skip == 0 )
                          str +=  "&" + id + "=" + val;
                   }
   } // func
   );
  return str;
}

function callserver(cmd,fldname,p_prefix,serverparam)
{
        message_clear();
        kalert( cmd + " " + serverparam);
        var xmlhttp;
        if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
          }
        else
          {// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          }
        xmlhttp.onreadystatechange=function()
        {
         if (xmlhttp.readyState==4 && xmlhttp.status==200)
         {
               data= xmlhttp.responseText;
               kalert( data);

        var fname="call_" + cmd;
        // alert( " got reply " + fname);
        myfunction = eval(fname);
				// alert(myfunction);
        if ( myfunction != null )
        {
					// console.log(data);
           //alert( " calling myfunction");
					 // alert( " got call common");
					 console.log('cmd' + cmd);
					 console.log('fldname' + fldname);
					 console.log('p_prefix' + p_prefix);
					 console.log('data' + data);
           myfunction(cmd,fldname,p_prefix,false,data);
        }
        else
          {

           call_common(cmd,fldname,p_prefix,false,data);
          }
          }
        };
        url="callserver.php";
        xmlhttp.open("POST",url);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(serverparam);
}

function show_fields(fldname)
{
        disp_id = fldname + "_disp";
        edit_id = fldname + "_edit";
        val   = $(edit_id).attr("style");
        fnd = val.indexOf("block");
        if ( fnd > 0  )
        {
            $(edit_id).attr("style", "display: none");
            $(disp_id).attr("style", "display: block");
        }
        else
        {
            $(edit_id).attr("style", "display: block");
            $(disp_id).attr("style", "display: none");
        }
}

function call_contact(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error  ("Server Error ");
                  $(fldname).focus();
            }
            else if ( obj.success == "true" )
            {
                   mess = "Thank you for your online submission, we will be in touch within 1 business day";
                   show_info(mess);
                   div_visible_hide('#support_div');
            }
        }
}


function validate_email(p_email)
{
var x=p_email;
var atpos=x.indexOf("@");
var dotpos=x.lastIndexOf(".");
if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
{
  return false;
}
  return true;
}

function validate_phone(x)
{
  var src = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
  if ( x.value.match(src)    )
  {
      return true;
  }
  else
  {
        return false;
  }
  return true;
}

function format_phone(fld)
{
   var str = fld.value;
   str = str.replace("-","");
   str = str.replace("_","");
   str = str.replace(".","");
   str = str.replace("(","");
   str = str.replace(")","");

   if ( str.trim().length >= 10 )
   {
      var res = str.substring(0,3) + '-' + str.substring(3,6) + '-' + str.substring(6);
      res=res.trim();
      fld.value=res;
   }
}
function uploadfilechk()
{
   product_id = $("#product_id").val();
   return true;
}

function uploaddocument_chkname()
{
   nm = $("#ovr_document_name").val();
   if ( nm.length == 0 )
   {
      mess =   "Please enter document name";
      //show_error  ( mess);
      $("#upload_message").text(mess);
      $("#ovr_document_name").focus();
   }
}
function call_image()
{

   l_record_id = $("#record_id").val();
   l_unique_id = $("#record_uniqueid").val();
   l_formname = $("#record_formname").val();
   file_type = $("#record_file_type").val();
   l_fl  = $("#profileimage").val();
   //alert(l_record_id + " " + l_unique_id + " " + l_formname + " " + file_type + " " + l_fl);
   if ( l_fl )
   {
	   var fileExt = l_fl.replace(/^.*\./,'');
       $("#record_copied_file").val("photo." + fileExt);
   }
   var xmlhttp;
   if (window.XMLHttpRequest)
   {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
   }
   else
   {// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
   var xmlhttp;
   xmlhttp.onreadystatechange=function()
   {
         if (xmlhttp.readyState==4 && xmlhttp.status==200)
         {
               data= xmlhttp.responseText;
							 // console.log(data);
               obj = JSON.parse(data);
               if ( obj.success == "false" )
               {
                     show_error  ( obj.message);
               }
               if ( obj.success == "true" )
               {
               } // true
          }
   };
   url="uploadfile.php";
   xmlhttp.open("POST",url);
   id = document.getElementById("profileimage");
   var file = id.files[0];
   var formData = new FormData();
   formData.append("file", file);
   formData.append("record_id", l_record_id);
   formData.append("formname", l_formname);
   formData.append("unique_id", l_unique_id);
   formData.append("file_type", "profile");
   //alert("file: " + file + " record_id: " + l_record_id + " unique_id: " + l_unique_id + " formname: " + l_formname);
   xmlhttp.send(formData);
}


function roundit(val)
{
    return parseFloat(val).toFixed(2);
}



function call_setsession(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list ="invest_no_of_shares=" + $("#invest_no_of_shares").val();
             param="type=" + cmd  + "&" + field_list;
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
        }
}




function call_profile(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
   kalert( " inside call_profile");

        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error  ( obj.message);
                  $(fldname).focus();
            }
            else if ( obj.success == "true" )
            {
                   show_info("Profile successfully updated");
            }
        }
}
function callshowdocument(id)
{
        document.postmeform.action="showdocument.php";
        document.postmeform.param1.value= id;
        document.postmeform.submit();
}

function callshowpdf(nm,id)
{
        document.postmeform.action="showpdf.php";
        document.postmeform.param1.value= nm;
        document.postmeform.param2.value= id;
        document.postmeform.submit();
}

function call_chkuserlogged(p_url)
{
   uid = $("#user_id").val();
   if ( uid > 0 )
   {
       window.location = p_url;
   }
   else
   {
     $("#alert_info_contact_url").attr("style", "display: block");
   }
}


function chkpassword(inputtxt)
{
    var passw=  /^[A-Za-z]\w{7,14}$/;
    if(inputtxt.match(passw))
    {
        return true;
    }
    else
    {
    flash_error( "Input Password and Submit [7 to 15 characters which contain only characters, numeric digits, underscore and first character must be a letter]!");
    return false;
    }
}

function callmenuitem(p_menuname,p_menuparam)
{
   if ( p_menuname == "callmenu" )
       window.location = "mymenu.php?menuid=" + p_menuparam;
   else if ( p_menuname == "showdata"  && p_menuparam == "pid" )
       window.location = "profile.php";
   else if ( p_menuname == "showdata" )
       window.location = "showdata.php?segment=" + p_menuparam;
   else if ( p_menuname == "showurl" )
       window.location = "showurl.php?param=" + p_menuparam;
   else
       window.location = p_menuname;
}
function call_common(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
       //alert( " inside call_common pre=  " + p_prefix + " cmd=" + cmd);

        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
					// console.log(data);
            obj = JSON.parse(data);
     if  ( cmd == "allmedication" )
     {
        localallmedication(data);
     }
     else if  ( cmd == "allwoundcare" )
     {
        localallwoundcare(data);
     }
     else if  ( cmd == "allphyact" )
     {
        localallphyact(data);
     }
     else if  ( cmd == "allsymptom" )
     {
        localallsymptom(data);
     }
     else if  ( cmd == "allvideo" )
     {
        localallvideo(data);
     }
     else if  ( cmd == "alldiet" )
     {
        localalldiet(data);
     }
     else if  ( cmd == "savepatient" )
     {
			  // alert('call_cmd savepatient');
				// console.log(data);
        localsavepatient(data);
     }
     else
		 // obj = JSON.parse(data); // added by dinesh
            if ( obj.success == "false" )
            {
                  show_error  ( obj.message);
                  //$(fldname).focus();
            }
            else if ( obj.success == "true" && cmd == "resetpassword")
            {
                  show_info  ( obj.message);
                  //show_info("Successfully updated");
            }
            else if ( obj.success == "true")
            {
                  //show_Info  ( obj.message);
                  show_info("Successfully updated");
            }
        }
}
function callform(p_frmname,p_id,p_tabname,p_calledby)
{

        document.postmeform.action=p_frmname,
        document.postmeform.record_id.value= p_id;
        document.postmeform.current_tab.value= p_tabname;
        document.postmeform.called_by.value= p_calledby;
        document.postmeform.submit();

        //var url = p_frmname + "?patient=" + p_id + "&tab=" + p_tabname + "&called=" + p_calledby;
        //window.location.href= url;
}
function callpatientform(p_frmname,p_id,p_tabname,p_calledby)
{
		document.postmeform.action=p_frmname,
        document.postmeform.record_id.value= p_id;
        document.postmeform.current_tab.value= p_tabname;
        document.postmeform.called_by.value= p_calledby;
        document.postmeform.submit();

        //var url = p_frmname + "?patient=" + p_id + "&tab=" + p_tabname + "&called=" + p_calledby;
        //window.location.href= url;
}
function goto_patient(p_tabname)
{
        document.postmeform.action="patient.php";
        document.postmeform.current_tab.value= p_tabname;
        document.postmeform.submit();
}

function call_savepatient(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_allwoundcare(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_allphyact(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_allsymptom(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_allvideo(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_alldiet(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_alltask(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_allvitals(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_saveuserdiet(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_savemedicationlist(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_savemedicationclasslist(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_savediagnosislist(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_savedefault(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_savedietlist(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_savephyactlist(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_savewoundlist(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}

function call_allmedication(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_medication(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}

function call_woundcare(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}

function call_clinic(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_resetpassword(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_diet(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_dietlist(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}

function call_symptom(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_video(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_vital(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_doctor(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_cc(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_uservideo(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_usersymptom(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_physicalactivity(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_organization(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_userappt(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}

function call_appointments(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}
function call_saveuser(cmd,fldname,p_prefix,p_flg,p_data)
{
   return call_common(cmd,fldname,p_prefix,p_flg,p_data);
}

function callformwithparentkey(p_frmname,p_record_id,p_patient_id)
{
        //alert( "callformwithparentkey" + "frmname=" + p_frmname + " record id=" + p_record_id + " patient id=" + p_patient_id );

        document.postmeform.action=p_frmname,
        document.postmeform.record_id.value= p_record_id;
        document.postmeform.patient_id.value= p_patient_id;
        document.postmeform.submit();
}
function mypopup(p_url)
{
	newwindow=window.open(p_url,'name','top=200px,left=200px,height=600px,width=600px');
	if (window.focus) {newwindow.focus();}
	return false;
}
function goto_page(p_func_name,p_cur_page,p_row_limit)
{
        document.postmeform.action=p_func_name;
        document.postmeform.param1.value= p_cur_page;
        document.postmeform.param2.value= p_row_limit;
        document.postmeform.submit();
}
function playvideo()
{
       l_val=$("#record_url").val();
       $("#play_video_url").attr("src", l_val);
}
function calldashboard_date(p_val)
{
    $("#record_calledby").val("date");
    calldashboard();
}
function calldashboardvital_date(p_val)
{
    $("#recordvital_calledby").val("date");
    calldashboardvital();
}

function callmywoundimage(p_param)
{
        document.postmeform.action="mywoundimage.php";
        document.postmeform.param1.value= $("#record_from_date").val();
        document.postmeform.param2.value= $("#record_to_date").val();
        document.postmeform.param3.value= $("#record_rows").val();
        document.postmeform.param4.value= $("#record_cur_page").val();
        document.postmeform.param5.value= p_param;
        document.postmeform.submit();



}

function calldashboard()
{

        document.postmeform.action=$("#record_dashboard_name").val();
        document.postmeform.param1.value= $("#record_from_date").val();
        document.postmeform.param2.value= $("#record_to_date").val();
        document.postmeform.param3.value= $("#record_charttype").val();
        document.postmeform.param4.value= $("#record_datatype").val();
        document.postmeform.param5.value= $("#record_information").val();
        document.postmeform.param6.value= $("#record_tabname").val();
        document.postmeform.param7.value= $("#record_calledby").val();

        document.postmeform.submit();
}
function calldashboardvital()
{

        document.postmeform.action=$("#recordvital_dashboard_name").val();
        document.postmeform.param1.value= $("#recordvital_from_date").val();
        document.postmeform.param2.value= $("#recordvital_to_date").val();
        document.postmeform.param3.value= $("#recordvital_charttype").val();
        document.postmeform.param4.value= $("#recordvital_datatype").val();
        document.postmeform.param5.value= $("#recordvital_information").val();
        document.postmeform.param6.value= $("#recordvital_tabname").val();
        document.postmeform.param7.value= $("#recordvital_calledby").val();

        document.postmeform.submit();
}

function call_pushmedication(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  show_info (obj.message);
            }
        }
}
function call_pushimage(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  show_info (obj.message);
            }
        }
}
function call_pushsymptom(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  show_info (obj.message);
            }
        }
}
function call_reviewsomething(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewvitals(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewvitalsnotify(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewpainmeds(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewpainmedsnotify(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewsymptoms(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{

        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewsymptomsnotify(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewwounds(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{

        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function call_reviewwoundsnotify(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  //show_info (obj.message);
                  var idToRemove = obj.key;
                  $( "#"+idToRemove ).remove();
            }
        }
}
function symptomHover(symptomid)
{
	$("#"+symptomid).tooltip({
      show: {
        effect: "slideDown",
        delay: 0
      }
    });
}


function call_clearsymptom(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  show_info (obj.message);
                  window.location = "patient.php?tab=symptom";
            }
        }
}
function callsetval(pid,pval)
{
   $(pid).val(pval);
}
function show_dashboard(ptabname)
{
  callsetval('#record_tabname',ptabname);
  if ( ptabname == "interact" )
  {
         $("#dashboard_input").attr("style", "display: blocked");
         $("#interact").attr("style", "display: blocked");
         $("#attract").attr("style", "display: none");
         $("#interact2").attr("style", "display: none");
  }
  else if ( ptabname == "attract" )
  {
         $("#dashboard_input").attr("style", "display: blocked");
         $("#attract").attr("style", "display: blocked");
         $("#interact").attr("style", "display: none");
         $("#interact2").attr("style", "display: none");
  }
  else if ( ptabname == "interact2" )
  {
         $("#dashboard_input").attr("style", "display: none");
         $("#interact2").attr("style", "display: blocked");
         $("#attract").attr("style", "display: none");
         $("#interact").attr("style", "display: none");
  }
}

function showwoundimageimage(p_id,p_lbl,p_url)
{
      $("#image_div").attr("style", "visibility:none");
      $("#image_url").attr("src",p_url);
      $("#image_patient_id").val(p_id);
}
function callpatientimage(p_id)
{
   //callform("woundimage.php",p_id);
   callform("patient.php",p_id,"woundimage","myhome");
}

function call_compliance(cmd,fldname,p_prefix,p_invokeservercall,reply_param)
{
        if ( p_invokeservercall == true )
        {
             field_list = getfields(p_prefix);
             param="type=" + cmd  + "&fieldlist=" +  encodeURI(field_list);
             callserver(cmd,fldname,p_prefix,param);
        }
        else
        {
            obj = JSON.parse(data);
            $("#mydashboard").attr("src","mytest.php");
            if ( obj.success == "false" )
            {
                  show_error (obj.message);
            }
            else if ( obj.success == "true" )
            {
                  show_info (obj.message);
            }
        }
}
function gobackdashboard()
{
        document.postmeform.action="dashboard.php";
        document.postmeform.param6.value= "interact2";
        document.postmeform.submit();
}
function callwoundimage(p_param)
{
        document.postmeform.action="woundimage.php";
        document.postmeform.param1.value= $("#record_from_date").val();
        document.postmeform.param2.value= $("#record_to_date").val();
        document.postmeform.param3.value= $("#record_rows").val();
        document.postmeform.param4.value= $("#record_cur_page").val();
        document.postmeform.param5.value= p_param;
        document.postmeform.submit();



}
function callrefresh(p_name)
{
   window.location = p_name;
}
function resettab(p_tabname)
{
    $("#tab-profile").attr('class',"tab-pane");
    $("#tab-medication").attr('class',"tab-pane");
    $("#tab-woundcare").attr('class',"tab-pane");
    $("#tab-diet").attr('class',"tab-pane");
    $("#tab-phyact").attr('class',"tab-pane");
    $("#tab-symptom").attr('class',"tab-pane");
    $("#tab-appt").attr('class',"tab-pane");
    $("#tab-videos").attr('class',"tab-pane");

    $("#li-tab-profile").attr('class',"");
    $("#li-tab-medication").attr('class',"");
    $("#li-tab-woundcare").attr('class',"");
    $("#li-tab-diet").attr('class',"");
    $("#li-tab-phyact").attr('class',"");
    $("#li-tab-symptom").attr('class',"");
    $("#li-tab-appt").attr('class',"");
    $("#li-tab-videos").attr('class',"");

    p_on_tab ="tab-profile";
    if ( p_tabname == "pp" )
       p_on_tab ="tab-profile";
    if ( p_tabname == "md" )
       p_on_tab ="tab-medication";
    if ( p_tabname == "wc" )
       p_on_tab ="tab-woundcare";
    if ( p_tabname == "diet" )
       p_on_tab ="tab-diet";
    if ( p_tabname == "pa" )
       p_on_tab ="tab-phyact";
    if ( p_tabname == "sym" )
       p_on_tab ="tab-symptom";
    if ( p_tabname == "appt" )
       p_on_tab ="tab-appt";
    if ( p_tabname == "videos" )
       p_on_tab ="tab-videos";
    tx = "#" + p_on_tab;
    $(tx).attr('class',"tab-pane active");
    tx = "#li-" + p_on_tab;
    $(tx).attr('class',"active");
}
