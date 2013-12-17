<?php 

//Modo de manutenção
function maintenace_mode() {
  if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
    die('<div style="text-align: center;"><h1>Em manutenção</h1><p>Por favor volte em 30 minutos.</p><p>Enquanto isso, acesse o site da <a href="http://www.eaxdesign.com.br/">Eax Design</a>.</p></div>');
  }
}
// Comente a seguinte linha para sair no "Modo de manutenção"
//add_action('get_header', 'maintenace_mode');

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


// Customizing Login

function mapadosplanos_login_stylesheet() { 
	?>
    <link rel="stylesheet" id="custom_wp_admin_css"  href="<?php echo get_bloginfo( 'stylesheet_directory' ) . '/style-login.css'; ?>" type="text/css" media="all" />
	<?php 
}
add_action( 'login_enqueue_scripts', 'mapadosplanos_login_stylesheet' );

function mapadosplanos_login_footer() { 
	?>
	<script type='text/javascript' src='<?php echo get_bloginfo( 'stylesheet_directory' ) . '/js/wp-login.js'; ?>'></script>

	<?php
}

	add_filter('login_footer', 'mapadosplanos_login_footer');




/**
 * Trocando mensagem ao salvar o {post}.
 */
function admin_messages($messages) {
	global $post, $post_ID;
	$post_type = get_post_type( $post_ID );
	if ( $post_type == 'listing' && current_user_can('delete_posts')) {
		$print = "Seu anúncio foi criado com sucesso. Aguarde para revisão.";
	} else {
		$print = "Post Publicado Normalmente!";
	}

	$obj = get_post_type_object($post_type);
	$singular = $obj->labels->singular_name;

	$messages[$post_type] = array(
	1 => sprintf( __($print . ' <a href="%s">Ver anúncio.</a>'), esc_url( get_permalink($post_ID) ) ), //Salvar/Atualizar
	6 => sprintf( __($print . ' <a href="%s">Ver anúncio.</a>'), esc_url( get_permalink($post_ID) ) ), //Publish
	10 => sprintf( __($print . ' <a href="%s">Ver anúncio.</a>'), esc_url( get_permalink($post_ID) ) ), //Salvar como Rascunho
	);
	return $messages;
}

add_filter('post_updated_messages', 'admin_messages' );


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
?>
