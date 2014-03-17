<?php 

$args = array(
	'posts_per_page'	=> 48,
	'post_type'			=> 'car-listing',
	'post_status'		=> 'publish',
	'paged'				=> $paged,
	'meta_query'		=> array(
		array(
			'key' => 'shandora_listing_agentpointed',
            'value' => serialize(array(strval($post->ID))),
            'compare' => '=',
		)
	)
);

$agent_query = new WP_Query($args);

if($agent_query->have_posts()) : $compare_page = bon_get_option('compare_page'); ?>

<ul class="listings <?php shandora_block_grid_column_class(); ?>" data-compareurl="<?php echo get_permalink($compare_page); ?>">
			
<?php
	while($agent_query->have_posts()) : $agent_query->the_post();

		$suffix = 'listing_';
		$status = shandora_get_meta($post->ID, $suffix . 'status'); 
	    $transmission = shandora_get_meta($post->ID, $suffix . 'transmission');
	    $engine = shandora_get_meta($post->ID, $suffix . 'enginesize');
	    $mileage = shandora_get_meta($post->ID, $suffix . 'mileage');
	    $badge = shandora_get_meta($post->ID, $suffix . 'badge');
	    $badgeclr = shandora_get_meta($post->ID, $suffix . 'badge_color');

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
					<span><?php if($transmission){ echo ucwords(str_replace('-', ' ', $transmission)); } else { echo _e('Unspecified','bon'); } ?></span>
				</div>
				<div class="icon mileage">
					<i class="awe-dashboard"></i>
					<span><?php if($mileage) { echo $mileage; } else { _e('Unspecified','bon'); } ?></span>
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
<?php
endwhile; 
?>
	</ul>
<?php
endif; wp_reset_query();
?>