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
?>
