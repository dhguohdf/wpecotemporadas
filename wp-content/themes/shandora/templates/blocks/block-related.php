<?php 

$current_post = $post->ID;

$locs = wp_get_object_terms( $current_post, 'property-location' );
$feats = wp_get_object_terms( $current_post, 'property-feature' );
$types = wp_get_object_terms( $current_post, 'property-type' );

$price = shandora_get_meta( $current_post, 'listing_price');
$price_min = $price - ( $price * 20 / 100 );
$price_max = $price + ( $price * 20 / 100 );

$loc_query = array();

foreach($locs as $loc) {
	if($loc->parent == 0 ) {
		$loc_query[] = $loc->slug;
	}
}

$feat_query = array();
foreach( $feats as $feat ) {
	$feat_query[] = $feat->slug;
}

$type_query = array();
foreach ($types as $type ) {
 	$type_query[] = $type->slug;
} 

$posts_per_page = 3;
$layout = get_theme_mod('theme_layout');
if(empty($layout)) {
    $layout = get_post_layout(get_queried_object_id());
    if($layout == '1c') {
        $posts_per_page = 4;
    }
}

$args = array(
	'posts_per_page'	=> $posts_per_page,
	'post_type'			=> 'listing',
	'post_status'		=> 'publish',
	'tax_query'			=> array(
		'relation' => 'OR',
		array(
		    'taxonomy' => 'property-location',
		    'field' => 'slug',
		    'terms' => $loc_query,
		),
		array(
		    'taxonomy' => 'property-feature',
		    'field' => 'slug',
		    'terms' => '',
		),
		array(
		    'taxonomy' => 'property-type',
		    'field' => 'slug',
		    'terms' => '',
		)
	),
	'meta_query' => array(
       'relation' => 'OR',
       array(
       		'key' => 'shandora_listing_price',
       		'compare' => 'BETWEEN',
       		'value' => array( $price_min, $price_max ),
       		'type' => 'NUMERIC',
       )

	)
	
);

$related_query = get_posts($args);

if($related_query) : $compare_page = bon_get_option('compare_page'); ?>
<h3 class="related-property-header"><?php _e('Related Properties', 'bon'); ?></h3>
<hr />
<ul class="listings related <?php shandora_block_grid_column_class(); ?>" data-compareurl="<?php echo get_permalink($compare_page); ?>">
			
<?php
	foreach( $related_query as $post ) : 

		$status = shandora_get_meta($post->ID, 'listing_status'); 
	    $bed = shandora_get_meta($post->ID, 'listing_bed');
	    $bath = shandora_get_meta($post->ID, 'listing_bath');
	    $lotsize = shandora_get_meta($post->ID, 'listing_lotsize');
	 	$sizemeasurement = bon_get_option('measurement');

?>
<li>
<article id="post-<?php $post->ID; ?>" <?php post_class($status); ?> itemscope itemtype="http://schema.org/RealEstateAgent">
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

			<?php do_atomic('entry_summary'); ?>

		</div><!-- .entry-summary -->


		<footer class="entry-footer">
			<div class="property-price">
				<?php shandora_get_listing_price(); ?>
			</div>
		</footer><!-- .entry-footer -->

</article>
</li>
<?php
endforeach; 
?>
	</ul>
<?php
endif; wp_reset_query();
?>