<?php

/* Add the post SEO meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'bon_meta_box_post_add_seo', 10, 2 );
add_action( 'add_meta_boxes', 'bon_meta_box_post_remove_seo', 10, 2 );

/* Save the post SEO meta box data on the 'save_post' hook. */
add_action( 'save_post', 'bon_meta_box_post_save_seo', 10, 2 );
add_action( 'add_attachment', 'bon_meta_box_post_save_seo' );
add_action( 'edit_attachment', 'bon_meta_box_post_save_seo' );

/**
 * Adds the post SEO meta box for all public post types.
 *
 * @since 1.2.0
 * @param string $post_type The post type of the current post being edited.
 * @param object $post The current post being edited.
 * @return void
 */
function bon_meta_box_post_add_seo( $post_type, $post ) {

	$post_type_object = get_post_type_object( $post_type );

	/* Only add meta box if current user can edit, add, or delete meta for the post. */
	if ( ( true === $post_type_object->public ) && ( current_user_can( 'edit_post_meta', $post->ID ) || current_user_can( 'add_post_meta', $post->ID ) || current_user_can( 'delete_post_meta', $post->ID ) ) )
		add_meta_box( 'bon-post-seo', __( 'SEO', 'bon' ), 'bon_meta_box_post_display_seo', $post_type, 'normal', 'high' );
}

/**
 * Remove the meta box from some post types.
 *
 * @since 1.3.0
 * @param string $post_type The post type of the current post being edited.
 * @param object $post The current post being edited.
 * @return void
 */ 
function bon_meta_box_post_remove_seo( $post_type, $post ) {

	/* Removes post stylesheets support of the bbPress 'topic' post type. */
	if ( function_exists( 'bbp_get_topic_post_type' ) && bbp_get_topic_post_type() == $post_type )
		remove_meta_box( 'bon-post-seo', bbp_get_topic_post_type(), 'normal' );

	/* Removes post stylesheets support of the bbPress 'reply' post type. */
	elseif ( function_exists( 'bbp_get_reply_post_type' ) && bbp_get_reply_post_type() == $post_type )
		remove_meta_box( 'bon-post-seo', bbp_get_reply_post_type(), 'normal' );
}

/**
 * Displays the post SEO meta box.
 *
 * @since 1.2.0
 * @return void
 */
function bon_meta_box_post_display_seo( $object, $box ) {

	wp_nonce_field( basename( __FILE__ ), 'bon-post-seo' ); ?>

	<p>
		<label for="bon-document-title"><?php _e( 'Document Title:', 'bon' ); ?></label>
		<br />
		<input type="text" name="bon-document-title" id="bon-document-title" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Title', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>

	<p>
		<label for="bon-meta-description"><?php _e( 'Meta Description:', 'bon' ); ?></label>
		<br />
		<textarea name="bon-meta-description" id="bon-meta-description" cols="60" rows="2" tabindex="30" style="width: 99%;"><?php echo esc_textarea( get_post_meta( $object->ID, 'Description', true ) ); ?></textarea>
	</p>

	<p>
		<label for="bon-meta-keywords"><?php _e( 'Meta Keywords:', 'bon' ); ?></label>
		<br />
		<input type="text" name="bon-meta-keywords" id="bon-meta-keywords" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Keywords', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>
<?php }

/**
 * Saves the post SEO meta box settings as post metadata.
 *
 * @since 1.2.0
 * @param int $post_id The ID of the current post being saved.
 * @param int $post The post object currently being saved.
 */
function bon_meta_box_post_save_seo( $post_id, $post = '' ) {

	$prefix = bon_get_prefix();

	/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
	if ( !is_object( $post ) )
		$post = get_post();

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['bon-post-seo'] ) || !wp_verify_nonce( $_POST['bon-post-seo'], basename( __FILE__ ) ) )
		return $post_id;

	$meta = array(
		'Title' => 	$_POST['bon-document-title'],
		'Description' => 	$_POST['bon-meta-description'],
		'Keywords' => 	$_POST['bon-meta-keywords']
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If there is no new meta value but an old value exists, delete it. */
		if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );

		/* If a new meta value was added and there was no previous value, add it. */
		elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );
	}
}

?>