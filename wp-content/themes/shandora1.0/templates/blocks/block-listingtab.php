<?php
	$status = shandora_get_meta($post->ID, 'listing_status'); 
    $bed = shandora_get_meta($post->ID, 'listing_bed');
    $bath = shandora_get_meta($post->ID, 'listing_bath');
    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
    $sizemeasurement = bon_get_option('measurement');
	$buildingsize = shandora_get_meta($post->ID, 'listing_buildingsize');
    $furnish = shandora_get_meta($post->ID, 'listing_furnishing');
    $mortgage = shandora_get_meta($post->ID, 'listing_mortgage');
    $garage = shandora_get_meta($post->ID, 'listing_garage');
    $basement = shandora_get_meta($post->ID,'listing_basement');
    $date = shandora_get_meta($post->ID,'listing_dateavail');
    $totalroom = shandora_get_meta($post->ID,'listing_totalroom');
    $year = shandora_get_meta($post->ID,'listing_yearbuild');
    $floor = shandora_get_meta($post->ID,'listing_floor');
    $agent_ids = get_post_meta($post->ID,'shandora_listing_agentpointed', true);
    $address = shandora_get_meta2($post->ID, 'shandora_listing_route');
    $location = get_the_term_list( $post->ID, 'property-location', '', '/ ' );
    $type = get_the_term_list($post->ID, 'property-type');
    $mls = shandora_get_meta($post->ID, 'listing_mls');
    $zip = shandora_get_meta2($post->ID, 'shandora_listing_zip');
    $eco1 = shandora_get_meta($post->ID, 'listing_eco1');
	$eco2 = shandora_get_meta($post->ID, 'listing_eco2');
	$eco3 = shandora_get_meta($post->ID, 'listing_eco3');

    $status_opt = shandora_get_search_option('status');

    if(array_key_exists($status, $status_opt)) {
    	$status = $status_opt[$status];
    }
?>
<section>
	<nav class="tab-nav">
		<a class="active" href="#tab-target-details"><?php _e('Detalhes','bon'); ?></a>
		<a class="" href="#tab-target-features"><?php _e('Diferenciais','bon'); ?></a>
		<a class="" href="#tab-target-spec"><?php _e('Especificações','bon'); ?></a>
		<a class="" href="#tab-target-data"><?php _e('Datas','bon'); ?></a>
	</nav>
	<div class="tab-contents">
		<div id="tab-target-details" class="tab-content active">
			<?php if((!empty($mortgage))) {
				if($mortgage =='nomortgage') {
					$mortgage = __('N/A','bon');
				} else {
					$mortgage = __('Available','bon');
				}
			} else {
				$mortgage = '-';
			}?>
			<ul class="property-details">
				<li><strong><?php _e('Endereço:','bon'); ?></strong><span><?php echo (!empty($address)) ? $address  : '-'; ?></span></li>
				<li><strong><?php _e('Estado/Cidade:','bon'); ?></strong><span><?php echo (!empty($location)) ? $location : '-'; ?></span></li>
				<li><strong><?php _e('CEP:','bon'); ?></strong><span><?php echo (!empty($zip)) ? $zip  : '-'; ?></span></li>
                <li><strong><?php _e('Localização:','bon'); ?></strong><span><?php echo (!empty($status)) ? ucwords( str_replace('-',' ', $status) ) : '-'; ?></span></li>
                <li><strong><?php _e('Tipo:','bon'); ?></strong><span><?php echo (!empty($type)) ? $type : '-'; ?></span></li>
                <li><strong><?php _e('Ano construído:','bon'); ?></strong><span><?php echo (!empty($year)) ? $year : '-'; ?></span></li>
            </ul>
		</div>
		<div id="tab-target-features" class="tab-content">

			<?php

			$feature = get_the_term_list( get_the_ID(), 'property-feature', '<li>', '</li><li>', '</li>' );
			$feature = strip_tags( $feature, '<li>' );

			echo '<ul class="property-features">';
			strip_tags( 'property-feature' );
			echo $feature;
;			echo '</ul>';

			?>

		</div>
		<div id="tab-target-spec" class="tab-content">
			<ul class="property-spec">
				<li><strong><?php _e('Quartos:','bon'); ?></strong><span><?php echo (!empty($bed)) ? $bed . ' unidade(s)' : '-'; ?></span></li>
                <li><strong><?php _e('Banheiros:','bon'); ?></strong><span><?php echo (!empty($bath)) ? $bath . ' unidade(s)' : '-'; ?></span></li>
				<li><strong><?php _e('Tamanho do Lote:','bon'); ?></strong><span><?php echo (!empty($lotsize)) ? $lotsize . ' ' . $sizemeasurement : '-'; ?></span></li>
                <li><strong><?php _e('Tamanho do imóvel:','bon'); ?></strong> <span><?php echo (!empty($buildingsize)) ? $buildingsize. ' ' . $sizemeasurement : '-'; ?></span></li>
                <li><strong><?php _e('Garagens:','bon'); ?></strong><span><?php echo (!empty($basement)) ? $basement . ' unidade(s)' : '-'; ?></span></li>
                <li><strong><?php _e('Andares:','bon'); ?></strong><span><?php echo (!empty($floor)) ? $floor . ' unidade(s)' : '-'; ?></span></li>
                <li><strong><?php _e('Total de cômodos:','bon'); ?></strong><span><?php echo (!empty($totalroom)) ? $totalroom . ' unidade(s)' : '-'; ?></span></li>
                <li><strong><?php _e('Data Disponível:','bon'); ?></strong><span><?php echo (!empty($date)) ? $date : '-'; ?></span></li>
			</ul>
		</div>
		<div id="tab-target-data" class="tab-content">
			<ul class="property-details">
				<?php //calendario no front-end ?>
				<?php global $post; ?>
				<?php reserva_wp_listing_calendar_render_front($post); ?>
				<?php //add_action('dynamic_sidebar_before', reserva_wp_listing_calendar_render($post)); ?>
			</ul>
		</div>
	</div>
</section>