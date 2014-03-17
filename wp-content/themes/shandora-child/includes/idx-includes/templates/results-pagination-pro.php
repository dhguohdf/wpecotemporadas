<?php if(self::has_listings()) { ?>
 
<div class="dsidx-paging-control bon-idx-pagination pagination-centered">
<?php
	$parts = array();
	$pagination = self::get_global('paging_control');
	echo $pagination['original'];
?>
</div>

<?php } ?>