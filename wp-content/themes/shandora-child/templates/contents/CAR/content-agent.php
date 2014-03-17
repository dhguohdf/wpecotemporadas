<?php 
	$status = shandora_get_meta($post->ID, 'listing_status'); 
    $bed = shandora_get_meta($post->ID, 'listing_bed');
    $bath = shandora_get_meta($post->ID, 'listing_bath');
    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
    $sizemeasurement = bon_get_option('measurement');
    $agent_pic = shandora_get_meta($post->ID, 'agentpic');
    $agent_fb = shandora_get_meta($post->ID, 'agentfb');
    $agent_tw = shandora_get_meta($post->ID, 'agentgplus');
    $agent_li = shandora_get_meta($post->ID,'agentlinkedin');
    $agent_mobile = shandora_get_meta($post->ID,'agentmobilephone');
    $agent_office = shandora_get_meta( $post->ID, 'agentofficephone');
    $agent_fax = shandora_get_meta($post->ID,'agentfax');
    $agent_email = shandora_get_meta($post->ID, 'agentemail');
    $agent_site = shandora_get_meta($post->ID, 'agentesite');
if( is_singular( get_post_type() ) ) { 


?>
<article id="post-<?php the_ID(); ?>" class="<?php bon_entry_class($status); ?>" itemscope itemtype="http://schema.org/RealEstateAgent">
	<header class="entry-header clear">
		<?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title" itemprop="name">', '</h1>', false ) ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content clear" itemprop="description">
		<div class="row">
		<?php 
		echo '<div class="column large-4 small-4">' . wp_get_attachment_image( $agent_pic, 'listing_small_box' ) . '</div>';
		?>
			<div class="column large-8 small-8">
				
				<div class="row">
					<div class="contact-info column large-6">
						<strong><?php _e('Celular','bon'); ?>: </strong><?php echo $agent_mobile; ?><br/>
						<strong><?php _e('Telefone 1','bon'); ?>: </strong><?php echo $agent_office; ?><br/>
						<strong><?php _e('Telefone 2','bon'); ?>: </strong><?php echo $agent_fax; ?><br/>
						<strong><?php _e('Website','bon'); ?>: </strong><?php echo $agent_site; ?><br/>
					</div>

					<div class="social-media column large-6">
						<strong><?php _e('Seguir no','bon'); ?>: </strong>
						<a title="<?php _e('Google Plus','bon'); ?>"href="<?php echo $agent_tw; ?>" class="flat round button small"><i class="awe-google-plus"></i></a>
						<a title="<?php _e('Facebook','bon'); ?>" href="<?php echo $agent_fb; ?>" class="flat round button small"><i class="awe-facebook"></i></a>
						<a title="<?php _e('LinkedIn','bon'); ?>" href="<?php echo $agent_li; ?>" class="flat round button small"><i class="awe-linkedin"></i></a>
					</div>
				</div>
				<hr />
				<?php the_content(); ?>
			</div>
		
		</div>
	<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
	</div><!-- .entry-content -->

	<div class="listing-contact row">
		<div class="column large-12">
			<form action="" method="post" id="agent-contactform">
				<div class="row collapse input-container">
					<div class="column large-1 small-1"><span class="attached-label prefix"><i class="sha-user"></i></span></div>
					<div class='column large-5 small-11'>
						<input class="attached-input required" type="text" name="name" id="name" placeholder="<?php _e('Nome Completo','bon'); ?>"  value="" size="22" tabindex="1" />
						<div class="contact-form-error" ><?php _e('Por favor, digitar seu nome completo.','bon'); ?></div>
					</div>
				</div>
				<div class="row collapse input-container">
					<div class="column large-1 small-1"><span class="attached-label prefix"><i class="sha-mail-2"></i></span></div>
					<div class='column large-5 small-11'>
						<input class="attached-input required email" type="email" placeholder="<?php _e('Email','bon'); ?>" name="email" id="email" value="" size="22" tabindex="2" />
						<div class="contact-form-error" ><?php _e('Por favor, digite um endereço de email válido.','bon'); ?></div>
					</div>
				</div>
				
				<div class="row collapse textarea-container input-container" data-match-height>
					<div data-height-watch class="column large-1 small-1"><span class="attached-label prefix"><i class="sha-pencil"></i></span></div>
					<div data-height-watch class='column large-5 small-11'>
						<textarea name="messages" class="attached-input required" id="messages" cols="58" rows="10" tabindex="4" placeholder="<?php _e('Messagem','bon'); ?>" ></textarea>
						<div class="contact-form-error" ><?php _e('Por favor, digite sua mensagem.','bon'); ?></div>
					</div>
				</div>
				<div>
					<input type="hidden" name="subject" value="<?php printf(__('Enviado do Perfil ECO de %s','bon'), get_the_title()); ?>" />
					<input type="hidden" name="receiver" value="<?php echo $agent_email; ?>" />
					<input class="button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Enviar mensagem particular', 'bon') ?>" />
					<span class="contact-loader"><img src="<?php echo trailingslashit(BON_THEME_URI); ?>assets/images/loader.gif" alt="loading..." />
				</div>
				<div class="sending-result"><div class="green bon-toolkit-alert"></div></div>
			</form>
		</div>
	</div>
</article>

<div id="agent-listings">
	<h3><?php printf(__('Últimos Anúncios de %s','bon'), get_the_title(get_the_ID())); ?></h3>
	<?php bon_get_template_part('block', 'agentlisting'); ?>
</div>

<?php } else {
?>

<li>
<article id="post-<?php the_ID(); ?>" class="<?php bon_entry_class($status); ?>" itemscope itemtype="http://schema.org/RealEstateAgent">

		<header class="entry-header">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php 
			echo wp_get_attachment_image( $agent_pic, 'listing_small_box');
			?>
			</a>
		</header><!-- .entry-header -->

		<div class="entry-summary">

			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title" itemprop="name">', '</h1>', false ) ); ?></a>
			<div class="entry-meta">
				<div>
					<strong><?php _e('Mobile:','bon'); ?></strong>
					<span><?php echo $agent_mobile; ?></span>
				</div>
				<div>	
					<strong><?php _e('Offce:','bon'); ?></strong>
					<span><?php echo $agent_office; ?></span>
				</div>
				<div>			
					<strong><?php _e('Fax:','bon'); ?></strong>
					<span><?php echo $agent_fax; ?></span>
				</div>
			</div>
		</div><!-- .entry-summary -->

</article>
</li>
<?php } ?>