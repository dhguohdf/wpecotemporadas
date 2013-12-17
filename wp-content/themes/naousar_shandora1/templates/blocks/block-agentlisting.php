<?php 

$args = array(
	'posts_per_page'	=> 48,
	'post_type'			=> 'listing',
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

		$status = shandora_get_meta($post->ID, 'listing_status'); 
	    $bed = shandora_get_meta($post->ID, 'listing_bed');
	    $bath = shandora_get_meta($post->ID, 'listing_bath');
	    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
	 	$sizemeasurement = bon_get_option('measurement');

?>
<li>
<article id="post-<?php $post->ID; ?>" class="<?php bon_entry_class($status); ?>" itemscope itemtype="http://schema.org/RealEstateAgent">
	<header class="entry-header">
		<div class="listing-hover">
			<span class="mask"></span>
			<?php echo shandora_get_listing_hover_action($post->ID); ?>
		</div>
		
		<?php	
			$terms = get_the_terms( $post->ID,"property-type" );
						
			if ( $terms && ! is_wp_error( $terms ) ) 
			{														   														   
				   foreach ( $terms as $term )
				   {															   
						echo '<a class="property-type" href="' . get_term_link($term->slug, "property-type" ) .'">'.$term->name.'</a>';
						break; // to display only one property type
				   }														   													   														   
			}
							
			?>
			<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'post_id' => $post->ID, 'size' => 'listing_small' ) ); ?>
			<?php $status_opt = shandora_get_search_option('status'); ?>
			<div class="badge <?php echo $status; ?>"><span><?php if($status != 'none') { if(array_key_exists($status, $status_opt)) { echo $status_opt[$status]; } } ?></span></div>

		</header><!-- .entry-header -->

		<div class="entry-summary">

			<?php echo apply_atomic_shortcode( 'entry_title', '<h1 class="entry-title" itemprop="name">'.$post->post_title.'</h1>' ); ?>
			<div class="entry-meta">

				<div class="icon bed">
					<i class="sha-bed"></i>
					<span><?php if(empty($bed)) { echo '-'; } else { printf( _n('%s Bed','%s Beds', $bed , 'bon'), $bed ); }?></span>
				</div>

				<div class="icon bath">
					<i class="sha-bath"></i>
					<span><?php if(empty($bath)) { echo '-'; } else { printf(_n('%s Bath','%s Baths', $bath , 'bon'), $bath ); } ?></span>
				</div>
				<div class="icon size">
					<i class="sha-ruler"></i>
					<span><?php if($lotsize) { echo $lotsize . ' ' . $sizemeasurement; } else { echo '-'; } ?></span>
				</div>
				
			</div>
		</div><!-- .entry-summary -->

		<footer class="entry-footer">
			<div class="property-price">
				<?php shandora_get_listing_price(); ?>
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