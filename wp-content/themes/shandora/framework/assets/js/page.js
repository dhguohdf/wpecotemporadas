jQuery(document).ready(function($){

	if($('select#page_template').val() == 'page-templates/page-template-compare-listings.php' || 
			$('select#page_template').val() == 'page-templates/page-template-search-listings.php' ||
			$('select#page_template').val() == 'page-templates/page-template-home.php' ||
			$('select#page_template').val() == 'page-templates/page-template-all-listings.php') {
			$('#theme-layouts-post-meta-box').hide();
		}

	$('select#page_template').change(function(){
		if($(this).val() == 'page-templates/page-template-compare-listings.php' || 
			$(this).val() == 'page-templates/page-template-search-listings.php' ||
			$(this).val() == 'page-templates/page-template-home.php' ||
			$(this).val() == 'page-templates/page-template-all-listings.php') {
			$('#theme-layouts-post-meta-box').hide();
		} else {
			$('#theme-layouts-post-meta-box').show();
		}
	});

	

});