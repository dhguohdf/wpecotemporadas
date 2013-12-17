 <?php
 
 # Only useful if using dsIDXpress Pro
 if(!self::is_pro()) { return; }

 ?>

<div id="dsidx-login" style="display:none">
	<div class="dsix-auth-header"><em><?php _e('Save favorite listings, searches, and get the latest alerts','bon'); ?></em></div>
	<div class="dsix-auth-content">
		<form action="" method="post">
			<label for="dsidx-login-email"><?php _e('Email','bon'); ?></label>
			<input type="text" id="dsidx-login-email" class="text" name="email" />

			<label for="dsidx-login-password"><?php _e('Password','bon'); ?></label>
			<input type="password" id="dsidx-login-password" class="text" name="password" />

			<div class="dsidx-auth-checkbox-container">
				<input type="checkbox" id="dsidx-login-rememberme" class="checkbox" name="remember" /><label class="checkbox" for="dsidx-login-rememberme"><?php _e('Keep me signed in on this computer','bon'); ?></label>
			</div>

			<div class="dsidx-auth-buttons">
				<input type="submit" id="dsidx-login-submit" value="Login" class="dsidx-large-button"/>
				<div style="clear: both;"></div>
				<div class="dsidx-dialog-message" style="display:none;"></div>
			</div>

				<div class="dxidx-auth-forgotpassword-container">
					<a href="javascript:void(0)" onclick="dsidx.auth.ForgotPasswordConfirm()"><?php _e('Forgot your password?','bon'); ?></a>
				</div>
		</form>
	</div>
	<div class="dsidx-auth-footer">
		<span class="dsidx-profile-button dsidx-profile-button-light"><?php _e('Not Registered Yet?','bon'); ?><span class="dsidx-profile-button-divet-right"></span></span>
		<a href="javascript:void(0)" onclick="dsidx.auth.Register()" class="dsidx-profile-button"><?php _e('Sign Me Up!','bon'); ?></a>
	</div>
</div><div id="dsidx-forgotpassword-confirm" style="display:none">
	<div class="dsix-auth-content">
		<form action="" method="post">
			<input type="hidden" id="dsidx-forgotpassword-referral" name="forgotPassword.Referral" />
			<input type="hidden" id="dsidx-forgotpassword-domainname" name="forgotPassword.DomainName" />

			<span class="dsix-auth-text"><?php _e('Fill in your email, and we\'ll send you a message with instructions on how to reset your password. Please make sure to use the same email address you provided during registration.','bon'); ?></span>
				
			<label for="dsidx-login-email"><?php _e('Email:','bon'); ?></label>
			<input type="text" id="dsidx-forgotpassword-email" class="text" name="emailAddress" />

			<div class="dsidx-auth-buttons">
				<input type="submit" id="dsidx-forgotpassword-submit" value="Send" class="dsidx-large-button" />
				<div style="clear: both;"></div>
				<div class="dsidx-dialog-message" style="display:none"></div>
			</div>
		</form>
	</div>
	<div class="dsidx-auth-footer">
		<a href="javascript:void(0)" onclick="dsidx.auth.Close()"><?php _e('close','bon'); ?></a>
		<a href="javascript:void(0)" onclick="dsidx.auth.Login()" class="dsidx-profile-button"><?php _e('Back to Login','bon'); ?></a>
	</div>
</div><div id="dsidx-passwordreset" style="display:none">
	<div class="dsix-auth-content">
		<form action="" method="post">
			<input type="hidden" id="dsidx-passwordreset-referral" name="passwordReset.Referral" />
			<input type="hidden" id="dsidx-passwordreset-domainname" name="passwordReset.DomainName" />
			<input type="hidden" id="dsidx-passwordreset-resettoken" name="resetToken" />
                                
			<label for="dsidx-passwordreset-password"><?php _e('New Password:','bon'); ?></label>
			<input type="password" id="dsidx-passwordreset-password" class="text" name="password" />

			<label for="dsidx-passwordreset-confirmpassword"><?php _e('Confirm Password:','bon'); ?></label>
			<input type="password" id="dsidx-passwordreset-confirmpassword" class="text" name="confirmpassword" />
 
			<div class="dsidx-auth-buttons">
				<input type="submit" id="dsidx-passwordreset-submit" value="<?php _e('Change Password','bon'); ?>" class="dsidx-large-button" />
				<div style="clear: both;"></div>
				<div class="dsidx-dialog-message" style="display:none"></div>
			</div>
		</form>
	</div>
	<div class="dsidx-auth-footer">
		<a href="javascript:void(0)" onclick="dsidx.auth.Close()"><?php _e('close','bon'); ?></a>
	</div>
</div>
	   
<div id="dsidx-register" style="display:none">
	<div class="dsix-auth-header"><em><?php _e('Save favorite listings, searches, and get the latest alerts','bon'); ?></em></div>
	<div class="dsix-auth-content">
		<form action="" method="post">
			<input type="hidden" id="dsidx-register-referral" name="newVisitor.Referral" />
			<input type="hidden" id="dsidx-register-listing-url" name="newVisitor.ListingUrl" value="http://craiglambton.com/idx/mls-40639363-1315_a_street_unit_302_hayward_ca_94541" />
			<input type="hidden" id="dsidx-register-mls-number" name="newVisitor.MlsNumber" value="40639363" />

			<label for="dsidx-login-first-name"><?php _e('First Name:','bon'); ?></label>
			<input type="text" id="dsidx-register-first-name" class="text" name="newVisitor.FirstName" />

			<label for="dsidx-login-last-name"><?php _e('Last Name:','bon'); ?></label>
			<input type="text" id="dsidx-register-last-name" class="text" name="newVisitor.LastName" />

			<label for="dsidx-login-email"><?php _e('Email:','bon'); ?></label>
			<input type="text" id="dsidx-register-email" class="text" name="newVisitor.Email" />

			<label for="dsidx-login-phone-number"><?php _e('Phone Number:','bon'); ?></label>
			<input type="text" id="dsidx-login-phone-number" class="text" name="newVisitor.PhoneNumber" />

			<label for="dsidx-login-password"><?php _e('Password:','bon'); ?></label>
			<input type="password" id="dsidx-register-password" class="text" name="newVisitor.Password" />

			<label for="dsidx-register-confirm-password"><?php _e('Confirm Password:','bon'); ?></label>
			<input type="password" id="dsidx-register-confirm-password" class="text" name="newVisitor.Password_Confirm" />

			<div class="dsidx-auth-checkbox-container">
				<input type="checkbox" id="dsidx-register-rememberme" class="checkbox" /><label class="checkbox" for="dsidx-login-rememberme"><?php _e('Keep me signed in on this computer','bon'); ?></label>
			</div>

			<div class="dsidx-auth-buttons">
				<input type="submit" id="dsidx-register-submit" value="Register" class="dsidx-large-button" />

				<div class="dsidx-dialog-message" style="display:none"></div>
			</div>
		</form>
	</div>
	<div class="dsidx-auth-footer">
		<span class="dsidx-profile-button dsidx-profile-button-light"><?php _e('Already have an account','bon'); ?><span class="dsidx-profile-button-divet-right"></span></span>
		<a href="javascript:void(0)" onclick="dsidx.auth.Login()" class="dsidx-profile-button"><?php _e('Login','bon'); ?></a>
	</div>
</div>