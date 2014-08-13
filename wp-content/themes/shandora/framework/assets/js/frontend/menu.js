var bonMenu;

(function($){
	
	// USE STRICT
	"use strict";

	var api;

	api = bonMenu = {

		init: function(){

			// check screensize after on window load and do the trigger if meet the requirement
			//if (Modernizr.mq('only screen and (min-width: 780px)')) { // start modernizr check
				this.clickTrigger();
				this.hoverTrigger();
				this.mobileMenuToggle();
			//} // end modernizr check
		},

		clickTrigger: function() {
			$('.bon-menu-click.menu-has-children > .bon-menu-label' ).on('click', function(e){

				// check is needed on event bind to prevent the event still triggering when window is resized
				if ( Modernizr.mq('only screen and (min-width: 780px)') ) { // start modernizr check

					e.preventDefault();
				
					var $parent = $(this).parent(),
						$toggle = $parent.find('.menu-toggle');
					
					if( $parent.hasClass('bon-menu-selected') ) {
						$parent.find('.bon-menu-selected .sub-menu').hide().end()
						       .find('.bon-menu-selected').removeClass('bon-menu-selected');
						$parent.children('.sub-menu').slideUp(300);
						$parent.removeClass('bon-menu-selected');

						/* PREPARE TOGGLE FOR MOBILE */
			            $toggle.removeClass('bi-angle-up').addClass('bi-angle-down');

					} else {
						$parent.find('.bon-menu-selected .sub-menu').hide().end()
							   .find('.bon-menu-selected').removeClass('bon-menu-selected').end()
							   .siblings('.bon-menu-selected').removeClass('bon-menu-selected').children('.sub-menu-1').slideUp(200);
						$parent.addClass('bon-menu-selected');

						if( $parent.hasClass('bon-mega-menu-item') && $parent.hasClass('menu-item-depth-0')  && !$parent.hasClass('bon-menu-full') ) {
							api.repositionSubMenu( $parent );
						}
						$parent.children('.sub-menu').stop(true,false).slideDown(300);

						/* PREPARE TOGGLE FOR MOBILE */
				        $toggle.removeClass('bi-angle-down').addClass('bi-angle-up');
					}
				} // end modernizr check
				
			});
		},

		hoverTrigger: function() {
			if (typeof jQuery().hoverIntent !== 'undefined' ) {

				$('.bon-menu-hover.menu-has-children' ).hoverIntent(function(e){

					// check is needed on event bind to prevent the event still triggering when window is resized
					if ( Modernizr.mq('only screen and (min-width: 780px)') ) { // start modernizr check

						var $this = $(this),
							$toggle = $(this).find('> .menu-toggle');

						$this.find('.bon-menu-selected .sub-menu').hide().end()
								   .find('.bon-menu-selected').removeClass('bon-menu-selected').end()
								   .siblings('.bon-menu-selected').removeClass('bon-menu-selected').children('.sub-menu-1').slideUp(200);
						$this.siblings().removeClass('bon-menu-selected').find('> .menu-toggle').removeClass('bi-angle-up').addClass('bi-angle-down');
						$this.addClass('bon-menu-selected');

						if( $this.hasClass('bon-mega-menu-item') && $this.hasClass('menu-item-depth-0')  && !$this.hasClass('bon-menu-full') ) {
							api.repositionSubMenu( $this );
						}

						$this.children('.sub-menu').stop(true,false).slideDown(300);

						/* PREPARE TOGGLE FOR MOBILE */
				        $toggle.removeClass('bi-angle-down').addClass('bi-angle-up');
					} // end modernizr check

				}, function(e){

					// check is needed on event bind to prevent the event still triggering when window is resized
					if ( Modernizr.mq('only screen and (min-width: 780px)') ) { // start modernizr check

						var $this = $(this),
							$toggle = $(this).find('> .menu-toggle');

						$this.find('.bon-menu-selected .sub-menu').hide().end().find('.bon-menu-selected').removeClass('bon-menu-selected');
						$this.children('.sub-menu').slideUp(300);
						$this.removeClass('bon-menu-selected');

						/* PREPARE TOGGLE FOR MOBILE */
			            $toggle.removeClass('bi-angle-up').addClass('bi-angle-down');
					} // end modernizr check

				});

			} else {
				
				$('.bon-menu-hover.menu-has-children' ).hover(function(e){

					// check is needed on event bind to prevent the event still triggering when window is resized
					if ( Modernizr.mq('only screen and (min-width: 780px)') ) { // start modernizr check

						var $this = $(this),
							$toggle = $(this).find('> .menu-toggle');

						$this.find('.bon-menu-selected .sub-menu').hide().end()
								   .find('.bon-menu-selected').removeClass('bon-menu-selected').end()
								   .siblings('.bon-menu-selected').removeClass('bon-menu-selected').children('.sub-menu-1').slideUp(200);
						$this.siblings().removeClass('bon-menu-selected').find('> .menu-toggle').removeClass('bi-angle-up').addClass('bi-angle-down');
						$this.addClass('bon-menu-selected');

						if( $this.hasClass('bon-mega-menu-item') && $this.hasClass('menu-item-depth-0')  && !$this.hasClass('bon-menu-full') ) {
							api.repositionSubMenu( $this );
						}

						$this.children('.sub-menu').stop(true,false).slideDown(300);

						/* PREPARE TOGGLE FOR MOBILE */
				        $toggle.removeClass('bi-angle-down').addClass('bi-angle-up');
					} // end modernizr check

				}, function(e){

					// check is needed on event bind to prevent the event still triggering when window is resized
					if ( Modernizr.mq('only screen and (min-width: 780px)') ) { // start modernizr check

						var $this = $(this),
							$toggle = $(this).find('> .menu-toggle');

						$this.find('.bon-menu-selected .sub-menu').hide().end().find('.bon-menu-selected').removeClass('bon-menu-selected');
						$this.children('.sub-menu').slideUp(300);
						$this.removeClass('bon-menu-selected');

						/* PREPARE TOGGLE FOR MOBILE */
			            $toggle.removeClass('bi-angle-up').addClass('bi-angle-down');
					} // end modernizr check

				});
			}
		},

		repositionSubMenu: function( $t ) {
			
			var  $sub_first = $t.find('.sub-menu-1'),
				 orientation = 'horizontal'; // currently only support horizontal

				switch( orientation ) {
					default:
					case 'horizontal':
						var selfWidth = $t.outerWidth(),
							selfLeft = $t.offset().left,
							subWidth = $sub_first.outerWidth(),
							menuBarWidth = $t.parents('ul.bon-mega-menu-items').outerWidth(),
							parentEdge = $t.parents('ul.bon-mega-menu-items').offset().left,
							centerLeft = ( selfLeft + ( selfWidth / 2 ) )
									- ( parentEdge + ( subWidth / 2 ) ),
							left = centerLeft > 0 ? centerLeft : 0;
						
						//If submenu is right of parentEdge
						if( left + subWidth > menuBarWidth ){
							left = menuBarWidth - subWidth;
						} 
						
						$sub_first.css({
							left: left + 'px'
						});

					break;

					case 'vertical':
						// do something
					break;
				}

		},

		mobileMenuToggle: function() {

			$('.bon-mega-menu-items .menu-has-children .menu-toggle').click(function (e) {
		        if ($(this).hasClass('bi-angle-down')) {
		            $(this).removeClass('bi-angle-down').addClass('bi-angle-up');
		        } else {
		            $(this).removeClass('bi-angle-up').addClass('bi-angle-down');
		        }
		        $(this).siblings('.sub-menu').slideToggle();
		        $(this).parent().toggleClass('bon-menu-selected');
		    });

		}
	}
	
	$(window).load(bonMenu.init());

})(jQuery);