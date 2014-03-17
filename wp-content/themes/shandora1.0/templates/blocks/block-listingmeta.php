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
	<li class="garage"><div class="meta-wrap">
		<i class="awe-ruler awe-eco1-img"></i>
		<span class="meta-value">
			<?php if($eco1 == 'naopossui') { 
				printf('Não<br>possui','bon'); 
			} else { 
				_e('Reciclagem <br> de Lixo','bon'); 
			} ?>
		</span>
		</div>
	</li>
	<li class="garage"><div class="meta-wrap">
		<i class="awe-ruler awe-eco2-img"></i>
		<span class="meta-value">
			<?php if($eco2 == 'naopossui') { 
				printf('Não<br>possui','bon'); 
			} else { 
				_e('Certificado<br>Ecológico','bon'); 
			} ?>
		</span>
		</div>
	</li>
	<li class="garage"><div class="meta-wrap">
		<i class="awe-ruler awe-eco3-img"></i>
		<span class="meta-value">
			<?php if($eco3 == 'naopossui') { 
				printf('Não<br>possui','bon'); 
			} else { 
				_e('Atividades<br>Ecológicas','bon'); 
			} ?>
		</span>
		</div>
	</li>
</ul>