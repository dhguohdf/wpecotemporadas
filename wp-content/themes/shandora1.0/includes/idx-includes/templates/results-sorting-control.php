<?php
list($sortorder, $sortby, $is_default) = self::get_sort();
$sortby = strtolower($sortby);
$sortorder = strtolower($sortorder);
?>
<div class="dsidx-sorting-control custom-idx-sorting-control">
   <form class="custom"><div class="row"><div class="column large-6">
        <span><?php _e('Sorted by', 'bon'); ?></span>
          <select id="idx-plus-sorting-control-type">
           <option value="DateAdded|DESC" <?php selected(($sortby === 'dateadded' && 'desc' === $sortorder), true); ?>>
             <?php _e('Time on market, newest first', 'bon'); ?>
           </option>
           <option value="DateAdded|ASC" <?php selected(($sortby === 'dateadded' && 'asc' === $sortorder), true); ?>>
             <?php _e('Time on market, oldest first', 'bon'); ?>
           </option>
           <option value="Price|DESC" <?php selected(($sortby === 'price' && 'desc' === $sortorder), true); ?>>
             <?php _e('Price, highest first', 'bon'); ?>
           </option>
           <option value="Price|ASC" <?php selected(($sortby === 'price' && 'asc' === $sortorder), true); ?>>
             <?php _e('Price, lowest first', 'bon'); ?>
           </option>
           <option value="OverallPriceDropPercent|DESC" <?php selected(($sortby === 'overallpricedroppercent' && 'desc' === $sortorder), true); ?>>
             <?php _e('Price drop %, largest first', 'bon'); ?>
           </option>
           <option value="LastUpdated|DESC" <?php selected(($sortby === 'lastupdated' && 'desc' === $sortorder), true); ?>>
             <?php _e('Last Updated, newest first', 'bon'); ?>
           </option>
           <option value="WalkScore|DESC" <?php selected(($sortby === 'walkscore' && 'desc' === $sortorder), true); ?>>
             <?php _e('Walk Score&#0174;, highest first', 'bon'); ?>
           </option>
           <option value="ImprovedSqFt|DESC" <?php selected(($sortby === 'improvedsqft' && 'desc' === $sortorder), true); ?>>
             <?php _e('Improved size, largest first', 'bon'); ?>
           </option>
           <option value="LotSqFt|DESC" <?php selected(($sortby === 'lotsqft' && 'desc' === $sortorder), true); ?>>
             <?php _e('Lot size, largest first', 'bon'); ?>
           </option>
         </select>
   </div></div></form>
</div>