<?php 
require_once("init_setup.php") ?>
<?php
      $ctrl = Controller::get(); 
      $dx = DataModel::get(); 
      $ux = UserModel::get();
      $user_id= $ctrl->getUserID();
      $name = $ctrl->getUserName();
      $email = $ctrl->getUserEmail();
      $sql = "SELECT created_ts FROM user WHERE id = $user_id";
      $createdAt = $ctrl->getRecordTEXT($sql);
?>
		</section>
		</div>
	<footer>
			<div class='container'>
				<div class='row'>
					<div class='span10'>
						<div >
						    <div>
						        
						        <script id="IntercomSettingsScriptTag">
  window.intercomSettings = {
    name: "<?php echo $name; ?>",
    email: "<?php echo $email; ?>",
    created_at: <?php echo strtotime($createdAt); ?>,
    app_id: "c90dee5f3983a48e583652e8ae2dad595f5a8bd8"
  };
</script>
<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://static.intercomcdn.com/intercom.v1.js';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>

						    </div>

<BR/>
<BR/>
Terms and Conditions  &copy; 2014 ProMD, LLC. "MobiMD" is a  trademark and property of ProMD LLC.
2013 </div>
					</div>
				</div>
			</div>
	</footer>

</body>
</html>
