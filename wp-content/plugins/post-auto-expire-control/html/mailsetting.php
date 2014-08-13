<?php 
	$options = get_option('apextmail');
	$pre = isset($options['pre']) ? $options['pre'] : '0';
	$preday = isset($options['preday']) ? $options['preday'] : null;
	$presubject = isset($options['presubject']) ? $options['presubject'] : null;
	$precontent = !empty($options['precontent']) ? $options['precontent'] : null;
	$on = isset($options['on']) ? $options['on'] : '0';
	$onsubject = isset($options['onsubject']) ? $options['onsubject'] : null;
	$oncontent = !empty($options['oncontent']) ? $options['oncontent'] : null;
	
	
	
	$html  = '<div class="notiymail"><p><input type="checkbox" id="apext_check" name="apextmail[pre]" value="1" ' . checked(1, $pre, false) . '/>'; 
	$html .= '<label>'.__("Enable notification before post's expiry date?","Apext").'</label></p>'; 
	
	$html .= '<p><label>'.__("How many days to send the notification before expiration? ","Apext").'</label>';
	$html .= '<br><input type="text" id="notify_day" name="apextmail[preday]" maxlength="4" size="2" value="'.$preday.'"/> days (numbers of days.)</p>'; 
	
	$html .= '<p><label>'.__("Email Subject","Apext").'</label><br>';
	$html .= '<input type="text" id="apext_text" name="apextmail[presubject]" size="50" value="'.esc_attr($presubject).'"/></p>';
		
	$html .= '<p><label>'.__("Email Content","Apext").'</label><br>';
	$html .= '<textarea id="apext_texarea" name="apextmail[precontent]" rows="7" cols="50" type="textarea">'.esc_textarea($precontent).'</textarea>';
	$html .= '</p></div>';
	
	$html .= '<div class="onmail"><p><input type="checkbox" id="apext_check" name="apextmail[on]" value="1" ' . checked(1, $on, false) . '/>';
	$html .= '<label>'.__("Enable notification <b>ON</b> the expiry date?","Apext").'</label></p>';  
	
	$html .= '<p><label>'.__("Email Subject","Apext").'</label><br>';
	$html .= '<input type="text" id="apext_text" name="apextmail[onsubject]" size="50" value="'.esc_attr($onsubject).'"/></p>';
	
	$html .= '<p><label>'.__("Email Content","Apext").'</label><br>';
	$html .= '<textarea id="apext_texarea" name="apextmail[oncontent]" rows="7" cols="50" type="textarea">'.esc_textarea($oncontent).'</textarea></p></div>';

    	$html .= '<div class="belowtext"><h3>'.__("Allowed html tags in email content :","Apext").'</h3>';
	$html .= '<br>'.__("&lt;p&gt;, &lt;br&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;i&gt;, &lt;li&gt;, &lt;ul&gt;, &lt;a href&gt;","Apext");
	$html .= '<br><br><h3>'.__("Available variable tags :","Apext").'</h3>';
	$html .= '<br>'.__("<b>[authorname]</b> = post author's username, <b>[post_id]</b> = post id,  <b>[title]</b> = post's title without link, <b>[title_link]</b> = post's title with permalink, <b>[exdate]</b> = post expire date, <b>[admin_name]</b> = admin's username, <b>[sitename]</b> = site's name","Apext").'</div>';
	
  
    echo $html; 


?>
