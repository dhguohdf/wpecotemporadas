<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* Start BonThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// BonFramework init
//require_once ( get_template_directory() . '/framework/bon.php' );


/*				
foreach ( $includes as $i ) {
	locate_template( $i, true );
}
*/


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
//add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_js' );

function set_default_admin_color($user_id) {
	$args = array(
		'ID' => $user_id,
		'admin_color' => 'flat'
	);
	wp_update_user( $args );
}
add_action('user_register', 'set_default_admin_color');



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


// example custom dashboard widget
function custom_dashboard_widget() {
	include("custom-dashboard.php");
}
function add_custom_dashboard_widget() {
	wp_add_dashboard_widget('custom_dashboard_widget', '<h1>Bem vindo ao painel de suas temporadas!</h1>', 'custom_dashboard_widget');
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

/**
 * Adicionando limite de uploads no post type listing
 */
function limite_upload($file) {
        global $post, $post_ID;
        $post_type = get_post_type( $post_ID );
        
  if ($file['type']=='application/octet-stream' && isset($file['tmp_name'])) {
    $file_size = getimagesize($file['tmp_name']);
    if (isset($file_size['error']) && $file_size['error']!=0) {
      $file['error'] = "Unexpected Error: {$file_size['error']}";
      return $file;
    } else {
      $file['type'] = $file_size['mime'];
    }
  }
  list($category,$type) = explode('/',$file['type']);

  if ('image'!=$category || !in_array($type,array('jpg','jpeg','gif','png')) && $post_type == "listing" ) {
    $file['error'] = "Desculpe você só pode fazer upload de arquivos .GIF, .JP ou .PNG.";
  } else if ($post_id = (isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false)) {

                if ( count( get_posts( "post_type=attachment&post_parent={$post_id}" ) ) > 19 )
                        $file['error'] = "Você atingiu seu limite de imagens (20).";
                }
  return $file;
}

add_filter('wp_handle_upload_prefilter', 'limite_upload');

// Mensagem padrão nos post types

add_filter( 'default_content', 'conteudo_editor', 10, 2 );

function conteudo_editor( $content, $post ) {

    switch( $post->post_type ) {
        case 'listing':
            $content = '<h1>Título de Mensagem</h1><br /> Mensagem default para anúncio!';
        break;
        case 'agent':
            $content = 'Mensagem default para Perfil Eco';
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
    echo '<div style="width:100%"><img src="https://trello-attachments.s3.amazonaws.com/525813762c0bfe3c1300254f/52e11e1c74fbf8652458b47c/47768b956006948401b2cff079902b8c/BARRA_DO_CLIENTE.png" width="100%" /></div>';
}


?>