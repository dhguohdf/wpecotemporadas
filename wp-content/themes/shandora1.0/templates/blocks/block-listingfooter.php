<?php 
	$prefix = bon_get_prefix();
	$agent_ids = get_post_meta($post->ID, $prefix . 'listing_agentpointed', true);
	$agent_id = '';
	$agent_fb = shandora_get_meta($post->ID, 'agentfb');
    $agent_tw = shandora_get_meta($post->ID, 'agentgplus');
    $agent_li = shandora_get_meta($post->ID,'agentlinkedin');
    if(is_array($agent_ids) && !empty($agent_ids)) {
      $agent_id = $agent_ids[0];
    } 
if( !empty($agent_id) ) {
	$agent_email = shandora_get_meta($agent_id, 'agentemail');
?>
<footer class="listing-contact row">
	<div class="column large-12">
		<div class="agent-detail row">
			

            <figure class="column large-12">
				<?php
					$img_id = shandora_get_meta($agent_id, 'agentpic');
					echo wp_get_attachment_image( $img_id, 'listing_small_box' );
				?>
			</figure>
			<div class="column large-12">
				<strong class="agent-title"><?php _e('Responsável pela temporada','bon'); ?></strong>
				<h3 class="subheader agent-name"><a href="<?php echo get_permalink($agent_id); ?>"><?php echo get_the_title($agent_id); ?></a></h3>
				<div class="agent-info">
					<strong><?php _e('Celular:','bon'); ?></strong>
					<span><?php echo shandora_get_meta( $agent_id, 'agentmobilephone'); ?></span>
				</div>
				<div class="agent-info">	
					<strong><?php _e('Telefone 1:','bon'); ?></strong>
					<span><?php echo shandora_get_meta( $agent_id, 'agentofficephone'); ?></span>
				</div>
				<div class="agent-info">			
					<strong><?php _e('Telefone 2:','bon'); ?></strong>
					<span><?php echo shandora_get_meta( $agent_id, 'agentfax'); ?></span>
				</div>
				<div class="agent-info">			
					<strong><?php _e('Email:','bon'); ?></strong>
					<span><?php echo shandora_get_meta( $agent_id, 'agentemail'); ?></span>
				</div>
			</br>
				<div class="agent-social">
					<strong><?php _e('Minhas Redes Sociais','bon'); ?></strong></br>
					<a title="<?php _e('Google Plus','bon'); ?>"href="<?php echo shandora_get_meta( $agent_tw, $prefix . 'agentgplus'); ?>" class="flat button"><i class="awe-google-plus"></i></a>
						<a title='<?php _e('Facebook','bon'); ?>' href='<?php echo shandora_get_meta( $agent_fb, $prefix . 'agentfb'); ?>' class='flat button'><i class='awe-facebook'></i></a>
						<a title="<?php _e('LinkedIn','bon'); ?>" href="<?php echo shandora_get_meta( $agent_li, $prefix . 'agentlinkedin'); ?>" class="flat button"><i class="awe-linkedin"></i></a>
					<?php
						$o = '<li><a href="'.$agent_tw.'" title="GooglePlus"><i class="awe-google-plus flat button"></i></a></li>';
						//if(!empty($agent_tw)) {$o .= '<li><a href="'.$agent_tw.'" title="GooglePlus"><i class="awe-google-plus icon"></i></a></li>';}
						//echo $o;
						?>
					</ul>			
				</div>
			</div>
			
		</div>
	</div>
	<div class="column large-12">

		<div class="row">
			<div class="column large-12">

				<form action="" method="post" id="agent-contactform">
					<h4>Entre em contato direto <br>com o proprietário!</h4>
					<div class="row collapse input-container">
						<div class="column large-2 small-1"><span class="attached-label prefix"><i class="sha-user"></i></span></div>
						<div class='column large-10 small-11'>
							<input class="attached-input required" type="text" placeholder="<?php _e('Nome','bon'); ?>"  name="name" id="name" value="" size="22" tabindex="1" />
							<div class="contact-form-error" ><?php _e('Por favor, inserir seu nome.','bon'); ?></div>
						</div>
					</div>
					<div class="row collapse input-container">
						<div class="column large-2 small-1"><span class="attached-label prefix"><i class="sha-mail-2"></i></span></div>
						<div class='column large-10 small-11'>
							<input class="attached-input required email" type="email" placeholder="<?php _e('Email','bon'); ?>"  name="email" id="email" value="" size="22" tabindex="2" />
							<div class="contact-form-error" ><?php _e('Por favor, inserir seu email.','bon'); ?></div>
						</div>
					</div>
					<div class="row collapse textarea-container input-container" data-match-height>
						<div data-height-watch class="column large-2 small-1"><span class="attached-label prefix"><i class="sha-pencil"></i></span></div>
						<div data-height-watch class='column large-10 small-11'>
							<textarea name="messages" class="attached-input required" id="messages" cols="58" rows="10" placeholder="<?php _e('Insira sua mensagem','bon'); ?>"  tabindex="4"></textarea>
							<div class="contact-form-error" ><?php _e('Por favor, digite sua mensagem.','bon'); ?></div>
						</div>
					</div>
					<div>
						<input type="hidden" name="subject" value="<?php printf(__('Ecotemporadas.com | Mensagem em anuncio %s','bon'), get_the_title()); ?>" />
						<input type="hidden" name="receiver" value="<?php echo $agent_email; ?>" />
						<input class="flat button red radius" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Enviar', 'bon') ?>" />
						<span class="contact-loader"><img src="<?php echo trailingslashit(BON_THEME_URI); ?>assets/images/loader.gif" alt="loading..." />
					</div>
					<div class="sending-result"><div class="green bon-toolkit-alert"></div></div>
				</form>
			</div>
		</div>

				<div class="related-post">
			<div class="related-header"><strong><a href="<?php echo get_permalink($agent_id); ?>"?><?php printf(__('Outras temporadas<br> de %s','bon'), get_the_title($agent_id)); ?></a></strong><a class="more-related" href="<?php echo get_permalink($agent_id); ?>" title="<?php _e('ver mais','bon'); ?>"><?php _e('ver mais','bon'); ?></a></div>
			<ul>
			<?php 
			
				$related_posts = get_posts(  array(
					'numberposts'		=>	5,
					'post_type'			=>	'listing',
					'post_status'		=>	'publish',
					'post__not_in'		=> array($post->ID),
					'meta_query'		=> array(
						array(
							'key' => $prefix . 'listing_agentpointed',
	                        'value' => serialize($agent_ids),
	                        'compare' => '=',
						)
					)
				));

				foreach($related_posts as $related_post) {
					echo '<li><a href="'.get_permalink( $related_post->ID ).'" title="'.sprintf(__('Permalink to %s','bon'), $related_post->post_title).'">'.$related_post->post_title.'</a></li>';
				}
			?>
		</ul>
		</div>
	</div>

</footer>
<?php } ?>