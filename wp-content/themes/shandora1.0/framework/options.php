<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php

function bon_set_framework_options() {
	$shortlabel =  'bon_framework';

	$framework_options = array();

	$framework_options[] = array( 'slug' =>'bon_framework',	'label' => __( 'Admin Settings', 'bon' ),
									'icon' => 'general',
									'type' => 'heading' );

	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'Super User (username)', 'bon' ),
									'desc' => sprintf( __( 'Enter your %s to hide the Framework Settings and Update Framework from other users.', 'bon' ), '<strong>' . __( 'username', 'bon' ) . '</strong>' ),
									'id' => $shortlabel . '_super_user',
									'std' => '',
									'class' => 'text',
									'type' => 'text' );

	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'Disable Backup Settings Menu Item', 'bon' ),
									'desc' => sprintf( __( 'Disable the %s menu item in the theme menu.', 'bon' ), '<strong>' . __( 'Backup Settings', 'bon' ) . '</strong>' ),
									'id' => $shortlabel . '_backupmenu_disable',
									'std' => '',
									'type' => 'checkbox' );

	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'Theme Update Notification', 'bon' ),
									'desc' => __( 'This will enable notices on your theme options page that there is an update available for your theme.', 'bon' ),
									'id' => $shortlabel . '_theme_version_checker',
									'std' => '',
									'type' => 'checkbox' );

	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'Branding', 'bon' ),
									'icon' => 'misc',
									'type' => 'heading' );

	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'Options panel header', 'bon' ),
									'desc' => __( 'Change the header image for the Theme Backend.', 'bon' ),
									'id' => $shortlabel . '_backend_header_image',
									'std' => '',
									'type' => 'upload' );

	$framework_options[] = array(  'slug' =>'bon_framework',	'label' => __( 'Options panel icon', 'bon' ),
									'desc' => __( 'Change the icon image for the WordPress backend sidebar.', 'bon' ),
									'id' => $shortlabel . '_backend_icon',
									'std' => '',
									'type' => 'upload' );

	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'WordPress login logo', 'bon' ),
									'desc' => __( 'Change the logo image for the WordPress login page.', 'bon' ) . '<br /><br />' . __( 'Optimal logo size is 274x63px', 'bon' ),
									'id' => $shortlabel . '_custom_login_logo',
									'std' => '',
									'type' => 'upload' );

	$framework_options[] = array(  'slug' =>'bon_framework',	'label' => __( 'WordPress login URL', 'bon' ),
									'desc' => __( 'Change the URL that the logo image on the WordPress login page links to.', 'bon' ),
									'id' => $shortlabel . '_custom_login_logo_url',
									'std' => '',
									'class' => 'text',
									'type' => 'text' );
									
	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'WordPress login logo Title', 'bon' ),
									'desc' => __( 'Change the title of the logo image on the WordPress login page.', 'bon' ),
									'id' => $shortlabel . '_custom_login_logo_title',
									'std' => '',
									'class' => 'text',
									'type' => 'text' );

	$framework_options[] = array( 'slug' =>'bon_framework', 	'label' => __( 'Remove Frontend Admin Bar', 'bon' ),
									'desc' => __( 'Remove Admin bar from the frontend.', 'bon' ),
									'id' => $shortlabel . '_remove_admin_bar',
									'std' => '',
									'type' => 'select',
									'options' => array(
										'true' => 'Yes',
										'false' => 'No',
										)
									);


	return $framework_options;
 } ?>