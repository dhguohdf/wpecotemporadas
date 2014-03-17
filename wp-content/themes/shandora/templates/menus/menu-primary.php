<?php if ( has_nav_menu( 'primary' ) ) {

	
	$menu_params = array(
			'theme_location'  => 'primary',
			'container'       => 'nav',
			'container_id'    => 'menu-primary',
			'container_class' => 'menu clearfix',
			'menu_id'         => 'menu-primary-items',
			'menu_class'      => 'menu-items clearfix',
			'fallback_cb'     => '',
			'walker'		  => new Shandora_Navigation_Menu,
		);
	
	wp_nav_menu($menu_params);

} ?>