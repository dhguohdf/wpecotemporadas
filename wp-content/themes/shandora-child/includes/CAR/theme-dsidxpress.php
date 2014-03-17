<?php
	


    add_action('widgets_init', 'shandora_widget_init_dsidxpress');

    function shandora_widget_init_dsidxpress() {

        unregister_widget( 'dsSearchAgent_SearchWidget' );

        require_once('widgets/widget-dsidxpress-search.php');

        register_widget( 'Shandora_dsIDXpress_Search' );
    }

    if( !defined( 'BON_IDX_DIR') ) {
        define( 'BON_IDX_DIR', trailingslashit( BON_INC ) . 'idx-includes/' );
    }

	require_once( trailingslashit( BON_IDX_DIR ) . 'idx-main.php');
?>