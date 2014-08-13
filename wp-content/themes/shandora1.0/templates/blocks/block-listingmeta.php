<?php
	$status = shandora_get_meta($post->ID, 'listing_status'); 
    $bed = shandora_get_meta($post->ID, 'listing_bed');
    $bath = shandora_get_meta($post->ID, 'listing_bath');
    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
    $sizemeasurement = bon_get_option('measurement');
	$furnish = shandora_get_meta($post->ID, 'listing_furnishing');
	$eco1 = shandora_get_meta($post->ID, 'listing_eco1');
	$eco2 = shandora_get_meta($post->ID, 'listing_eco2');
	$eco3 = shandora_get_meta($post->ID, 'listing_eco3');
	$rooms = shandora_get_meta($post->ID, 'listing_totalroom');
?>

<ul class="large-custom-grid-6 small-custom-grid-3">
	<li class="bed"><div class="meta-wrap">
		<i class="sha-bed"></i>
		<span class="meta-value">
			<?php if(!empty($bed)) {
				($bed > 0) ? printf(_n( 'Quantidade:<br>Um Quarto', 'Quantidade:<br>%s Quartos', $bed, 'bon' ), $bed) : _e('Sem Quartos','bon'); 
			} else { 
				_e('Não<br>possui','bon'); 
			} ?>
		</span></div>
	</li>
	<li class="bath"><div class="meta-wrap">
		<i class="sha-bath"></i>
		<span class="meta-value">
			<?php if(!empty($bath)) { 
				($bath > 0) ? printf(_n( 'Quantidade:<br>Um Banheiro', 'Quantidade:<br>%s Banheiros ', $bath, 'bon' ), $bath) : _e('Sem Banheiros','bon'); 
			} else { 
				_e('Não<br>possui','bon'); 
			} ?>
		</span></div>
	</li>
	<li class="lotsize"><div class="meta-wrap">
		<i class="sha-ruler-2"></i>
		<span class="meta-value">
			<?php if($lotsize) { echo 'Metragem:<br>' . $lotsize . ' ' . strtolower($sizemeasurement); } else { _e('&nbsp<br>-','bon'); } ?>
		</span></div>
	</li>
	<li class="garage eco-reci"><div class="meta-wrap">
		<div class="awe-ruler awe-eco1-img" data-toggle="tooltip" data-placement="top" title="Quando seu imóvel incentiva a reciclagem e possui um sistema de coleta seletiva separando o lixo reciclável do lixo orgânico." id="tooltip1"></div>
		<span class="meta-value">
			<?php if($eco1 == 'naopossui') { 
				echo '<style>li.garage.eco-reci {display:none};</style>'; }
			else { 
				_e('Reciclagem <br> de Lixo','bon'); 
			} ?>
		</span>
		</div>
	</li>
	<li class="garage eco-cert"><div class="meta-wrap">
		<div class="awe-ruler awe-eco2-img" data-toggle="tooltip" data-placement="top" title="Quando seu imóvel possui uma qualidade ecológica ou evita danificar o meio ambiente reaproveitando materiais ou recursos." id="tooltip2"></div>
		<span class="meta-value">
			<?php if($eco2 == 'naopossui') { 
				echo '<style>li.garage.eco-cert {display:none};</style>'; 
			} else { 
				_e('Certificado<br>Ecológico','bon'); 
			} ?>
		</span>
		</div>
	</li>
	<li class="garage eco-ativ"><div class="meta-wrap">
		<div class="awe-ruler awe-eco3-img" data-toggle="tooltip" data-placement="top" title="Quando o imóvel está dentro ou nas proximidades da natureza: reservas ecológicas, parques, área de proteção ambiental…etc." id="tooltip3"></div>
		<span class="meta-value">
			<?php if($eco3 == 'naopossui') { 
				echo '<style>li.garage.eco-ativ {display:none};</style>'; 
			} else { 
				_e('Atividades<br>Ecológicas'); 
			} ?>
		</span>
		</div>
	</li>
</ul>
<div id="bookingdatepicker></div>