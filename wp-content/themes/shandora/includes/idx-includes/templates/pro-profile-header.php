 <?php
 
 # Only useful if using dsIDXpress Pro
 if(!self::is_pro()) { return; }
 
  echo self::get_global('pro_top_search'); 
 
  echo self::get_global('pro_midx_javascript');
 
  echo self::get_global('pro_visitor_javascript');
 
  do_action('bon_idx_pro_login_registration_form');
 

  echo '<div class="row"><div class="bon-idx-profile-header column large-12">';
  # The Profile Header is the Profile / Searches / Listings / Logout section
  echo self::get_global('pro_profile_header');
 
  # Top actions is Save Search / Favorite listing
  echo self::get_global('pro_top_actions');

  echo '</div></div>';