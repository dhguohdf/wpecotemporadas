<?php
load_theme_textdomain('bon');

if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* Start BonThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// BonFramework init
require_once ( get_template_directory() . '/framework/bon.php' );

/*-----------------------------------------------------------------------------------*/
/* Load the theme-specific files, with support for overriding via a child theme.
/*-----------------------------------------------------------------------------------*/


$includes = array(
				'includes/theme-supports.php', // register / setup theme supports
				'includes/theme-posttypes.php',  // Custom theme posttypes
				'includes/search-fields.php',
				'includes/theme-actions.php',  // Theme actions & user defined hooks
				'includes/theme-toolkit-override.php',
				'includes/theme-hooks.php',
				'includes/theme-plugins.php',
				'includes/theme-shortcodes.php',
				'includes/custom-header.php',
				'includes/custom-background.php',
				'includes/theme-widgets.php',
				'includes/theme-head.php',
				);

// Allow child themes/plugins to add widgets to be loaded.

$includes = apply_filters( 'shandora_includes', $includes );



foreach ( $includes as $i ) {
	require_once( $i );
}

function shandora_admin_init() {
	
	if(!defined( "DSIDXPRESS_OPTION_NAME" )) {
		return;
	}

	$idx_opt = get_option(DSIDXPRESS_OPTION_NAME);

	if(is_plugin_active('dsidxpress/dsidxpress.php') && isset($idx_opt['Activated'])) {
		require_once( 'includes/theme-dsidxpress.php' );
	}
}



add_action( 'after_setup_theme', 'shandora_admin_init');
// this function for checking update in ThemeForest! Please do not edit the code
function shandora_updater() {
	if(bon_get_framework_option('bon_framework_update_notification') == true) {
		
		require_once( trailingslashit( BON_CLASSES ) . 'class-pixelentity-theme-update.php');

		$username = bon_get_framework_option('bon_framework_envato_username');

		$apikey = bon_get_framework_option('bon_framework_envato_api');

		$author = 'Hermanto Lim';

		PixelentityThemeUpdate::init($username,$apikey,$author); 
	}
}

add_action('admin_init', 'shandora_updater');

/*-----------------------------------------------------------------------------------*/
/* You can add custom functions below */
/*-----------------------------------------------------------------------------------*/

function shandora_first_and_last_menu_class($items) {
    $items[1]->classes[] = 'first';
    $items[count($items)]->classes[] = 'last';
    return $items;
}
add_filter('wp_nav_menu_objects', 'shandora_first_and_last_menu_class');


class Shandora_Navigation_Menu extends Walker_Nav_Menu {
    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
        $id_field = $this->db_fields['id'];
        if ( !empty( $children_elements[ $element->$id_field ] ) ) {
            $element->classes[] = 'menu-has-children';
        }
        Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    function end_el(&$output, $item, $depth=0, $args=array()) {
    	if(in_array('menu-has-children', $item->classes) )  {
    		$output .= '<i class="icon awe-angle-down menu-toggle"></i>';
    	}
    	parent::end_el($output, $item, $depth, $args);
    }
}

function shandora_add_listing_class($class) {
	if(is_singular('car-listing')) {
		$class .= ' singular-listing';
		return $class;
	} else {
		return $class;
	}
}
add_filter('shandora_body_class', 'shandora_add_listing_class');

add_action('admin_footer','posts_status_color');
function posts_status_color(){
?>
<style>
.status-draft{background: #edd2d1 !important; color: #990500 !important;}
.status-pending{background: #E1F9FA !important;}
.status-publish{background: #d1ede0 !important;}
.status-future{background: red !important;}
.status-private{background:#ffdcb4;}

</style>
<?php
}

function custom_post_status(){
	register_post_status( 'status-processo-revisao', array(
		'label'                     => false,
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'private'                   => true,
		'label_count'               => _n_noop( 'Processo de Revisão <span class="count">(%s)</span>', 'Processos de revisão <span class="count">(%s)</span>' ),
	) );
}
add_action( 'init', 'custom_post_status' );

function test_modify_post_table( $column ) {
    $column['pagamento'] = 'Pagamento'; 
    return $column;
}



add_filter( 'manage_posts_columns', 'test_modify_post_table' );

function test_modify_post_table_row( $column_name, $post_id ) {
	if (get_post_status ( $ID ) == 'private'	) {
    switch ($column_name) {
        case 'pagamento' :
		$EmailVendedor = 'andre@eaxdesign.com.br';
		$identificacao = get_the_ID();
		global $current_user;
      	get_currentuserinfo();
      	echo '<form><spam></spam></form>';
        echo '<form target="PagSeguro" action="https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx" method="post" name="TestePS" id="TestePS" />

			<input type="hidden" name="email_cobranca" value="'.$EmailVendedor.'">
			<input type="hidden" name="tipo" value="CP" />
			<input type="hidden" name="moeda" value="BRL" />
			<input type="hidden" name="ref_transacao" value="ID '.$identificacao.'" />

			<input type="hidden" name="item_id_1" value="1" />
			<input type="hidden" name="item_descr_1" value="Anuncio ECOtrimestral" />
			<input type="hidden" name="item_quant_1" value="1" />
			<input type="hidden" name="item_valor_1" value="85,00" />

			<input type="hidden" name="cliente_nome" value="'.$current_user->user_firstname.' '.$current_user->user_lastname.'" />
			<input type="hidden" name="cliente_end" value="'.$current_user->addr1.'" />
			<input type="hidden" name="cliente_num" value="'.$current_user->numaddr.'" />
			<input type="hidden" name="cliente_compl" value="'.$current_user->addr2.'" />
			<input type="hidden" name="cliente_bairro" value="'.$current_user->bairro.'" />
			<input type="hidden" name="cliente_cidade" value="'.$current_user->city.'" />
			<input type="hidden" name="cliente_uf" value="'.$current_user->uf.'" />
			<input type="hidden" name="cliente_pais" value="'.$current_user->country.'" />
			<input type="hidden" name="cliente_ddd" value="'.$current_user->ddd.'" />

			<input type="hidden" name="cliente_tel" value="'.$current_user->phone1.'" />
			<input type="hidden" name="cliente_email" value="'.$current_user->user_email.'" />
			<input type="image" src="http://ecotemporadas.com/wp-content/uploads/website-img/botao_trimestral.jpg" alt="Plano Trimestral"></form>';


		echo '<form target="PagSeguro" action="https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx" method="post" name="TestePS" id="TestePS" />

			<input type="hidden" name="email_cobranca" value="'.$EmailVendedor.'">
			<input type="hidden" name="tipo" value="CP" />
			<input type="hidden" name="moeda" value="BRL" />
			<input type="hidden" name="ref_transacao" value="ID '.$identificacao.'" />

			<input type="hidden" name="item_id_1" value="2" />
			<input type="hidden" name="item_descr_1" value="Anuncio ECOsemestral" />
			<input type="hidden" name="item_quant_1" value="1" />
			<input type="hidden" name="item_valor_1" value="160,00" />

			<input type="hidden" name="cliente_nome" value="'.$current_user->user_firstname.' '.$current_user->user_lastname.'" />
			<input type="hidden" name="cliente_end" value="'.$current_user->addr1.'" />
			<input type="hidden" name="cliente_num" value="'.$current_user->numaddr.'" />
			<input type="hidden" name="cliente_compl" value="'.$current_user->addr2.'" />
			<input type="hidden" name="cliente_bairro" value="'.$current_user->bairro.'" />
			<input type="hidden" name="cliente_cidade" value="'.$current_user->city.'" />
			<input type="hidden" name="cliente_uf" value="'.$current_user->uf.'" />
			<input type="hidden" name="cliente_pais" value="'.$current_user->country.'" />
			<input type="hidden" name="cliente_ddd" value="'.$current_user->ddd.'" />

			<input type="hidden" name="cliente_tel" value="'.$current_user->phone1.'" />
			<input type="hidden" name="cliente_email" value="'.$current_user->user_email.'" />

			<input type="image" src="http://ecotemporadas.com/wp-content/uploads/website-img/botao_semestral.jpg" alt="Plano Semestral">
			</form>';

		echo '<form target="PagSeguro" action="https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx" method="post" name="TestePS" id="TestePS" />

			<input type="hidden" name="email_cobranca" value="'.$EmailVendedor.'">
			<input type="hidden" name="tipo" value="CP" />
			<input type="hidden" name="moeda" value="BRL" />
			<input type="hidden" name="ref_transacao" value="ID '.$identificacao.'" />

			<input type="hidden" name="item_id_1" value="3" />
			<input type="hidden" name="item_descr_1" value="Anuncio ECOanual" />
			<input type="hidden" name="item_quant_1" value="1" />
			<input type="hidden" name="item_valor_1" value="310,00" />

			<input type="hidden" name="cliente_nome" value="'.$current_user->user_firstname.' '.$current_user->user_lastname.'" />
			<input type="hidden" name="cliente_end" value="'.$current_user->addr1.'" />
			<input type="hidden" name="cliente_num" value="'.$current_user->numaddr.'" />
			<input type="hidden" name="cliente_compl" value="'.$current_user->addr2.'" />
			<input type="hidden" name="cliente_bairro" value="'.$current_user->bairro.'" />
			<input type="hidden" name="cliente_cidade" value="'.$current_user->city.'" />
			<input type="hidden" name="cliente_uf" value="'.$current_user->uf.'" />
			<input type="hidden" name="cliente_pais" value="'.$current_user->country.'" />
			<input type="hidden" name="cliente_ddd" value="'.$current_user->ddd.'" />

			<input type="hidden" name="cliente_tel" value="'.$current_user->phone1.'" />
			<input type="hidden" name="cliente_email" value="'.$current_user->user_email.'" />

			<input type="image" src="http://ecotemporadas.com/wp-content/uploads/website-img/botao_anual.jpg" alt="Plano Anual">
			</form>';
            break;
 
        default:
    }}
}
 
add_filter( 'manage_posts_custom_column', 'test_modify_post_table_row', 10, 2 );



		add_filter( 'display_post_states', 'custom_post_state' );
        function custom_post_state( $states ) {
                global $post;
                $show_custom_state = get_post_meta( $post->ID, '_status' );
                   if ( $show_custom_state ) {
                        $states[] = __( '<br><br><span class="custom_state '.strtolower($show_custom_state[0]).'">'.$show_custom_state[0].'</span>' );
                        }
                return $states;
        }
        add_action( 'post_submitbox_misc_actions', 'custom_status_metabox' );
        function custom_status_metabox(){
                global $post;
                $custom  = get_post_custom($post->ID);
                $status  = $custom["_status"][0];
                $i   = 0;
                /* ----------------------------------- */
                /*   Array of custom status messages            */
                /* ----------------------------------- */
                $custom_status = array(
                                'Anúncio Revisado - OK',
                                'Anúncio Recusado - Aguardando revisão',
                                'Anúncio Trimestral',
                                'Anúncio Semestral',
                                'Anúncio Anual',
                                'Final',
                        );
                echo '<div class="misc-pub-section custom">';
                echo '<label>Custom status: </label><select name="status">';
                echo '<option class="default">Revisão</option>';
                for($i=0;$i<count($custom_status);$i++){
                        if($status == $custom_status[$i]){
                            echo '<option value="'.$custom_status[$i].'" selected="true">'.$custom_status[$i].'</option>';
                          }else{
                            echo '<option value="'.$custom_status[$i].'">'.$custom_status[$i].'</option>';
                          }
                        }
                echo '</select>';
                echo '<br /></div>';
        }
        add_action('save_post', 'save_status');
        function save_status(){
                global $post;
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){ return $post->ID; }
                update_post_meta($post->ID, "_status", $_POST["status"]);
        }
        add_action( 'admin_head', 'status_css' );
        function status_css() {
                echo '<style type="text/css">
                .expirationdate{
			    background: none repeat scroll 0 0 #E78229;
			    color: white !important;
			}

			.date {
				font-style: oblique;
				font-size: 12px;
			}
                .default{font-weight:bold;}
                .custom{border-top:solid 1px #e5e5e5;}
                .custom_state{
                        font-size:12px;
                        color:#666;
                        background:#e5e5e5;
                        padding:3px 6px 3px 6px;
                        -moz-border-radius:3px;
                        }
                        /* ----------------------------------- */
                        /*   change color of messages bellow            */
                        /* ----------------------------------- */
                        .revisado{background:#4BC8EB;color:#fff;}
                        .recusado{background:#C90016;color:#fff;}
                        .trimestral{background:#03C03C;color:#fff;font-style:oblique;}
                        .semestral{background:#03C03C;color:#fff;font-style:oblique;}
                        .anual{background:#03C03C;color:#fff;font-style:oblique;}
                        .final{background:#DE9414;color:#333;}
                        </style>';
        }

        //removes quick edit from custom post type list
function remove_quick_edit( $actions ) {
	global $post;
    if( $post->post_type == 'listing' || 'agent' ) {
		unset($actions['inline hide-if-no-js']);
	}
    return $actions;
}

add_action('wp_footer', 'add_googleanalytics');
 
function add_googleanalytics() { } 


function wp_admin_bar_new_item() {
global $wp_admin_bar;
$wp_admin_bar->add_menu(array(
'id' => 'new-content',
'title' => __('<span class="ab-icon"></span><span class="ab-label">Crie seu anúncio agora!</span>'),
'href' => 'ecotemporadas.com/wp-admin/post-new.php?post_type=listing'
));
}
add_action('wp_before_admin_bar_render', 'wp_admin_bar_new_item');

//Loading the drag'n drop blocker script
function load_custom_wp_admin_js() {
        wp_register_script( 'my_custom_script', get_template_directory_uri() . '/custom.js');
        wp_enqueue_script( 'my_custom_script' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_js' );

add_filter( 'get_user_option_admin_color', function( $color_scheme ) {
global $_wp_admin_css_colors;

if ( ! isset( $_wp_admin_css_colors[ $color_scheme ] ) ) {
    $color_scheme = 'seaweed';
}

return $color_scheme;

}, 5 );

function my_plugin_get_comment_list_by_user($clauses) {
if (is_admin()) {
    global $user_ID, $wpdb;
    $clauses['join'] = ", wp_posts";
    $clauses['where'] .= " AND wp_posts.post_author = ".$user_ID." AND wp_comments.comment_post_ID = wp_posts.ID";
};
return $clauses;
};
// Ensure that editors and admins can moderate all comments
if(!current_user_can('edit_others_posts')) {
add_filter('comments_clauses', 'my_plugin_get_comment_list_by_user');
}

function set_messages($messages) {
global $post, $post_ID;
$post_type = get_post_type( $post_ID );

$obj = get_post_type_object($post_type);
$singular = $obj->labels->name;

$messages[$post_type] = array(
0 => '', // Unused. Messages start at index 1.
1 => sprintf( __($singular.' criado. <a href="%s">Ver '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
2 => __('Custom field updated.'),
3 => __('Custom field deleted.'),
4 => __($singular.' updated.'),
5 => isset($_GET['revision']) ? sprintf( __($singular.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
6 => sprintf( __($singular.' criado. <a href="%s">Visualizar '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
7 => __('Page saved.'),
8 => sprintf( __($singular.' enviado para revisão. <a target="_blank" href="%s">Ver '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
9 => sprintf( __($singular.' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.strtolower($singular).'</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
10 => sprintf( __($singular.' draft updated. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
);
return $messages;
}

add_filter('post_updated_messages', 'set_messages' );



// TIPS
add_action( 'admin_enqueue_scripts', 'my_admin_enqueue_scripts' );
function my_admin_enqueue_scripts() {
    wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
    add_action( 'admin_print_footer_scripts', 'my_admin_print_footer_scripts' );
}
function my_admin_print_footer_scripts() {
    $pointer_content = '<h3>TESTE</h3>';
    $pointer_content .= '<p>Added new functions to Edit Post section and few more options for users (authors and subscribers only).</p>';
?>
   <script type="text/javascript">
   //<![CDATA[
   jQuery(document).ready( function($) {
    $('#eco-options).pointer({
        content: '<?php echo $pointer_content; ?>',
        position: 'top',
        close: function() {
            // Once the close button is hit
        }
      }).pointer('open');
   });
   //]]>
   </script>
<?php


}

