jQuery(document).ready(function($){

	if($('select#page_template').val() == 'page-templates/page-template-compare-listings.php' || 
		$('select#page_template').val() == 'page-templates/page-template-home.php' ) {
			$('#theme-layouts-post-meta-box').hide();
		}

	$('select#page_template').change(function(){
		if($(this).val() == 'page-templates/page-template-compare-listings.php' || 
			$(this).val() == 'page-templates/page-template-home.php') {
			$('#theme-layouts-post-meta-box').hide();
		} else {
			$('#theme-layouts-post-meta-box').show();
		}
	});

	if($('select#page_template').val() == 'page-templates/page-template-property-status.php') {
		$('#status-opt').show();
	} else {
		$('#status-opt').hide();
	}

	$('select#page_template').change(function(){
		if($('select#page_template').val() == 'page-templates/page-template-property-status.php') {
			$('#status-opt').show();
		} else {
			$('#status-opt').hide();
		}
	});


	if($('select#page_template').val() == 'page-templates/page-template-car-status.php') {
		$('#car-status-opt').show();
	} else {
		$('#car-status-opt').hide();
	}

	$('select#page_template').change(function(){
		if($('select#page_template').val() == 'page-templates/page-template-car-status.php') {
			$('#car-status-opt').show();
		} else {
			$('#car-status-opt').hide();
		}
	});

});