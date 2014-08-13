<?php

function bon_entry_class( $class = '', $post_id = null, $echo = true ) {
	_deprecated_function( __FUNCTION__, '1.0', "bon_attr('post')" );
}

function bon_comment_class( $class = '' ) {
	_deprecated_function( __FUNCTION__, '1.0', "bon_attr('comment')" );
}

function bon_body_class( $class = '' ) {
	_deprecated_function( __FUNCTION__, '1.0', "bon_attr('body')" );
}

function bon_document_title() {
	_deprecated_function( __FUNCTION__, '1.0', "bon_wp_title()" );
	bon_wp_title();
}
