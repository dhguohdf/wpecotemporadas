<?php
   global $idx_plus_user;
   $data = self::get_global('results_listings');
   $i = 0;
?>
 <?php 
    $ul_class = "large-block-grid-3 small-block-grid-2";
    $layout = get_theme_mod('theme_layout');
    if($layout == '1c') {
      $ul_class= "large-block-grid-4 small-block-grid-2";
    }
 ?>
 <ul id="dsidx-listings" class="listings <?php echo $ul_class; ?>">

  <?php 
  if (!is_array($data)) { 
    echo $data;
  } 
  else {
    foreach ($data as $listing) {
      extract($listing); 


      $classes = array(
      'listing-' . $mls
      );                
      
      ?>
      <li>

        <article>
          <header class="entry-header">
            <?php echo '<a href="' . $url . '" rel="nofollow" alt="'.$title.'">' . self::process_photo($photo, $listing, array('width'=>400, 'height' => 400)) . '</a>';?>
          </header>
          <div class="entry-summary">
            <h1 itemprop="name" class="entry-title"><?php echo '<a href="' . $url . '" rel="nofollow" alt="'.$title.'">' . $title . '</a>'; ?></h1>
            <div class="entry-meta">
              <div class="icon bed">
                <i class="sha-bed"></i>
                <span><?php echo $beds;?></span>
              </div>
              <div class="icon bath">
                <i class="sha-bath"></i>
                <span><?php echo $baths;?></span>
              </div>
              <div class="icon size">
                <i class="sha-ruler"></i>
                <span><?php echo $lotsize;?></span>
              </div>
            </div>
          </div>

          <footer class="entry-footer">
            <div class="property-price">
                <span itemprop="price"><?php echo $price; ?></span>
            </div>
          </footer>

        </article>
      </li>

    <?php $i++; } }?>

  </ul>