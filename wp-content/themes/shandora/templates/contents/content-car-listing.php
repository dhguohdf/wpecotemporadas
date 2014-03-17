<?php 

	$suffix = 'listing_';
	$status = shandora_get_meta($post->ID, $suffix . 'status'); 
    $transmission = shandora_get_meta($post->ID, $suffix . 'transmission');
    $engine = shandora_get_meta($post->ID, $suffix . 'enginesize');
    $mileage = shandora_get_meta($post->ID, $suffix . 'mileage');
    $badge = shandora_get_meta($post->ID, $suffix . 'badge');
    $badgeclr = shandora_get_meta($post->ID, $suffix . 'badge_color');
    $trans_opt = shandora_get_car_search_option('transmission');
    if(array_key_exists($transmission, $trans_opt)) {
    	$transmission = $trans_opt[$transmission];
    }
    $status_opt = shandora_get_car_search_option('status');
    if(array_key_exists($status, $status_opt)) {
    	$status = $status_opt[$status];
    }
if( is_singular( get_post_type() ) ) { 


?>
<article id="post-<?php the_ID(); ?>" class="<?php bon_entry_class($status); ?> listing" itemscope itemtype="http://schema.org/RealEstateAgent">
	<header class="entry-header clear">
		<?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title" itemprop="name">', '</h1>', false ) ); ?>
		
		<a class="print" href="javascript:window.print()"><i class="sha-printer"></i></a>
		<?php echo apply_atomic_shortcode('listing_published', '[entry-published text="'.__('Published on ','bon').'"]'); ?>
		<h4 class="price"><?php shandora_get_listing_price(); ?></h4>

	</header><!-- .entry-header -->

	<?php bon_get_template_part('block','listinggallery'); ?>

	<div class="entry-content clear" itemprop="description">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<p class="page-links">' . '<span class="before">' . __( 'Pages:', 'bon' ) . '</span>', 'after' => '</p>' ) ); ?>
	</div><!-- .entry-content -->


	<div class="entry-meta" itemprop="description">
		<?php bon_get_template_part('block', 'carlistingmeta'); ?>
	</div>

	<div class="row entry-specification">
		<div id="detail-tab" class="column large-12">
			<?php bon_get_template_part('block','carlistingtab'); ?>
		</div>
		
	</div>

	<div class="row">
		<?php $vid = shandora_get_video(); ?>
		<div id="listing-video"  class="column large-12">
			<?php echo $vid; ?>
		</div>
	</div>

	<?php bon_get_template_part('block', 'carlistingfooter'); ?>

</article>
<?php } else {
?>

<li>
<article id="post-<?php the_ID(); ?>" class="<?php bon_entry_class($badgeclr); ?>" itemscope itemtype="http://schema.org/RealEstateAgent">

		<header class="entry-header">
			<div class="listing-hover">
				<span class="mask"></span>
				<?php echo shandora_get_listing_hover_action(get_the_ID()); ?>
			</div>
			<?php
						$terms = get_the_terms( get_the_ID(),"body-type" );
						
			if ( $terms && ! is_wp_error( $terms ) ) 
			{														   														   
				   foreach ( $terms as $term )
				   {															   
						echo '<a class="body-type property-type" href="' . get_term_link($term->slug, "body-type" ) .'">'.$term->name.'</a>';
						break; // to display only one property type
				   }														   													   														   
			}
							
			?>
			<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'size' => 'listing_small' ) ); ?>
			<div class="badge <?php echo $badgeclr; ?>"><span><?php if($badge != 'none') { echo $badge; } ?></span></div>

		</header><!-- .entry-header -->

		<div class="entry-summary">

			<?php echo apply_atomic_shortcode( 'entry_title', the_title( '<h1 class="entry-title" itemprop="name"><a href="'.get_permalink().'" title="'.the_title_attribute( array('before' => __('Permalink to ','bon'), 'echo' => false) ).'">', '</a></h1>', false ) ); ?>
			<div class="entry-meta">

				<div class="icon engine">
					<i class="sha-engine"></i>
					<span><?php if($engine){ echo $engine; } else { echo _e('Unspecified','bon'); } ?></span>
				</div>

				<div class="icon transmission">
					<i class="sha-gear-shifter"></i>
					<span><?php if($transmission){ echo $transmission; } else { echo _e('Unspecified','bon'); } ?></span>
				</div>
				<div class="icon mileage">
					<i class="awe-dashboard"></i>
					<span><?php if(isset($mileage)) { echo $mileage; } else { _e('Unspecified','bon'); } ?></span>
				</div>

			</div>
		</div><!-- .entry-summary -->

		<footer class="entry-footer">
			<div class="property-price">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( array('before' => __('Permalink to ','bon') ) ); ?>"><?php shandora_get_listing_price(); ?></a>
			</div>
		</footer><!-- .entry-footer -->

</article>
</li>
<?php } ?>