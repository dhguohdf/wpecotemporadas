<?php

  
  $image_id = shandora_get_meta($post->ID, 'slider_image');
  
  $image_array = wp_get_attachment_image_src($image_id, 'featured_slider');

  $image_src = $image_array[0];


  $subtitle = shandora_get_meta($post->ID, 'slider_subtitle');

  $url = shandora_get_meta($post->ID, 'slider_linkto');

  $position = shandora_get_meta($post->ID, 'slider_position');

  $icon = 'sha-arrow-right';

  if($position == 'caption-right') {
    $icon = 'sha-arrow-left';
  }

?>
<li>
  <div class="mask"></div>
  <img src="<?php echo esc_url($image_src); ?>" alt="<?php the_title(); ?>" />

  <div class="hide-for-small flex-caption <?php echo $position; ?>">

    <h1 class="primary-title"><?php the_title(); ?></h1>

    <?php if($subtitle) { ?>
    <h2 class="secondary-title"><?php echo $subtitle; ?></h2>
    <?php } ?>
    
    <?php if($url) { ?>
    <a class="flex-readmore <?php if(empty($subtitle)) { echo "no-sub"; } ?>" href="<?php echo esc_url($url); ?>" title="<?php _e('Read More','bon'); ?>"><i class="<?php echo $icon; ?> icon-anim-bottom-top"></i></a>
    <?php } ?>

    <?php if($post->post_content != "") { ?>
    <div class="caption-content hide-for-medium-down">
      <?php the_content(); ?>
    </div>
    <?php } ?>
  
  </div>

</li>