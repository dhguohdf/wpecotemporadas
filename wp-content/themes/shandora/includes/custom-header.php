<?php

function shandora_custom_header_setup() {
	$args = array(
		// Text color and image (empty to use none).

		'default-image'          => '%s/assets/images/headers/silhouette1.png',

		// Set height and width, with a maximum value for the width.
		'height'                 => 120,
		'width'                  => 1240,

		'header-text'			 => false,

		// Callbacks for styling the header and the admin preview.
		'wp-head-callback'       => 'shandora_header_style',
		'admin-head-callback'    => 'shandora_admin_header_style',
		'admin-preview-callback' => 'shandora_admin_header_image',
	);

	add_theme_support( 'custom-header', $args );

	/*
	 * Default custom headers packaged with the theme.
	 * %s is a placeholder for the theme template directory URI.
	 */
	register_default_headers( array(
		'silhouette1' => array(
			'url'           => '%s/assets/images/headers/silhouette1.png',
			'thumbnail_url' => '%s/assets/images/headers/thumb-silhouette1.png',
			'description'   => _x( 'Silhouette 1', 'header image description', 'bon' )
		),
		
	) );
}
add_action( 'after_setup_theme', 'shandora_custom_header_setup' );


/**
 * Styles the header text displayed on the blog.
 *
 * get_header_textcolor() options: Hide text (returns 'blank'), or any hex value.
 *
 * @since Twenty Thirteen 1.0
 */
function shandora_header_style() {
	$header_image = get_header_image();

	// If no custom options for text are set, let's bail.
	if ( empty( $header_image ) )
		return;

	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="shandora-header-css">
	<?php
		if ( ! empty( $header_image ) ) :
	?>
		#header-background {
			background: url(<?php header_image(); ?>) no-repeat scroll bottom;
			background-size: 1240px auto;
			height: 120px;
		}

	<?php endif; ?>
	</style>
	<?php
}

/**
 * Outputs markup to be displayed on the Appearance > Header admin panel.
 * This callback overrides the default markup displayed there.
 *
 * @since Twenty Thirteen 1.0
 */
function shandora_admin_header_image() {
	?>
	<div id="headimg" style="background: url(<?php header_image(); ?>) no-repeat scroll top; background-size: 1240px auto;"></div>
<?php }

/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @since Twenty Thirteen 1.0
 */
function shandora_admin_header_style() {
	$header_image = get_header_image();
?>
	<style type="text/css" id="shandora-admin-header-css">
	.appearance_page_custom-header #headimg {
		border: none;
		height: 120px;
		width: 100%;
		-webkit-box-sizing: border-box;
		-moz-box-sizing:    border-box;
		box-sizing:         border-box;
		<?php
		if ( ! empty( $header_image ) ) {
			echo 'background: url(' . esc_url( $header_image ) . ') no-repeat scroll bottom; background-size: 1240px auto;';
		} ?>
		padding: 0 20px;
	}
	</style>
<?php
} ?>