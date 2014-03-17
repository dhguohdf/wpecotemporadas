<?php if ( has_nav_menu( 'topbar' ) ) {

	
	$menu_params = array(
			'theme_location'  => 'topbar',
			'container'       => 'nav',
			'container_id'    => 'menu-topbar',
			'container_class' => 'large-6 column',
			'menu_id'         => 'menu-topbar-items',
			'menu_class'      => 'left menu-items',
			'fallback_cb'     => '',
			'depth'			  => '1',
		);

	
	wp_nav_menu($menu_params);
	

} ?>