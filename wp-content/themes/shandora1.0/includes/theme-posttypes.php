<?php
/**
 * ======================================================================================================
 *
 * Check custom post type supports
 * This function check if theme supports specific custom post type or not if supported
 * register required custom post type
 *
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */


add_action('init', 'shandora_setup_listing_post_type', 1);
add_action('init', 'shandora_setup_agent_post_type', 1);



add_action( 'after_setup_theme', 'shandora_add_car_listing',2 );

function shandora_add_car_listing(){
	if(bon_get_option('enable_car_listing') == 'yes') {
		add_action('init', 'shandora_setup_car_dealer_post_type', 1);
		add_action('init', 'shandora_setup_sales_rep_post_type', 1);
	}
}

if( !function_exists('shandora_setup_listing_post_type') ) {

	function shandora_setup_listing_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$suffix = 'listing_';

		$cpt = $bon->cpt();

		//$cpt->create('listing', array( 'rewrite' => array(
		//		'slug'       => _x('anuncio', 'URL Slug', 'bon'),
		//		'with_front' => false,
		//		'pages'      => true,
		//		'feeds'      => true,
		//		'ep_mask'    => EP_PERMALINK,
		//	), 'labels' => array(
		//		'name' => __( 'Anúncios' ),
		//		'singular_name' => __( 'Anúncio' )
		//	), 'supports' => array('editor','title', 'thumbnail','comments'), 'menu_position' => 6 ));
		$cpt->create('listing', 'Anúncio', array('supports' => array('editor','title', 'excerpt', 'thumbnail','comments' ), 'menu_position' => 6));

		$gallery_opts = array(
			array( 

				'label'	=> __('Galeria de imagens:', 'bon'),
				'desc'	=> __('Clique no botão ao lado e dê upload em suas imagens. Máximo de 20.', 'bon'), 
				'id'	=> $prefix . $suffix . 'gallery',
				'type'	=> 'gallery',
			),

		);

		$gallery_opts2 = array(
			array( 

				'label'	=> __('Galeria de imagens:', 'bon'),
				'desc'	=> __('Clique no botão ao lado e dê upload em suas imagens. Máximo de 20.', 'bon'), 
				'id'	=> $prefix . $suffix . 'gallery',
				'type'	=> 'gallery',
			),

		);

		$promo = array(
			array( 

				'label'	=> __('Código Promocional', 'bon'),
				'desc'	=> __('Se você possui um código promocional, digite-o aqui', 'bon'), 
				'id'	=> $prefix . $suffix .'promo',
				'type'	=> 'text',
				
			),
		);

		$eco_options = array(
			array( 

				'label'	=> __('Escolha aqui o <br> Perfil ECO responsável', 'bon'),
				'desc'	=> __('', 'bon'), 
				'id'	=> $prefix . $suffix .'agentpointed',
				'type'	=> 'post_select',
				'filter_author' => true, 
				'post_type' => 'agent',				
			),
		);

		$ecoseals_opt = array(
			array( 

				'label'	=> __('<br>Reciclagem de Lixo:', 'bon'),
				'desc'	=> __('Sua propriedade possui acesso à coleta seletiva?', 'bon'), 
				'id'	=> $prefix . $suffix .'eco1',
				'type'	=> 'select',
				'options' => shandora_get_search_option('eco1')
				
			),

			array( 

				'label'	=> __('Certificado Ambiental:', 'bon'),
				'desc'	=> __('', 'bon'), 
				'id'	=> $prefix . $suffix .'eco2',
				'type'	=> 'select',
				'options' => shandora_get_search_option('eco2')
				
			),

			array( 

				'label'	=> __('Proximidade à<br>atividades ecológicas:', 'bon'),
				'desc'	=> __('', 'bon'), 
				'id'	=> $prefix . $suffix .'eco3',
				'type'	=> 'select',
				'options' => shandora_get_search_option('eco3')
				
			),

		);

		$prop_options = array(

			array( 
				'label'	=> __('Localização:', 'bon'),
				'desc'	=> __('', 'bon'), 
				'id'	=> $prefix . $suffix . 'status',
				'type'	=> 'select',
				'options' => shandora_get_search_option()
			),

			array(

				'label'	=> __('Endereço do imóvel:', 'bon'),
				'id'	=> $prefix . $suffix .'address',
				'type'	=> 'text',

			),

			array(
				'label'	=> __('CEP:', 'bon'), 
				'id'	=> $prefix . $suffix .'zip',
				'type'	=> 'text',
			),

			array(

				'label'	=> __('Valor Principal:', 'bon'),
				'desc'	=> __('Escolha seu valor principal, não esqueça que você pode definir outros valores no seu calendário do anúncio. Por favor, preencha com apenas números, ex: 123456', 'bon'), 
				'id'	=> $prefix . $suffix .'price',
				'class'	=> $prefix . $suffix .'price',
				'type'	=> 'text',

			),

			array( 
				'label'	=> __('Período Principal:', 'bon'),
				'desc'	=> __('Escolha o tipo de período que o imóvel será exibido', 'bon'), 
				'id'	=> $prefix . $suffix . 'period',
				'class'	=> $prefix . $suffix . 'period',
				'type'	=> 'select',
				'options' => shandora_get_search_option('period'),
			),

			array( 

				'label'	=> __('Quartos:', 'bon'),
				'id'	=> $prefix . $suffix .'bed',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Banheiros:', 'bon'),
				'id'	=> $prefix . $suffix .'bath',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Garagens:', 'bon'),
				'id'	=> $prefix . $suffix .'basement',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Andares:', 'bon'), 
				'id'	=> $prefix . $suffix .'floor',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Total de cômodos:', 'bon'),
				'id'	=> $prefix . $suffix .'totalroom',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Tamanho do Lote:', 'bon'),
				'id'	=> $prefix . $suffix .'lotsize',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Tamanho da área construída:', 'bon'),
				'id'	=> $prefix . $suffix .'buildingsize',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Mobília:', 'bon'),
				'id'	=> $prefix . $suffix .'furnishing',
				'type'	=> 'select',
				'options' => shandora_get_search_option('furnishing')
			),

			array( 

				'label'	=> __('Data de disponibilidade', 'bon'),
				'desc'	=> __('Quando a propriedade estará disponível?', 'bon'), 
				'id'	=> $prefix . $suffix .'dateavail',
				'type'	=> 'date',
				
			),

			array( 

				'label'	=> __('Ano construído', 'bon'),
				'desc'	=> __('Quando sua propriedade foi construída, útil em propriedades históricas ou tombadas pelo governo.', 'bon'), 
				'id'	=> $prefix . $suffix .'yearbuild',
				'type'	=> 'text',
				
			),
			
			array( 

				'label'	=> __('Ferramenta', 'bon'),
				'desc'	=> __('Funciona plz', 'bon'), 
				'id'	=> $prefix . $suffix .'iframe',
				'type'	=> 'iframe',
				
			),

			array( 

				'label'	=> __('Latitude do local:', 'bon'),
				'desc'	=> __('Latitude do local, utilizada para a localização de seu imóvel no mapa. Encontre sua longitude no site <a href="http://translate.google.com.br/translate?sl=en&tl=pt&js=n&prev=_t&hl=pt-BR&ie=UTF-8&u=http%3A%2F%2Fwww.latlong.net%2F&act=url" target="_blank">aqui</a>. Copie e cole aqui sua longitude', 'bon'), 
				'id'	=> $prefix . $suffix .'maplatitude',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Longitude do local:', 'bon'),
				'desc'	=> __('Longitude do local, utilizada para a localização de seu imóvel no mapa. Encontre sua longitude no site <a href="http://translate.google.com.br/translate?sl=en&tl=pt&js=n&prev=_t&hl=pt-BR&ie=UTF-8&u=http%3A%2F%2Fwww.latlong.net%2F&act=url" target="_blank">aqui</a>. Copie e cole aqui sua longitude', 'bon'), 
				'id'	=> $prefix . $suffix .'maplongitude',
				'type'	=> 'text',
				
			),

			array( 

				'label'	=> __('Imóvel em Destaque', 'bon'),
				'class' => $prefix . $suffix .'featured',
				'id'	=> $prefix . $suffix .'featured',
				'type'	=> 'checkbox',
				
			),
			
		);
	

		$cpt->add_taxonomy("Property Type", array('hierarchical' => true, 'label' => __('Tipo do Local','bon'), 'labels' => array(
			'menu_name' => __('Tipo','bon') ) ) );

		$cpt->add_taxonomy("Property Location", array('hierarchical' => true, 'label' => __('Estado e Cidade','bon'), 'labels' => array(
			'parent_item'       => __( 'Escolha o Estado' ),
			'add_new_item'               => __( 'Adicionar sua cidade' ),
			'menu_name' => __('Local','bon') ) ) );

		$cpt->add_taxonomy("Property Feature", array( 'label' => __('Diferenciais do Anúncio','bon'), 'labels' => array(
			'separate_items_with_commas' => __( 'Escreva os diferenciais do anúncio e clique em adicionar!' ),
			'choose_from_most_used'      => __( 'Escolha entre os destaques mais utilizados' ),
			'menu_name' => __('Features','bon') ) ) );

		$cpt->add_meta_box(   
		    'gallery-options',
		    'Suas fotos do Anúncio',
		    $gallery_opts
		);

		$cpt->add_meta_box(   
		    'promo',
		    'Promoção ecotemporadas.com',
		    $promo,
		    'normal',
		    'low'
		);

		$cpt->add_meta_box(   
		    'eco-options',
		    'Perfil ECO',
		    $eco_options,
		    'side',
		    'high',
		    '0'
		);

		$cpt->add_meta_box(   
		    'ecoseals_opt',
		    'Certificados Ecológicos',
		    $ecoseals_opt
		);

		$cpt->add_meta_box(   
		    'property-options',
		    'Detalhes do seu Anúncio',
		    $prop_options  
		);

	}

}


if( !function_exists('shandora_setup_agent_post_type') ) {

	function shandora_setup_agent_post_type() {
		global $bon;

		$prefix = bon_get_prefix();

		$cpt = $bon->cpt();

		//$cpt->create('agent', array( 'rewrite' => array(
		//		'slug'       => _x('perfileco', 'URL Slug', 'bon'),
		//		'with_front' => false,
		//		'pages'      => true,
		///		'feeds'      => true,
			//	'ep_mask'    => EP_PERMALINK,
		//	), 'labels' => array(
		//		'name' => __( 'Perfis ECO' ),
		//		'singular_name' => __( 'Perfil ECO' )
		//	), 'supports' => array('editor', 'title', 'comments'), 'menu_position' => 7 ));

		$cpt->create('agent', 'Perfil ECO', array('supports' => array('editor', 'title', 'comments', ) , 'menu_position' => 7 ));

		$agent_opt1 = array(


			array( 
				'label'	=> __('Foto do Perfil', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentpic',
				'type'	=> 'image',
			),

			array( 
				'label'	=> __('Link do Facebook', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfb',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Link do Google Plus', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentgplus',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Link do LinkedIn', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentlinkedin',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Endereço de email', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentemail',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Celular', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentmobilephone',
				'type'	=> 'text',
			),


			array( 
				'label'	=> __('Telefone 1', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentofficephone',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Telefone 2', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentfax',
				'type'	=> 'text',
			),

			array( 
				'label'	=> __('Website', 'bon'),
				'desc'	=> '', 
				'id'	=> $prefix.'agentsite',
				'type'	=> 'text',
			),

			
			
		);


		$cpt->add_meta_box(   
		    'agent-options',
		    'Opções do Perfil ECO',
		    $agent_opt1  
		);

	
	}

}


add_action( 'init', 'shandora_page_meta');
if( !function_exists('shandora_page_meta') ) {

	function shandora_page_meta() {
		if(is_admin()) {
			global $bon;

			$mb = $bon->mb();

			$fields = array(
				array(
					'id' => 'shandora_status_query',
					'type' => 'select',
					'label' => __('Property Status to Query','bon'),
					'options' => shandora_get_search_option('status')
				)
			);

			$mb->create_box('status-opt', __('Property Status', 'bon'), $fields, array('page'));
		}
	}

}

?>