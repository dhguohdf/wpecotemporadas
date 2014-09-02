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


function eco_status_aprovado_expirando(){
	register_post_status( 'expirando', array(
		'label'                     => _x( 'Expirando', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Expirando <span class="count">(%s)</span>', 'Expirando <span class="count">(%s)</span>' ),
	) );
}
add_action( 'init', 'eco_status_aprovado_expirando' );


	add_action('admin_footer-post.php', 'eco_append_post_status_list');
	function eco_append_post_status_list(){
	     global $post;
	     $complete = '';
	     $label = '';
	     if($post->post_type == 'post'){
	          if($post->post_status == 'expirando'){
	               $complete = ' selected="selected"';
	               $label = '<span id="post-status-display"> Expirando</span>';
	          }
	          echo '
	          <script>
	          jQuery(document).ready(function($){
	               $("select#post_status").append("<option value="archive" '.$complete.'>Expirando</option>");
	               $(".misc-pub-section label").append("'.$label.'");
	          });
	          </script>
	          ';
	     }
	}
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



//add_action( 'after_setup_theme', 'shandora_admin_init');
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

//add_action('admin_init', 'shandora_updater');

/*-----------------------------------------------------------------------------------*/
/* You can add 
 functions below */
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

function test_modify_post_table( $columns ) {
	return array_merge($columns, 
        array('pagamento' => __('Formas de Pagamento')));
}
add_filter( 'manage_listing_posts_columns', 'test_modify_post_table' );

function test_modify_post_table_row( $column_name, $post_id ) {
	if (get_post_status ( $ID ) == 'private' or get_post_status ( $ID ) == 'expirando') {
    switch ($column_name) {
        case 'pagamento' :
		//$EmailVendedor = 'contato@matheusgimenez.com.br';
		$EmailVendedor = 'pagamentos@ecotemporadas.com';
		$identificacao = get_the_ID();
		$p = get_post($identificacao);
		$comprador = $p->post_author;
		global $current_user;
      	get_currentuserinfo();

      	if(function_exists('reserva_wp_busca_ultima_transacao')) {
			$rwp_transacao = reserva_wp_busca_ultima_transacao($current_user->ID, $post_id);
			$identificacao .= '-'.$rwp_transacao;
		}

      	echo '<form><spam></spam></form>';
        echo '<form action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" onsubmit="PagSeguroLightbox(this); return false;">

			<input type="hidden" name="code" value="81045E8D0399448E970C11732E435C2D" />
			<input type="hidden" name="email_cobranca" value="'.$EmailVendedor.'">
			<input type="hidden" name="tipo" value="CP" />
			<input type="hidden" name="moeda" value="BRL" />
			<input type="hidden" name="ref_transacao" value="'.$post_id.'" />
			<input type="hidden" name="cliente_nome" value="'.$current_user->user_firstname.' '.$current_user->user_lastname.'" />
			<input type="hidden" name="cliente_end" value="'.$current_user->addr1.'" />
			<input type="hidden" name="cliente_num" value="'.$current_user->numaddr.'" />
			<input type="hidden" name="cliente_compl" value="'.$current_user->addr2.'" />
			<input type="hidden" name="cliente_bairro" value="'.$current_user->bairro.'" />
			<input type="hidden" name="cliente_cidade" value="'.$current_user->city.'" />
			<input type="hidden" name="cliente_uf" value="'.$current_user->uf.'" />
			<input type="hidden" name="cliente_pais" value="'.$current_user->country.'" />
			<input type="hidden" name="cliente_ddd" value="'.$current_user->ddd.'" />
			<input type="hidden" name="reference" value="'.$post_id.'" />
			<input type="hidden" name="item_id_1" value="2" />
			<input type="hidden" name="item_descr_1" value="12 meses" />
			<input type="hidden" name="item_quant_1" value="1" />
			<input type="hidden" name="item_valor_1" value="120,00" />
			<input type="hidden" name="item_frete_1" value="0" />

			<input type="hidden" name="cliente_tel" value="'.$current_user->phone1.'" />
			<input type="image" src="http://ecotemporadas.com/wp-content/uploads/eco-botao_pagamento1_eax2.png" name="submit" alt="Pague com PagSeguro - é rápido, grátis e seguro!" />
			</form>
			<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>';
            break;
 
        default:
    } }
}
 
// add_filter( 'manage_posts_custom_column', 'test_modify_post_table_row', 10, 2 );
add_action( 'manage_listing_posts_custom_column', 'test_modify_post_table_row', 10, 2 );



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

//removes quick edit from custom post type list
function remove_quick_edit( $actions ) {
	global $post;
    if( $post->post_type == 'listing' || 'agent' ) {
		unset($actions['inline hide-if-no-js']);
	}
    return $actions;
}


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
        wp_register_script( 'my_custom_script', get_template_directory_uri() . '/custom3.js');
        wp_enqueue_script( 'my_custom_script' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_js' );

function eco_scripts() {
wp_enqueue_script( 'ecotempo-mask-js', get_stylesheet_directory_uri() . '/jquery.mask.min.js', array(), '1.0.0', true );
wp_enqueue_script( 'ecotempo-bootstrap-js', get_stylesheet_directory_uri() . '/bootstrap.min.js', array(), '1.0.0', true );
}

add_action( 'wp_enqueue_scripts', 'eco_scripts' );
add_action( 'admin_enqueue_scripts', 'eco_scripts' );

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
1 => sprintf( __($singular.' foi alterado! <a href="%s">Veja o seu anúncio alterado aqui!</a>'), esc_url( get_permalink($post_ID) ) ),
2 => __('Custom field updated.'),
3 => __('Custom field deleted.'),
4 => __($singular.' atualizado!.'),
5 => isset($_GET['revision']) ? sprintf( __($singular.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
6 => sprintf( __('<div class="warning-message">Parabéns! Você criou seu Perfil ECO!</br></br>Agora comece a criar seu anúncio <a href="http://ecotemporadas.com/wp-admin/post-new.php?post_type=listing" class="warning-red">clicando aqui.</a></br>Não esqueça de vincular o seu Perfil ECO que você acabou de criar!</div>'), esc_url( get_permalink($post_ID) ) ),
7 => __('Page saved.'),
8 => sprintf( '<div class="warning-message">Parabéns! '. __($singular.' enviado para revisão! <a target="_blank" href="%s" class="warning-red">Clique aqui para ver como ficou.</a><br> Aguarde no máximo 3 horas para revisão de seu anúncio e liberação para pagamento/publicação!<br><br><strong>Não se preocupe, você receberá um email! :) </strong></div>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
9 => sprintf( __($singular.' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.strtolower($singular).'</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
10 => sprintf( __($singular.': rascunho atualizado. <a target="_blank" href="%s"> Verifique como seria publicado clicando aqui.</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
);
return $messages;
}

add_filter('post_updated_messages', 'set_messages' );


// example custom dashboard widget
function custom_dashboard_widget() {
	include("custom-dashboard.php");
}
function add_custom_dashboard_widget() {
	wp_add_dashboard_widget('custom_dashboard_widget', 'Vai uma ajudinha aí? Tire suas dúvidas abaixo!', 'custom_dashboard_widget');
}
add_action('wp_dashboard_setup', 'add_custom_dashboard_widget');

function so_screen_layout_columns( $columns ) {
    $columns['dashboard'] = 1;
    return $columns;
}
add_filter( 'screen_layout_columns', 'so_screen_layout_columns' );

function so_screen_layout_dashboard() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_dashboard', 'so_screen_layout_dashboard' );

// Mensagem padrão nos post types

add_filter( 'default_content', 'conteudo_editor', 10, 2 );

function conteudo_editor( $content, $post ) {

    switch( $post->post_type ) {
        case 'listing':
            $content = '<h5>Insira uma descrição  do seu anúncio.</h5>';
        break;
        case 'agent':
            $content = 'Deixe uma mensagem falando sobre você!<br> Faça seus clientes confiarem em seus anúncios dando mais credibilidade!';
        break;

        default:
            $content = 'Esse e o conteudo default para todos os contents que não forem Perfis Ecos e nem Anúncios';
        break;
    }

    return $content;
}

// Customizing Admin

add_action( 'admin_enqueue_scripts', 'ecotemporadas_admin_stylesheet' );

function ecotemporadas_admin_stylesheet() { 
        wp_enqueue_style('ecotemporadas_admin_css', get_bloginfo( 'stylesheet_directory' ) . '/style-admin.css');
}

// Customizing Login

function ecotemporadas_login_stylesheet() { 
        ?>
    <link rel="stylesheet" id="custom_wp_admin_css"  href="<?php echo get_bloginfo( 'stylesheet_directory' ) . '/style-login.css'; ?>" type="text/css" media="all" />
        <?php 
}
add_action( 'login_enqueue_scripts', 'ecotemporadas_login_stylesheet' );


add_action( 'in_admin_header', 'insert_header_wpse_51023' );

function insert_header_wpse_51023()
{
    echo '<div style="width:63%"><img src="http://ecotemporadas.com/wp-content/themes/shandora1.0/assets/theme/eco-barra_cliente_eax1.png" width="100%" /></div>';
}


add_filter( 'wpmem_register_form', 'my_submit_button_filter' );
function my_submit_button_filter( $string ) {

	// the parameter $string is the 
	// generated html of the form

	// use str_replace like:
	// $new_string = str_replace( $needle, $replacement, $haystack );
	
	global $wpmem_a;
	
	$new_text = ( $wpmem_a == 'edit' ) ? 'Update Profile' : 'Registrar!';
	
	$string = str_replace( 'Submit', $new_text, $string );
	
	return $string;

}

// Move Yoast to bottom
function yoasttobottom() {
	return 'low';
}


add_filter( 'wpseo_metabox_prio', 'yoasttobottom');

/*SUBSUB SUB UPDATE WITH TEH POSTS*/
function jquery_remove_counts()
{
	?>
	<script type="text/javascript">
	jQuery(function(){
		jQuery("li.all").remove();
		jQuery("li.publish").find("span.count").remove();
		jQuery("li.trash").find("span.count").remove();
		jQuery("li.draft").find("span.count").remove();
		jQuery("li.pending").find("span.count").remove();
		jQuery("li.private").find("span.count").remove();
	});
	</script>
	<?php
}
add_action('admin_footer', 'jquery_remove_counts');

function custom_post_states( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     if($arg != 'post'){
          if($post->post_status == 'private'){
               return array('<br><h3>Renove seu anúncio</h3>');
          }
          if($post->post_status == 'draft'){
               return array('<br><h3>Salve para enviar para revisão</h3>');
          }
          if($post->post_status == 'expirando'){
               return array('<br><h3>O anúncio está perto de expirar</h3>');
          }
          if($post->post_status == 'pending'){
               return array('<br><h3>Enviaremos o link de<br> pagamento em seu email</h3>');
          }
     }
    return $states;
}
add_filter( 'display_post_states', 'custom_post_states' );
if ( is_singular( 'book' ) ) {
    // conditional content/code
}

add_action('do_meta_boxes', 'move_publish_metabox');
function move_publish_metabox(){
    remove_meta_box( 'submitdiv', 'listing', 'side' ); //check the name, I'm doing this from memory
    remove_meta_box( 'submitdiv', 'agent', 'side' ); //check the name, I'm doing this from memory
    add_meta_box('submitdiv', 'Publique seu anúncio', 'post_submit_meta_box', 'listing', 'normal', 'low');
    add_meta_box('submitdiv', 'Crie seu Perfil Eco', 'post_submit_meta_box', 'agent', 'normal', 'low');
}


function fb_remove_postbox() {
    wp_deregister_script('postbox');
}
//add_action( 'admin_init', 'fb_remove_postbox' );

function set_user_cookie() {
    if (!isset($_COOKIE['popover_never_view'])) {
		setcookie("popover_never_view", "hidealways", time()+3600);  /* expire in 1 hour */
    }
}
add_action( 'init', 'set_user_cookie');

function post_expire_notification( $id ){
	wp_mail( get_option('admin_email'), '[ecotemporadas.com] O anúncio ' . $id . ' expirou', 'O anúncio ' . $id . ' expirou. <p>Email the author: <a href="mailto:'. get_the_author_meta('user_email') .'">'.the_author_meta('display_name').'</a></p>') ;
}

add_action( 'postExpiratorExpire', 'post_expire_notification' );

