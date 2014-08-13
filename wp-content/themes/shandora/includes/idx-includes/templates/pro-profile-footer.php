 <?php
 
 # Only useful if using dsIDXpress Pro
 if(!self::is_pro()) { return; }

 	echo self::get_global('login_form'); 
 	echo self::get_global('forgot_form');
 	echo self::get_global('reset_form');
 	echo self::get_global('register_form');

 ?>