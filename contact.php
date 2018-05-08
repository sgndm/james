<?php require_once("init_setup.php") ?>
<?php
      $contact_tab_active="active";
      $ctrl = Controller::get();

      $user_id =    $ctrl->getUserID();
      $kx = UserModel::get(); 
      $pdata = $kx->getUser();

      $l_first_name = $pdata["first_name"];
      $l_last_name = $pdata["last_name"];
      $user_name = $l_first_name . " " . $l_last_name;
      $user_email = $pdata["email"];
      $user_phone = $pdata["phone"];
      $user_phone = $ctrl->formatPhone($user_phone);

?>

<?php include("head.php") ?>
<?
      $show_contact = 0;
?>
                <h3 class='section-header'>Contact Page</h3>
                <div class='row'>
                  <div class='span4'>
                    <div class='blog-side-text-block widget-filled widget-yellow'>
                      <h3>Contact Information</h3>
                      <ul class="unstyled big-iconed-tips">
                        <li>
                          <i class='icon-map-marker'></i>
                            9690 South 300 West ste. 313 <BR>
                                Sandy, Utah 84070 
                        </li>
                        <li>
                          <i class='icon-phone-sign'></i>
                          Phone: 801-510-9432 
                        </li>
                        <li>
                          <i class='icon-envelope-alt'></i>
                          Email: support@promd.co
                        </li>
                    </div>
                  </div>
                  <div class='span8'>
                    <div class="white-card extra-padding">
                    <!--form class='form-transparent no-margin-bottom' id='form-add-comment'-->
                      <fieldset>
                        <div class='row-fluid'>
                          <div class='span12'>
                            <legend>Complete the form to contact us</legend>
                            <div class='controls controls-row'>
                              <input id="contact_type" value="contact"  type="hidden"/>
                              <input id="contact_user_id" value="<?php echo $user_id; ?>" class='search-input span4' placeholder='Your name' type='hidden'>
                              <input minlength=1 title="User Name " validate=1 id="contact_name" value="<?php echo $user_name; ?>" class='search-input span4' placeholder='Your name' type='text'>
                              <input datatype="phone" onchange="format_phone(this)" id="contact_phone" value="<?php echo $user_phone; ?>" class='search-input span4' placeholder='Your phone' type='text'>
                              <input datatype="email" minlength=1 title="Email" validate=1 id="contact_email" value="<?php echo $user_email; ?>"  class='search-input span4' placeholder='Your email' type='text'>
                            </div>
                            <div class='controls controls-row'>
                              <textarea style="margin-left:0" minlength=1 title="Message" id="contact_message" class='span12' placeholder='Your message' rows='6'></textarea>
                            </div>
<input type="button"  onClick="callme('contact,contact,contact_name')" value="Submit Message" />
                            <div class='form-actions'>
                              <!--button class='btn btn-small'>Submit Message</button-->

                            </div>
                          </div>
                        </div>
                      </fieldset>
                    <!--/form-->
                    </div>
                  </div>
                </div>
                <div class='row'>
                  <div class='span12'>
                    <iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3030.4624500782816!2d-111.9007901!3d40.575546599999996!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x875287d5979c78b5%3A0x8508150c52f6fb96!2s9690+S+300+W!5e0!3m2!1sen!2sin!4v1404755892761"></iframe>
                  </div>
                </div>
<?php include("foot.php") ?>
