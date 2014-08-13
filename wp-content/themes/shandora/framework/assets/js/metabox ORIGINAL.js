(function ($) {
	bonMetaboxInterface = {
		toggle_collapsed_fields: function () {
			$( '.meta_box .collapsed' ).each(function(){
				$( this ).find( 'input:checked' ).parent().parent().nextAll().each( function(){
					if ($( this ).hasClass( 'last' ) ) {
						$( this ).removeClass( 'hidden' );
						return false;
					}
					$( this ).filter( '.hidden' ).removeClass( 'hidden' );
					
				});

				$( 'input:checkbox', this ).click(function ( e ) {
					bonMetaboxInterface.unhide_hidden( $( this ).attr( 'id' ) );
				});

			});
	 	},

		unhide_hidden: function ( obj ) {
	 		obj = $( '#' + obj ); // Get the jQuery object.
	 		
			if ( obj.attr( 'checked' ) ) {
				obj.parent().parent().nextAll().removeClass( 'hidden' ).addClass( 'visible' );
			} else {
				obj.parent().parent().nextAll().each( function(){
					if ( $( this ).filter( '.last' ).length ) {
						$( this ).addClass( 'hidden' );
					return false;
					}
					$( this ).addClass( 'hidden' );
				});
			}
	 	}

	 	
	};


	$(document).ready(function(){
			
			bonMetaboxInterface.toggle_collapsed_fields();

			if(!!$.prototype.wpColorPicker) {
				$('.bon-color').wpColorPicker();
			}

		// the upload image button, saves the id and outputs a preview of the image
		var imageFrame;

	
		$('.meta_box_image').on( 'click', 'a.meta_box_upload_image_button', function( event ) {

			
			
			var options, attachment;
			
			$self = $(this);
			$div = $self.closest('div.meta_box_image');

			event.preventDefault();

			// if the frame already exists, open it
			if ( imageFrame ) {
				imageFrame.open();
				return;
			}
			
			// set our settings
			imageFrame = wp.media({
				title: 'Choose Image',
				multiple: false,
				library: {
			 		type: 'image'
				},
				button: {
			  		text: 'Use This Image'
				}
			});
			
			// set up our select handler
			imageFrame.on( 'select', function() {
				selection = imageFrame.state().get('selection');
				
				if ( ! selection )
				return;
				
				// loop through the selected files
				selection.each( function( attachment ) {
					var src = '';
					if(attachment.attributes.sizes.thumbnail != undefined) {
						src = attachment.attributes.sizes.thumbnail.url;
					} else {
						src = attachment.attributes.sizes.full.url;
					}
					var id = attachment.id;
					
					$div.find('.meta_box_preview_image').attr('src', src);
					$div.find('.meta_box_upload_image').val(id);
				} );
			});
			
			// open the frame
			imageFrame.open();
		});
		
		// the remove image link, removes the image id from the hidden field and replaces the image preview
		$('.meta_box_clear_image_button').click(function() {
			var defaultImage = $(this).parent().siblings('.meta_box_default_image').text();
			$(this).parent().siblings('.meta_box_upload_image').val('');
			$(this).parent().siblings('.meta_box_preview_image').attr('src', defaultImage);
			return false;
		});
		
		// the file image button, saves the id and outputs the file name
		var fileFrame;
		$('.meta_box_file_stuff').on('click', 'a.meta_box_upload_file_button', function(e) {
			e.preventDefault();
			
			var options, attachment;
			
			$self = $(e.target);
			$div = $self.closest('div.meta_box_file_stuff');
			
			// if the frame already exists, open it
			if ( fileFrame ) {
				fileFrame.open();
				return;
			}
			
			// set our settings
			fileFrame = wp.media({
				title: 'Choose File',
				multiple: false,
				library: {
			 		type: 'file'
				},
				button: {
			  		text: 'Use This File'
				}
			});
			
			// set up our select handler
			fileFrame.on( 'select', function() {
				selection = fileFrame.state().get('selection');
				
				if ( ! selection )
				return;
				
				// loop through the selected files
				selection.each( function( attachment ) {
					var src = attachment.attributes.url;
					var id = attachment.id;
					
					$div.find('.meta_box_filename').text(src);
					$div.find('.meta_box_upload_file').val(src);
					$div.find('.meta_box_file').addClass('checked');
				} );
			});
			
			// open the frame
			fileFrame.open();
		});
		
		// the remove image link, removes the image id from the hidden field and replaces the image preview
		$('.meta_box_clear_file_button').click(function() {
			$(this).parent().siblings('.meta_box_upload_file').val('');
			$(this).parent().siblings('.meta_box_filename').text('');
			$(this).parent().siblings('.meta_box_file').removeClass('checked');
			return false;
		});
		
		// function to create an array of input values
		function ids(inputs) {
			var a = [];
			for (var i = 0; i < inputs.length; i++) {
				a.push(inputs[i].val);
			}
			//$("span").text(a.join(" "));
	    }
		// repeatable fields
		$('.meta_box_repeatable').on('click', 'a.meta_box_repeatable_add', function( event ) {
			// clone
			var row = $(this).closest('.meta_box_repeatable').find('tbody tr:last-child');
			var clone = row.clone(false);
			clone.find('select.chosen').removeAttr('style', '').removeAttr('id', '').removeClass('chzn-done').data('chosen', null).next().remove();
			clone.find('input.regular-text, textarea, select, input.upload').val('');
			clone.find('input[type=checkbox], input[type=radio]').attr('checked', false);
			row.after(clone);
			// increment name and id
			clone.find('input, textarea, select')
				.attr('name', function(index, name) {
					
					return name.replace(/(\d+)/, function(fullMatch, n) {
						return Number(n) + 1;
					});
				});
			// increment name and id
			clone.find('input, textarea, select, .ui-slider')
				.attr('id', function(index, id) {
					
					return id.replace(/(\d+)/, function(fullMatch, n) {
						return Number(n) + 1;
					});
				});

			clone.find('.ui-slider').each(function(){
				var max = parseInt($(this).data('max'));
				var min = parseInt($(this).data('min'));
				var step = parseInt($(this).data('step'));

				$( this ).slider({
					value: 0,
					min: min,
					max: max,
					step: step,
					slide: function( event, ui ) {
						$( this ).next().val( ui.value );
					}
				});
			});

			clone.find('.datepicker').each(function(){
				$(this).datepicker({
					dateFormat: 'yy-mm-dd'
				});
			});

			var arr = [];

			
			$('input.repeatable_id:text').each(function(){ arr.push($(this).val()); }); 
			clone.find('input.repeatable_id')
				.val(Number(Math.max.apply( Math, arr )) + 1);
			if (!!$.prototype.chosen) {
				clone.find('select.chosen')
					.chosen({allow_single_deselect: true});
			}
			
			event.preventDefault();
		});

		
		$('.meta_box_repeatable').on( 'click', 'a.meta_box_repeatable_remove', function( event ){
			$(this).closest('tr').remove();
			event.preventDefault();
		});
			
		$('.meta_box_repeatable tbody').sortable({
			opacity: 0.6,
			revert: true,
			cursor: 'move',
			handle: '.hndle'
		});
		
		// post_drop_sort	
		$('.sort_list').sortable({
			connectWith: '.sort_list',
			opacity: 0.6,
			revert: true,
			cursor: 'move',
			cancel: '.post_drop_sort_area_name',
			items: 'li:not(.post_drop_sort_area_name)',
	        update: function(event, ui) {
				var result = $(this).sortable('toArray');
				var thisID = $(this).attr('id');
				$('.store-' + thisID).val(result) 
			}
	    });

		$('.sort_list').disableSelection();

		// turn select boxes into something magical
		if (!!$.prototype.chosen)
			$('.chosen').chosen({ allow_single_deselect: true });

		// Image Options
		$('.radio-img-img').click(function(){
			$(this).parent().parent().find('.radio-img-img').removeClass('radio-img-selected');
			$(this).addClass('radio-img-selected');		
		});
			
		$('.radio-img-label').hide();
		$('.radio-img-img').show();
		$('.radio-img-radio').hide();


			// Uploading files
			var image_gallery_frame;
			var $image_gallery_ids = $('.image-gallery-input');
			var $gallery_images = $('.gallery-images-container ul.gallery-images');

			
			$('.add-gallery-images ').on( 'click', 'a', function( event ) {

				var $el = $(this);
				

				
				// If the media frame already exists, reopen it.
				if ( image_gallery_frame ) {
					image_gallery_frame.open();
					return;
				}

				// Create the media frame.
				image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
					// Set the title of the modal.
					title: 'Add Images to Gallery',
					button: {
						text: 'Add to gallery',
					},
					multiple: true
				});

				// When an image is selected, run a callback.
				image_gallery_frame.on( 'select', function() {

					var selection = image_gallery_frame.state().get('selection');
					
					var attachment_ids = $image_gallery_ids.val();

					selection.map( function( attachment ) {

						attachment = attachment.toJSON();

						if ( attachment.id ) {

							

							if(attachment_ids != '') {
								at_id = ","+attachment.id;
							} else {
								at_id = attachment.id;
							}

							attachment_ids += at_id;

							$gallery_images.append('\
								<li class="image" data-attachment_id="' + attachment.id + '">\
									<img src="' + attachment.sizes.thumbnail.url + '" />\
									<ul class="actions">\
										<li><a href="#" class="delete" title="Delete image">Delete</a></li>\
									</ul>\
								</li>');
						}

					} );
				
					$image_gallery_ids.val( attachment_ids );
				});

				// Finally, open the modal.
				image_gallery_frame.open();

				return false;
			});

			// Image ordering
			
			$gallery_images.sortable({
				items: 'li.image',
				cursor: 'move',
				scrollSensitivity:40,
				forcePlaceholderSize: true,
				forceHelperSize: false,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'gallery-image-placeholder',
				start:function(event,ui){
					ui.item.css('background-color','#f6f6f6');
				},
				stop:function(event,ui){
					ui.item.removeAttr('style');
				},
				update: function(event, ui) {
					var attachment_ids = '';

					$('.gallery-images-container ul li.image').css('cursor','default').each(function() {
						var attachment_id = $(this).data( 'attachment_id' );
						attachment_ids = attachment_ids + attachment_id + ',';
					});

					$image_gallery_ids.val( attachment_ids );
				}
			});
			
			// Remove images
			$('.gallery-images-container').on( 'click', 'a.delete', function() {

				$(this).closest('li.image').remove();

				var at_ids = '';
				var a = $('.gallery-images-container ul li.image').length;


				$('.gallery-images-container ul li.image').css('cursor','default').each(function() {

					var attachment_id = $(this).data( 'attachment_id' );
					at_ids = at_ids + attachment_id + ',';
				});

				if(at_ids.substr(at_ids.length -1) == ',') {
					at_ids = at_ids.substr(0, at_ids.length -1);
				}

				$image_gallery_ids.val( at_ids );

				return false;
			} );
		
	});
	
})(jQuery);