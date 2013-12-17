<?php 

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



/**
 * Trocando mensagem ao salvar o {listing}.
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
	1 => sprintf( __($print . ' <a href="%s">Ver anúncio.</a>'), esc_url( get_permalink($post_ID) ) ),
	6 => sprintf( __($print . ' <a href="%s">Ver anúncio.</a>'), esc_url( get_permalink($post_ID) ) ),
	);
	return $messages;
}

add_filter('post_updated_messages', 'admin_messages' );

add_filter('wp_handle_upload_prefilter', 'limite_upload');
function limite_upload( $file ) {
	global $post, $post_ID;
	$post_type = get_post_type( $post_ID );
	if ( $post_type == 'listing' ) {
	
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
	  if ('image'!=$category || !in_array($type,array('jpg','jpeg','gif','png'))) {
		$file['error'] = "Sorry, you can only upload a .GIF, a .JPG, or a .PNG image file.";
	  } else if ($post_id = (isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false)) {
		if (count(get_posts("post_type=attachment&post_parent={$post_id}"))>2)
		  $file['error'] = "Sorry, you cannot upload more than one (3) image.";
	  }
	} 
  return $file;
}

?>
