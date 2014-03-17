/**
 * BonFramework Admin Interface JavaScript
 *
 * All JavaScript logic for the theme options admin interface.
 * @since 1.0
 *
 */

(function ($) {

  bonAdminInterface = {
  
/**
 * toggle_nav_tabs()
 *
 * @since 1.0
 */
 
 	toggle_nav_tabs: function () {
 		var flip = 0;
	
		$( '#expand_options' ).click( function(){
			if( flip == 0 ){
				flip = 1;
				$( '#bon_container #bon-nav' ).hide();
				$( '#bon_container #content' ).width( 785 );
				$( '#bon_container .group' ).add( '#bon_container .group h1' ).show();

				$(this).text( '[-]' );

			} else {
				flip = 0;
				$( '#bon_container #bon-nav' ).show();
				$( '#bon_container #content' ).width( 595 );
				$( '#bon_container .group' ).add( '#bon_container .group h1' ).hide();
				$( '#bon_container .group:first' ).show();
				$( '#bon_container #bon-nav li' ).removeClass( 'current' );
				$( '#bon_container #bon-nav li:first' ).addClass( 'current' );

				$(this).text( '[+]' );

			}

		});
 	}, // End toggle_nav_tabs()

/**
 * load_first_tab()
 *
 * @since 1.0
 */
 
 	load_first_tab: function () {
 		$( '.group' ).hide();
 		$( '.group:has(".section"):first' ).fadeIn(); // Fade in the first tab containing options (not just the first tab).
 	}, // End load_first_tab()
 	
/**
 * open_first_menu()
 *
 * @since 5.0.0
 */
 
 	open_first_menu: function () {
 		$( '#bon-nav li.current.has-children:first ul.sub-menu' ).slideDown().addClass( 'open' ).children( 'li:first' ).addClass( 'active' ).parents( 'li.has-children' ).addClass( 'open' );
 	}, // End open_first_menu()
 	
/**
 * toggle_nav_menus()
 *
 * @since 5.0.0
 */
 
 	toggle_nav_menus: function () {
 		$( '#bon-nav li.has-children > a' ).click( function ( e ) {
 			if ( $( this ).parent().hasClass( 'open' ) ) { return false; }
 			
 			$( '#bon-nav li.top-level' ).removeClass( 'open' ).removeClass( 'current' );
 			$( '#bon-nav li.active' ).removeClass( 'active' );
 			if ( $( this ).parents( '.top-level' ).hasClass( 'open' ) ) {} else {
 				$( '#bon-nav .sub-menu.open' ).removeClass( 'open' ).slideUp().parent().removeClass( 'current' );
 				$( this ).parent().addClass( 'open' ).addClass( 'current' ).find( '.sub-menu' ).slideDown().addClass( 'open' ).children( 'li:first' ).addClass( 'active' );
 			}
 			
 			// Find the first child with sections and display it.
 			var clickedGroup = $( this ).parent().find( '.sub-menu li a:first' ).attr( 'href' );
 			
 			if ( clickedGroup != '' ) {
 				$( '.group' ).hide();
 				$( clickedGroup ).fadeIn();
 			}
 			return false;
 		});
 	}, // End toggle_nav_menus()
 	
/**
 * toggle_collapsed_fields()
 *
 * @since 1.0
 */
 
 	toggle_collapsed_fields: function () {
		$( '.group .collapsed' ).each(function(){

			$( this ).find( 'input:checked' ).parent().parent().parent().nextAll().each( function(){
				if ($( this ).hasClass( 'last' ) ) {
					$( this ).removeClass( 'hidden' );
					return false;
				}
				$( this ).filter( '.hidden' ).removeClass( 'hidden' );
				
				$( '.group .collapsed input:checkbox').click(function ( e ) {
					bonAdminInterface.unhide_hidden( $( this ).attr( 'id' ) );
				});

			});

			$( '.group .collapsed input:checkbox').click(function ( e ) {
				bonAdminInterface.unhide_hidden( $( this ).attr( 'id' ) );
			});


		});
 	}, // End toggle_collapsed_fields()

/**
 * setup_nav_highlights()
 *
 * @since 1.0
 */
 
 	setup_nav_highlights: function () {
	 	// Highlight the first item by default.
	 	$( '#bon-nav li.top-level:first' ).addClass( 'current' ).addClass( 'open' );
		
		// Default single-level logic.
		$( '#bon-nav li.top-level' ).not( '.has-children' ).find( 'a' ).click( function ( e ) {
			var thisObj = $( this );
			var clickedGroup = thisObj.attr( 'href' );
			
			if ( clickedGroup != '' ) {
				$( '#bon-nav .open' ).removeClass( 'open' );
				$( '.sub-menu' ).slideUp();
				$( '#bon-nav .active' ).removeClass( 'active' );
				$( '#bon-nav li.current' ).removeClass( 'current' );
				thisObj.parent().addClass( 'current' );
				
				$( '.group' ).hide();
				$( clickedGroup ).fadeIn();
				
				return false;
			}
		});
		
		$( '#bon-nav li:not(".has-children") > a:first' ).click( function( evt ) {
			var parentObj = $( this ).parent( 'li' );
			var thisObj = $( this );
			
			var clickedGroup = thisObj.attr( 'href' );
			
			if ( $( this ).parents( '.top-level' ).hasClass( 'open' ) ) {} else {
				$( '#bon-nav li.top-level' ).removeClass( 'current' ).removeClass( 'open' );
				$( '#bon-nav .sub-menu' ).removeClass( 'open' ).slideUp();
				$( this ).parents( 'li.top-level' ).addClass( 'current' );
			}
		
			$( '.group' ).hide();
			$( clickedGroup ).fadeIn();
		
			evt.preventDefault();
			return false;
		});
		
		// Sub-menu link click logic.
		$( '.sub-menu a' ).click( function ( e ) {
			var thisObj = $( this );
			var parentMenu = $( this ).parents( 'li.top-level' );
			var clickedGroup = thisObj.attr( 'href' );
			
			if ( $( '.sub-menu li a[href="' + clickedGroup + '"]' ).hasClass( 'active' ) ) {
				return false;
			}
			
			if ( clickedGroup != '' ) {
				parentMenu.addClass( 'open' );
				$( '.sub-menu li, .flyout-menu li' ).removeClass( 'active' );
				$( this ).parent().addClass( 'active' );
				$( '.group' ).hide();
				$( clickedGroup ).fadeIn();
			}
			
			return false;
		});
 	}, // End setup_nav_highlights()

/**
 * setup_custom_typography()
 *
 * @since 1.0
 */
 
 	setup_custom_typography: function () {
	 	$( 'select.bon-typography-unit' ).change( function(){
			var val = $( this ).val();
			var parent = $( this ).parent();
			var name = parent.find( '.bon-typography-size-px' ).attr( 'name' );
			if( name == '' ) { var name = parent.find( '.bon-typography-size-em' ).attr( 'name' ); }
			
			if( val == 'px' ) {
				var name = parent.find( '.bon-typography-size-em' ).attr( 'name' );
				parent.find( '.bon-typography-size-em' ).hide().removeAttr( 'name' );
				parent.find( '.bon-typography-size-px' ).show().attr( 'name', name );
			}
			else if( val == 'em' ) {
				var name = parent.find( '.bon-typography-size-px' ).attr( 'name' );
				parent.find( '.bon-typography-size-px' ).hide().removeAttr( 'name' );
				parent.find( '.bon-typography-size-em' ).show().attr( 'name', name );
			}
		
		});
 	}, // End setup_custom_typography()

/**
 * setup_custom_ui_slider()
 *
 * @since 5.3.5
 */
 
 	setup_custom_ui_slider: function () {

		$('div.ui-slide').each(function(i){

			if( $(this).data('min') != undefined && $(this).data('max') != undefined ) {

				$(this).slider( { 
								min: parseInt($(this).data('min')), 
								max: parseInt($(this).data('max')), 
								value: parseInt($(this).next("input").val()),
								step: parseInt($(this).data('step')) ,
								slide: function( event, ui ) {
									$( this ).next("input").val(ui.value);
								}
							});

				$(this).data('min', '').data('max', '').data('step', '');

			}

		});

 	}, // End setup_custom_ui_slider()

/**
 * init_flyout_menus()
 *
 * @since 5.0.0
 */
 
 	init_flyout_menus: function () {
 		// Only trigger flyouts on menus with closed sub-menus.
 		$( '#bon-nav li.has-children' ).each ( function ( i ) {
 			$( this ).hover(
	 			function () {
	 				if ( $( this ).find( '.flyout-menu' ).length == 0 ) {
		 				var flyoutContents = $( this ).find( '.sub-menu' ).html();
		 				var flyoutMenu = $( '<div />' ).addClass( 'flyout-menu' ).html( '<ul>' + flyoutContents + '</ul>' );
		 				$( this ).append( flyoutMenu );
	 				}
	 			}, 
	 			function () {
	 				// $( '#bon-nav .flyout-menu' ).remove();
	 			}
	 		);
 		});
 		
 		// Add custom link click logic to the flyout menus, due to custom logic being required.
 		$( '#bon-nav li.has-children' ).on( 'click', '.flyout-menu a', function ( e ) {
 			var thisObj = $( this );
 			var parentObj = $( this ).parent();
 			var parentMenu = $( this ).parents( '.top-level' );
 			var clickedGroup = $( this ).attr( 'href' );
 			
 			if ( clickedGroup != '' ) {
	 			$( '.group' ).hide();
	 			$( clickedGroup ).fadeIn();
	 			
	 			// Adjust the main navigation menu.
	 			$( '#bon-nav li' ).removeClass( 'open' ).removeClass( 'current' ).find( '.sub-menu' ).slideUp().removeClass( 'open' );
	 			parentMenu.addClass( 'open' ).addClass( 'current' ).find( '.sub-menu' ).slideDown().addClass( 'open' );
	 			$( '#bon-nav li.active' ).removeClass( 'active' );
	 			$( '#bon-nav a[href="' + clickedGroup + '"]' ).parent().addClass( 'active' );
 			}
 			
 			return false;
 		});
 	}, // End init_flyout_menus()

/**
 * banner_advert_close()
 *
 * @since 5.3.4
 */

	banner_advert_close: function () {
		$( '.bon-banner' ).each( function ( i ) {
			if ( $( this ).find( '.close-banner a' ).length ) {
				$( this ).find( '.close-banner a' ).click( function ( e ) {
					var answer = confirm( 'Are you sure you\'d like to close this banner?' + "\n" + 'Before closing this banner, make sure you have saved your theme options.' );
					if ( answer ) {} else {
						return false;
					}
				});
			}
		});
	},  // End banner_advert_close()

/**
 * unhide_hidden()
 *
 * @since 1.0
 * @see toggle_collapsed_fields()
 */
 
 	unhide_hidden: function ( obj ) {
 		obj = $( '#' + obj ); // Get the jQuery object.
		if ( obj.attr( 'checked' ) ) {
			obj.parent().parent().parent().nextAll().each(function(){
				if ( $( this ).filter( '.last' ).length ) {
					$( this ).removeClass( 'hidden' ).addClass( 'visible' );
					return false;
				}
				$( this ).removeClass( 'hidden' );
			});

		} else {
			obj.parent().parent().parent().nextAll().each( function(){
				if ( $( this ).filter( '.last' ).length ) {
					$( this ).addClass( 'hidden' );
				return false;
				}
				$( this ).addClass( 'hidden' );
			});
		}
 	} // End unhide_hidden()
  
  }; // End bonAdminInterface Object // Don't remove this, or the sky will fall on your head.

/**
 * Execute the above methods in the bonAdminInterface object.
 *
 * @since 1.0
 */
	$(document).ready(function () {
		
		if(!!$.prototype.wpColorPicker) {
			$('.bon-color').wpColorPicker();
		}
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

		// the file image button, saves the id and outputs the file name
		var fileFrame;
		$('.meta_box_upload_image_button').click(function(event) {
			event.preventDefault();
			
			var options, attachment;
			
			$self = $(event.target);
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
					console.log(attachment);
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

		bonAdminInterface.toggle_nav_tabs();
		bonAdminInterface.load_first_tab();
		bonAdminInterface.toggle_collapsed_fields();
		bonAdminInterface.setup_nav_highlights();
		bonAdminInterface.toggle_nav_menus();
		bonAdminInterface.init_flyout_menus();
		bonAdminInterface.open_first_menu();
		bonAdminInterface.banner_advert_close();
		bonAdminInterface.setup_custom_typography();
		bonAdminInterface.setup_custom_ui_slider();
		

		
			/* CSS */
			if($('#custom_css').length > 0) {
				var css_editor = CodeMirror.fromTextArea(document.getElementById('custom_css'), {
					 lineNumbers : true,
					 matchBrackets: true,
					 autoClearEmptyLines : true,
					 onBlur : function() { css_editor.save(); },
					 theme : 'default',
				     mode : 'text/css'
				});
			}
			

			/* JS */
			if($('#custom_js').length > 0) {
				var js_editor = CodeMirror.fromTextArea(document.getElementById('custom_js'), {
					 lineNumbers : true,
					 matchBrackets: true,
					 autoClearEmptyLines : true,
					 onBlur : function() { js_editor.save();},
					 theme : 'default',
				     mode : 'text/javascript'
				});
			}
			if($('#google_analytics').length > 0) {
				/* JS */
				var analytics = CodeMirror.fromTextArea(document.getElementById('google_analytics'), {
					 lineNumbers : true,
					 matchBrackets: true,
					 autoClearEmptyLines : true,
					 onBlur : function() { analytics.save();},
					 theme : 'default',
				     mode : 'text/javascript'
				});
			}

			// repeatable fields
			$('.meta_box_repeatable_add').live('click', function() {
				// clone
				var row = $(this).closest('.meta_box_repeatable').find('tbody tr:last-child');
				var clone = row.clone();
				clone.find('select.chosen').removeAttr('style', '').removeAttr('id', '').removeClass('chzn-done').data('chosen', null).next().remove();
				clone.find('input.regular-text, textarea, select').val('');
				clone.find('input[type=checkbox], input[type=radio]').attr('checked', false);
				row.after(clone);
				// increment name and id
				clone.find('input, textarea, select')
					.attr('name', function(index, name) {
						return name.replace(/(\d+)/, function(fullMatch, n) {
							return Number(n) + 1;
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
				//
				return false;
			});
			
			$('.meta_box_repeatable_remove').live('click', function(){
				$(this).closest('tr').remove();
				return false;
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

	});
  
})(jQuery);