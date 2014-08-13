<?php
/*
Plugin Name: Auto Post Expire By User Role 
Plugin URI: http://9-sec.com/2012/10/post-auto-expire-control/
Description: this plugin helps you to set your author post expiration based on the user role and post type.
Version: 0.1.3
Author: TC 
Author URI: http://www.9-sec.com/
*/
/*  Copyright 2012 TCK (email: devildai@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if(!class_exists('apext')){
  
	class apext{
	 		
		const apext_ver = 1;//the plugin version
		//constructor
		public function __construct(){
			add_action('init', array($this, 'ApextLanguage'),1);
	    	add_action('admin_menu', array($this,'apext_menu'));
			add_action( 'admin_init' , array( $this,'apext_init' ) );
			
			add_action('add_meta_boxes', array($this,'add_ex_field'));
			add_action('save_post', array($this,'saving_apext_meta'));
			add_action('admin_print_styles-post-new.php', array( $this,'metabox_css'));
			add_action('admin_print_styles-post.php', array( $this,'metabox_css'));
			add_action( 'wp_ajax_nopriv_adding_rule_ajax', array( $this,'adding_rule_ajax') );  
			add_action( 'wp_ajax_adding_rule_ajax', array( $this,'adding_rule_ajax') ); 
			//update meta ajax 
			add_action( 'wp_ajax_nopriv_updata_meta_ajax', array( $this,'updata_meta_ajax') );  
			add_action( 'wp_ajax_updata_meta_ajax', array( $this,'updata_meta_ajax') ); 
			add_action('post_expire',array($this,'pre_expire_posts')); 
			//shorcodes
			add_action( 'init', array($this, 'apext_register_shortcodes'));
			$this->errors = new WP_Error();
		}
		
		static function apext_activation() {
			
           wp_schedule_event( current_time( 'timestamp'), 'daily', 'post_expire' );
		}
       
       static function apext_deactivation() {
			
         wp_clear_scheduled_hook( 'post_expire' );
		}
		
	   function ApextLanguage() {
		   load_plugin_textdomain( 'Apext', false, 'posts-auto-expire-control/lang' );
	    }
		
		function apext_menu(){
		 // Add a new submenu under Settings:
			$hook = add_options_page('Posts Auto Expire Control', 'Posts Auto Expire Control', 'manage_options', 'apext_setting',array($this,'author_expirator_page'));
			add_action('admin_print_scripts-'.$hook, array($this,'controljs'));
			add_action('admin_print_styles-'.$hook, array($this,'apext_css'));	
		}
			
		function controljs() {
			wp_enqueue_script('controljs', plugins_url('js/jscontrol.js', __FILE__), array('jquery'), '1.0', true);
			wp_localize_script( 'ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );  
		}
		
		function apext_css() {
			wp_enqueue_style('apextcss', plugins_url('css/apext.css', __FILE__), '1.0', true);
		}	
		
		function metabox_css() {
			wp_enqueue_style('metaboxcss', plugins_url('css/apextmetabox.css', __FILE__), '1.0', true);
		}
		
		function apext_register_shortcodes(){

			add_shortcode('apext-expire', array($this, 'showing_expire_shortcode'));

		}

		
		function apext_init() {
			wp_enqueue_script('jscontrol');
			
				if( false == get_option( 'apext_ver' ) ) {    
				 add_option('apext_ver', self::apext_ver );	//add the version of apext.For future plugin enhancement. 
				}
			
				if( false == get_option( 'apext' ) ) {    
				 add_option( 'apext' );  
				}
				
				if( false == get_option( 'apextmail' ) ) {    
					$default_options = array(
						'pre' => '',
						'preday' => '',
						'presubject' => __('Your post "[title]" will be expired on [exdate]','Apext'),
						'precontent' => __('<p>Dear [authorname],<br>Your post "[title_link]", with the id of <b>[post_id]</b>, will be expired by <b>[exdate]</b></p>','Apext'),
						'on' => '',
						'onsubject' => __('Your post "[title]" will be expired by today','Apext'),
						'oncontent' => __('<p>Dear [authorname],<br>Your post "[title_link]", with the id of <b>[post_id]</b>, will be expired by <b>today</b>.<br>Feel free to come back to post again.</p>','Apext')
					);
							
				 add_option( 'apextmail', $default_options );  
				}
				
				register_setting( 'apext_setting', 'apext', array($this,'section_rule_fn'));
				register_setting( 'apextmail', 'apextmail', array($this,'apext_mail_fn'));
				
			
		}		
		
		function pre_expire_posts(){
				$options = get_option('apext'); 
				if(isset($options['rules'])){
					
					foreach($options['rules'] as $v) {
						$cpt = $v['cpt'];	
						$action = $v ['action'];
						$role  = $v['role'];
						$userid = $this->userids($role);	
											
						$args = array('post_status' => 'publish','post_type'=>$cpt, 'author' => $userid,'numberposts' => -1);
						$posts = get_posts( $args );
						foreach ($posts as $post) {
							$thepostid = $post->ID;
							$day = get_post_meta($thepostid, '_pebr_day_limit', true);
							
							if(!empty($day) || !isset($day)){
		            	
							$todays_date = date("Y-m-d"); 
							$today = strtotime($todays_date); 
						
							$postdate = get_the_time('Y-m-d', $thepostid);
			           
							$expiry_date = strtotime(''.$day.' day' ,strtotime($postdate)); 
		               
							//$notification = strtotime('-3 day' ,$expiry_date);
							$this->apext_notification($post,$today,$expiry_date);
							$this->apext_expire_fn($thepostid,$today,$expiry_date,$action);
						
						}
		       	      
		           }
		         } 
	           } 
        }
        
        function apext_expire_fn($thepostid,$today,$expiry_date,$action) {
	
			if ($today > $expiry_date ){
			$ex_post = array();
			$ex_post['ID'] = $thepostid;
			$ex_post['post_status'] = $action;// Update the post into the database
			$ex_post_array = apply_filters( 'apext_expost_array', $ex_post,$thepostid,$action,$expiry_date);
		
			wp_update_post( $ex_post_array );							 
			}


			do_action( 'apext_expost_action', $thepostid,$today,$expiry_date,$action);
	    }
	    
	    function apext_notification($post,$today,$expiry_date) {
		  $options = get_option('apextmail');
		  if(isset($options)){
				
			$user_info = get_userdata($post->post_author);	
			$author_name =  $user_info->user_login;
			$author_email = $user_info->user_email;
			$permalink = get_permalink( $post->ID);
			$title = $post->post_title;
			$admin = get_userdata(1);
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';
			$notification = strtotime('-'.$options['preday'].' day' ,$expiry_date);
			$notifymessage = $options['precontent']; 
			$ondaymessage = $options['oncontent'];
			$notisubject = $options['presubject'];
			$expisubject = $options['onsubject'];
			
				$data = array(
				"[authorname]" => $author_name,
				"[sitename]" => get_option("blogname"),
				"[title_link]" => '<a href="'.$permalink.'" target="_blank">'.$title.'</a>',
				"[title]" => $title,
				"[exdate]" => date('F j, Y',$expiry_date),
				"[admin_name]" => $admin->user_login,
				"[post_id]" => $post->ID
				);
				
			$data = apply_filters( 'apext_mail_content', $data );
			$presubject = str_replace( array_keys($data) , $data, $notisubject);
			$expisubject = str_replace( array_keys($data) , $data, $expisubject);
			$premessage = str_replace( array_keys($data) , $data, $notifymessage);
			$onmessage = str_replace( array_keys($data) , $data, $ondaymessage);
			
								
			if (($options['pre'] == '1') && !empty($options['preday']) && $today == $notification ) {
							$send=wp_mail($author_email, $presubject, $premessage, $headers);
							/*if(!$send){
								wp_mail('yourmail@example.com','fail send mail','the plugin failed to send',$headers);
								};*///testing sending status. uncomment this if you want the system send you the error when the plugin failed to sent the mail to the user. replace the 'yourmail@example.com' to your email.
							}
				        
			elseif(($options['on'] == '1') && ($today == $expiry_date)) {
						   $send=wp_mail($author_email, $expisubject, $onmessage, $headers);
						   /*if(!$send){
								wp_mail('yourmail@example.com','fail send mail','the plugin failed to send',$headers);
								};*///testing sending status. uncomment this if you want the system send you the error when the plugin failed to sent the mail to the user. replace the 'yourmail@example.com' to your email.
						 }	
			else{return;}	
			
				
				}
			
		}
	    
	    function userids($role) {
		$user_ids = get_users( array('role' => $role ));
		$userarray = array();
		foreach ($user_ids as $user) {
				
     			$userarray[]= $user->ID;
   				 }
		return implode(',',$userarray);
		}		
			
    	function author_expirator_page() {
			include "html/render_output.php";
		}
			
		function add_new_rule_form() {
			include "html/add_rule_form.php";
		}		
		
		function adding_rule_ajax() {
			include "html/add_rule_ajax.php";
		}
		
		function mail_setting_cb() {
			include "html/mailsetting.php";
		}
		
		function updata_meta_ajax() {
				$options = get_option('apext'); 
				if(isset($options['rules'])){
					$xuser = array();
					$xcpt = array();
					foreach($options['rules'] as $v) {
						$cpt = $v['cpt'];	
						
						$role  = $v['role'];
						$limit = $v['day'];
						$userid = $this->userids($role);	
						$xcpt[] =  $cpt;
						$args = array('post_status' => 'publish','post_type'=>$cpt, 'author' => $userid,'numberposts' => -1);
						$posts = get_posts( $args );
						foreach ($posts as $post) {
								$postdb[] = $post->ID;	
								update_post_meta($post->ID,'_pebr_day_limit',$limit);      
						}		           
		           }
				$args2 =array('post_status' => 'publish','meta_key'=>'_pebr_day_limit','numberposts' => -1, 'post__not_in'=>$postdb,'post_type' =>'any');
				$xposts = get_posts($args2 );
					foreach($xposts as $xpost){
								delete_post_meta($xpost->ID,'_pebr_day_limit');   
						 }
			  }
		    else{  //if all the rules removed,we need to delete all the meta in previous published post.
		     $posts = get_posts(array('post_status' => 'publish','meta_key'=>'_pebr_day_limit','numberposts' => -1, 'post_type' =>'any'));
			 foreach($posts as $post) {
							    delete_post_meta($post->ID,'_pebr_day_limit');   	      
		           } 
				 }
				  
				  
				 
	   }
		
	   function section_rule_fn($input) {
			$error=null;
			$uniq = array();
			foreach($input['rules'] as $v) {
			$key = $v['role'] . '-' . $v['cpt'];
			if (!isset($uniq[$key]))
			$uniq[$key] = 0;
			else
			$uniq[$key]++;
			$day = is_numeric($v['day']);
			
			if(!$day){$this->numeric_error();exit;}
			}
		
			$dupli =array_filter($uniq);	
			if($dupli){
				$this->duplicate_error();
				exit;
				}elseif(!$dupli){
			return $input;}
							
		}
		
		function duplicate_error() {
						
			$updatedlink=add_query_arg( 'error', 'duplicate', admin_url('options-general.php?page=apext_setting') );
			wp_redirect( $updatedlink );	
		
		}	
		
		function numeric_error() {
						
			$updatedlink=add_query_arg( 'error', 'numeric', admin_url('options-general.php?page=apext_setting') );
			wp_redirect( $updatedlink );	
		
		}	
		
		function apext_mail_fn($input){
			 global $allowedtags;
			 $mailtags = array(
				//formatting
				'strong' => array(),
				'em'     => array(),
				'b'      => array(),
				'i'      => array(),
				'p'      => array(),
				'li'      => array(),
				'ul'  => array(),
				//links
				'a'     => array(
				'href' => array()
			 )
			);
	
			$valid = array();
			$valid['pre'] = strip_tags( stripslashes( $input['pre']));
			$valid['presubject'] = wp_kses($input['presubject'],$mailtags);
			$valid['precontent'] = wp_kses($input['precontent'],$mailtags);
			$valid['on'] =esc_attr($input['on']);
			$valid['onsubject'] = wp_kses($input['onsubject'],$mailtags);
			$valid['oncontent'] = wp_kses($input['oncontent'],$mailtags);
			$digit  = is_int($input['preday']);
			if(($input['pre'] == '1') && ($input['preday'] == null)){
				$this->empty_mail_error();exit;
				}
			elseif(!$digit && $digit != null){$this->mail_numeric_error();exit;}
			else {$valid['preday'] = esc_attr($input['preday']);}
			
		    return $valid;	
		}
		
		function mail_numeric_error(){
			$updatedlink=add_query_arg( 'error', 'digit', admin_url('options-general.php?page=apext_setting#apextmaildiv') );
			wp_redirect( $updatedlink );
			
		}
		
		function empty_mail_error(){
			$updatedlink=add_query_arg( 'error', 'digitnull', admin_url('options-general.php?page=apext_setting#apextmaildiv') );
			wp_redirect( $updatedlink );
		}
		
		function add_ex_field() {
			global $wp_roles;
			$current_user = wp_get_current_user();
			$roles = $current_user->roles;
			$role = array_shift($roles);
			$options = get_option('apext');  
			if(isset($options['rules'])){
			  foreach($options['rules'] as  $v){
				  
					
				  if(($role == 'administrator') || ('administrator'==$v['role'])){ 	
					add_meta_box( 'apextadmin', __( __("Posts Auto Expire Control","Apext"), 'myplugin_textdomain' ),array($this,'apext_custom_box'),$v['cpt'],'normal','low');}
					elseif($role==$v['role']) {
					add_meta_box( 'apextuser', __( __("Posts Auto Expire Control","Apext"), 'myplugin_textdomain' ),array($this,'apext_custom_box'),$v['cpt'],'normal','low');}
			  } 
			}
		
		}
	
		function apext_custom_box($post,$metabox) {
			echo '<input type="hidden" name="apext_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
			$limit = get_post_meta($post->ID, '_pebr_day_limit', true); 
			echo  '<input type="text" name="_pebr_day_limit" id="user_role_id" value="'.$limit.'"   size="2">';
			echo '&nbsp;<label>'.__(" days to expire since the post has published.","Apext").'</label>';	
				
		}
	
		function saving_apext_meta($post) {
			// verify nonce -- checks that the user has access
		 if ( !isset($_POST['apext_nonce']) || !wp_verify_nonce( $_POST['apext_nonce'], basename(__FILE__) )) {
			return $post;
		   }
			   if(!empty($_POST['_pebr_day_limit'])){	
			   $limit = $_POST['_pebr_day_limit'];}
			   else{$limit = $this->getapextlimit($post);} 
			   update_post_meta($post,'_pebr_day_limit',$limit);   
		}
		
		function getapextlimit($post) {
		 global $wp_roles; 
				$current_user = wp_get_current_user();
				$roles = $current_user->roles;
				$role = array_shift($roles);    
				$options = get_option('apext');
			foreach($options['rules'] as  $v){
				if(($role==$v['role']) && (get_post_type($post) == $v['cpt'])){
				$limit = $v['day'];
				}
			}
			
			return $limit;
		}
		
		function notify_render() {
			
			
		}
		
		function showing_expire_shortcode() {
			$postid = get_the_ID();
			$limit = get_post_meta($postid, '_pebr_day_limit', true); 
			$postdate = get_the_time('Y-m-d', $postid);
			$expiry_date = strtotime(''.$limit.' day' ,strtotime($postdate));
			$todays_date = date("Y-m-d");
		    $today = strtotime($todays_date);  
			
			if($expiry_date > $today ){
			$message = '<div class="apext_expire">This post will be expired on <span>'.date("F d, Y",$expiry_date).'</span></div>';	
			
			}
			elseif($today == $expiry_date){
			$message = '<div class="apext_expire">This post will be expired by today</div>';	}
			
			else {$message = 'error';}
			return $message;
			
		} 
	
	
			
				
	}//end class
}//end if class exists
register_activation_hook( __FILE__, array('apext', 'apext_activation') );
register_deactivation_hook( __FILE__, array('apext', 'apext_deactivation') );	
global $apext;
$apext = new apext();
