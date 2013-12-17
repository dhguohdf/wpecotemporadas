<?php
/*
Plugin Name: Draft Notification
Plugin URI: http://www.dagondesign.com/articles/draft-notification-plugin-for-wordpress/
Description: Sends an email to the site admin when a draft is saved.
Author: Dagon Design
Version: 1.22
Author URI: http://www.dagondesign.com
*/


function dddn_process($id) {

	// emails anyone on or above this level
	$email_user_level = 10;

	global $wpdb;
	
	$tp = $wpdb->prefix;

	$result = $wpdb->get_row("
		SELECT post_status, post_title, user_login, user_nicename, display_name 
		FROM {$tp}posts, {$tp}users 
		WHERE {$tp}posts.post_author = {$tp}users.ID 
		AND {$tp}posts.ID = '$id'
	");

	if (($result->post_status == "draft") || ($result->post_status == "pending")) {

		$message = "";
		$message .= "Um rascunho foi atualizado em: '" . get_bloginfo('name') . "'\n\n";
		$message .= "Titulo: " . $result->post_title . "\n\n";

			// *** Choose one of the following options to show the author's name
	
		$message .= "Autor: " . $result->display_name . "\n\n";
		// $message .= "Author: " . $result->user_nicename . "\n\n";
		// $message .= "Author: " . $result->user_login . "\n\n";

		$message .= "Link: " . get_permalink($id);

		$subject = "Rascunho atualizado em '" . get_bloginfo('name') . "'";

	
		$editors = $wpdb->get_results("SELECT user_id FROM {$tp}usermeta WHERE {$tp}usermeta.meta_value >= " . $email_user_level);
		
		$recipient = "";	
		
		foreach ($editors as $editor) {			
			$user_info = get_userdata($editor->user_id);
			$recipient .= $user_info->user_email . ','; 
		} 
		
		mail($recipient, $subject, $message);
		

	}

}


add_action('save_post', 'dddn_process');

?>