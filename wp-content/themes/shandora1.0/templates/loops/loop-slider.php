<?php 
  
  $slider_post_per_page = bon_get_option('slider_post_per_page', 5);

  $loop = new WP_Query(
    array(
        'post_type'      => 'slider',
        'posts_per_page' => $slider_post_per_page,
        'order_by'       => 'menu_order',
        'order'          => 'ASC'
      )
  ); 

?>

<?php if ( $loop->have_posts() ) : ?>
<div id="slider-container" class="container full">

  <div class="slider-inner-container">
      
      <div id="main-slider" class="flexslider">

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