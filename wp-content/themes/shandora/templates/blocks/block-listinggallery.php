<?php
$listing_gal = shandora_get_meta(get_the_ID(), 'listing_gallery');

$layout = get_theme_mod( 'theme_layout' );
if( $layout == '1c') {
	$image_size = 'featured_slider';
} else {
	$image_size = 'listing_large';
}
if ( $listing_gal ) {
	$attachments = array_filter( explode( ',', $listing_gal ) );

	if($attachments) {

?>
<div class="entry-gallery">
	<?php $with_thumbnail = bon_get_option('listing_gallery_thumbnail'); 

	$ul_class = 'bxslider';
	if($with_thumbnail == 'no') {
		$ul_class = 'bxslider-no-thumb';
	} else if($with_thumbnail == 'both') {
		$ul_class = 'bxslider-both';
	}
	?>

	<ul class="<?php echo $ul_class; ?>">
	<?php 
	
		foreach ( $attachments as $attachment_id ) {
			$meta = wp_get_attachment_metadata( $attachment_id );
			$popup = wp_get_attachment_image_src( $attachment_id, 'full' );
			$popup = $popup[0];
			$args = array( 'alt' => '' );
			echo '<li><span class="caption">'. get_the_title($attachment_id) .'</span><a href="'. $popup .'" class="listing-gallery-popup">' . wp_get_attachment_image( $attachment_id, $image_size ) . '</a></li>';	
		} 

	?>
	</ul>
	<?php if($with_thumbnail == 'yes' || $with_thumbnail == 'both') : ?>
	<ul id="bx-pager" class="large-custom-grid-7 small-custom-grid-6">
	<?php
		$i = 0;
		foreach ( $attachments as $attachment_id ) {
			echo '<li><a data-slide-index="'.$i.'" href="">' .wp_get_attachment_image( $attachment_id, 'listing_small_box') . '<span class="mask"></span></a></li>';	
			$i++;
		}
	?>
	</ul>

	<?php endif; ?>
</div>

<?php } 

} else { 

	if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'size' => 'listing_large', 'before' => '<div class="featured-image">', 'after' => '</div>' ) );

} ?>