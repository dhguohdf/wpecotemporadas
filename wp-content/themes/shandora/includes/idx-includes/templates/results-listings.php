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
    if( !is_sold() ) {
    foreach ($data as $listing) {
      extract($listing); 

      if($PhotoCount > 0 ) {
          $photourl = trailingslashit( esc_url( $PhotoUriBase ) ) . '0-full.jpg';
        } else {
          $photourl = get_template_directory_uri() . '/assets/images/nophoto.png';
        }

      $img = self::process_photo($photo, $listing, array('width'=>400, 'height' => 400));

      if(empty($img)) {
        $img = '<img src="'.$photourl.'" alt="'.$title.'"/>';
      }

      $classes = array(
      'listing-' . $mls
      );                
      
      ?>
      <li>

        <article>
          <header class="entry-header">
            <?php echo '<a href="' . $url . '" rel="nofollow" alt="'.$title.'">' . $img . '</a>';?>
          </header>
          <div class="entry-summary">
            <h1 itemprop="name" class="entry-title"><?php echo '<a href="' . $url . '" rel="nofollow" alt="'.$title.'">' . $title . '</a>'; ?></h1>
            <div class="entry-meta">
              <div class="icon bed">
                <i class="sha-bed"></i>
                <span><?php echo (isset($beds) && !empty($beds)) ? $beds : $bedsshortstring;?></span>
              </div>
              <div class="icon bath">
                <i class="sha-bath"></i>
                <span><?php echo (isset($baths) && !empty($baths)) ? $baths : $bathsshortstring;?></span>
              </div>
              <div class="icon size">
                <i class="sha-ruler"></i>
                <span><?php echo (isset($homesize) && !empty($homesize)) ? $homesize : $ImprovedSqFt;?></span>
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

    <?php $i++; } } else {
        foreach ($data as $listing) {
        extract($listing); 

        $title = $Address . ', (MLS #'.$MlsNumber.')';

        if($PhotoCount > 0 ) {
          $photourl = trailingslashit( esc_url( $PhotoUriBase ) ) . '0-full.jpg';
        } else {
          $photourl = get_template_directory_uri() . '/assets/images/nophoto.png';
        }
      $classes = array(
      'listing-' . $mls
      );                
      
      ?><!-- <div>DUMP: <?php var_dump($listing); ?></div> -->
      <li>

        <article>
          <header class="entry-header">
            <?php echo '<a href="' . $PrettyUriForUrl . '" rel="nofollow" alt="'.$title.'"><img src="'. $photourl.'"</a>';?>
          </header>
          <div class="entry-summary">
            <h1 itemprop="name" class="entry-title"><?php echo '<a href="' . $PrettyUriForUrl . '" rel="nofollow" alt="'.$title.'">' . $title . '</a>'; ?></h1>
            <div class="entry-meta">
              <div class="icon bed">
                <i class="sha-bed"></i>
                <span><?php echo $bedsshortstring;?></span>
              </div>
              <div class="icon bath">
                <i class="sha-bath"></i>
                <span><?php echo $bathsshortstring;?></span>
              </div>
              <div class="icon size">
                <i class="sha-ruler"></i>
                <span><?php echo $ImprovedSqFt;?></span>
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

    <?php $i++; }
    } }
    ?>

  </ul>