<?php 
	global $wp_roles;
		$post_types=get_post_types('','names'); 
		unset($post_types['revision']); unset($post_types['attachment']);unset($post_types['nav_menu_item']);
		$actions = array("", "pending", "draft", "trash", "delete");
	  
		$html = '<div class="add_new_rule">';
		$html .= '<h3>'.__("Add Post Expiration Rule","Apext").'</h3>';
		$html .= '<form id="apext_new_rule">';
		//get all user role
		$html .= '<p><span>'.__("User Role","Apext").'</span>';
		$html .= '<select id="role" name="pre_add_role">';
		$html .= '<option value=""></option>';
           foreach($wp_roles->role_names as $role => $name ) {
		$html .=  '<option value="'.strtolower($name).'">'.$name.'</option>';}
		$html .= '</select>';
		$html .= '<br><label>'.__("Select user role","Apext").'</label>';
		$html .= '</p>';
		//get all post type
		$html .= '<p><span>'.__("Post Type","Apext").'</span>';
		$html .= '<select id="cpt" name="pre_add_type">';
		$html .= '<option value=""></option>';
           foreach($post_types as $post_type ) {
		$html .=  "<option value='$post_type'>$post_type</option>";}
		$html .= '</select>';
		$html .= '<br><label>'.__("Select Post Type","Apext").'</label>';
		$html .= '</p>';
		//action after expired
		$html .= '<p><span>'.__("Action","Apext").'</span>';
		$html .= '<select id="status" name="pre_add_action">';
           foreach($actions as $action) {
		$html .=  "<option value='$action'>$action</option>";}
		$html .= '</select>';
		$html .= '<br><label>'.__("Action taken after post expired","Apext").'</label>';
		$html .= '</p>';
		//post expire input
		$html .= '<p><span>'.__("Life Span","Apext").'</span>';
		$html .= '<input type="text" id="day" name="pre_add_day" size="20" value="">';
		$html .= '<br><label>'.__("Days to expire","Apext").'</label>';
		$html .= '</p>';
		$html .= '<p>';
		$html .= '<input type="submit" value="'.__("Add Rule","Apext").'" class="add-button button-primary" />'; 
		$html .= '</form></div>';
		$html .= '</p>';
		echo $html;
?>
