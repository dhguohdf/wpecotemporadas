<?php 

	$status = shandora_get_meta($post->ID, 'listing_status'); 
	$badge = shandora_get_meta($post->ID, 'listing_badge');
    $badgeclr = shandora_get_meta($post->ID, 'listing_badge_color');
    $size = ( isset( $_GET['view'] ) && $_GET['view'] == 'list' ) ?  'listing_list' : 'listing_small';

?>
<header class="entry-header">
	<div class="listing-hover">
		<span class="mask"></span>
		<?php echo shandora_get_listing_hover_action(get_the_ID()); ?>
	</div>
	<?php

		if( get_post_type() == 'listing' ) {

			$terms = get_the_terms( get_the_ID(), "property-type" );
					
			if ( $terms && ! is_wp_error( $terms ) ) {														   														   
			   foreach ( $terms as $term ) {															   
					echo '<a class="property-type" href="' . get_term_link($term->slug, "property-type" ) .'">'.$term->name.'</a>';
					break; // to display only one property type
			   }														   													   														   
			}
		} else {
			$terms = get_the_terms( get_the_ID(), "body-type" );
				
			if ( $terms && ! is_wp_error( $terms ) ) {														   														   
			   foreach ( $terms as $term ) {															   
					echo '<a class="body-type property-type" href="' . get_term_link($term->slug, "body-type" ) .'">'.$term->name.'</a>';
					break; // to display only one property type
			   }														   													   														   
			}
		}
	?>
	<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'size' => $size ) ); ?>
	<?php $status_opt = shandora_get_search_option('status'); ?>

	<?php if ( get_post_type() == 'listing' ) : ?>
		<div class="badge <?php echo $status; echo ($size == 'listing_list') ? ' hide-for-small' : ''; ?>">
			<span>
				<?php if($status != 'none') { if(array_key_exists($status, $status_opt)) { echo $status_opt[$status]; } } ?>
			</span>
		</div>
	<?php else : ?>
		<div class="badge <?php echo $badgeclr; echo ($size == 'listing_list') ? ' hide-for-small' : ''; ?>">
			<span>
				<?php if($badgeclr != 'none' && !empty($badge)) { echo $badge; } ?>
			</span>
		</div>
	<?php endif; ?>

</header><!-- .entry-header -->