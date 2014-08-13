<?php 
  
  $slider_post_per_page = bon_get_option('slider_post_per_page', 5);
  $post_in = array();

  global $post;
  $slideshow_ids = shandora_get_meta( $post->ID, 'slideshow_ids' );
  $slideshow_type = shandora_get_meta( $post->ID, 'slideshow_type' );
  $slideshow_type = ($slideshow_type != '' ) ? $slideshow_type : 'full';
  if( !empty( $slideshow_ids ) ) {
    $post_in = explode(',', $slideshow_ids);
  }

  $loop = new WP_Query(
    array(
        'post_type'      => 'slider',
        'posts_per_page' => $slider_post_per_page,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'DESC',
        'post__in' => $post_in,
      )
  ); 

?>

<?php if ( $loop->have_posts() ) : ?>
<div id="slider-container" class="container <?php echo $slideshow_type; ?>">

  <div class="slider-inner-container">
      
      <div id="main-slider" class="flexslider" data-interval="<?php echo bon_get_option('slider_interval', 12000 ); ?>">

        <ul class="slides">
          <?php while( $loop->have_posts() ) : $loop->the_post(); ?>
            
            <?php bon_get_template_part('block','slider'); ?>

          <?php endwhile; ?>
        </ul>

      </div>

  </div>

</div>

<?php else : 

    bon_get_template_part('loop', 'error');

endif; 
wp_reset_postdata(); 
?>