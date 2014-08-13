var bonIconUtil;

(function($){
	// USE STRICT
	"use strict";

	var api = bonIconUtil = {

		chooseIcon: 'bon-choose-icon',
		removeIcon: 'bon-remove-icon',
		iconFieldset: 'bon-icon-fieldset',
		iconInputField: 'bon-icon-input',
		iconPlaceholder: 'bon-icon-placeholder',
		iconCell: 'bon-icon-cell',
		iconEmpty: 'bon-no-icon',
		modalHolder : 'bon-icon-modal',
		modalOverlay: 'bon-icon-modal-overlay',
		modalLoader: 'bon-modal-loader',
		modalClose: 'bon-close-icon-popup',
		modalClass : 'bon-icon-font-modal',

		init: function(){
			
			this.chooseIconAction();
			this.removeIconAction();
			this.changeIconAction();
			this.selectIconAction();
			this.closeIconPopUp();

		},
		iconModal: function(){
			if( $('#' + api.modalHolder).length < 1 ) {
				var modalTemplate = '<div id="'+api.modalOverlay+'"></div><div id="'+api.modalHolder+'"><div id="'+api.modalLoader+'" class="bonicons bi-spinner bi-spin"></div></div>';
				$('body').append( modalTemplate );
			} else {
				$('#' + api.modalHolder + ', #'+ api.modalOverlay ).show();
			}
		},
		removeIconAction: function() {

			$('body').on( 'click', '.' + api.removeIcon, function(event) {
		        var $el = $(this);
		        var $div = $el.closest('.' + api.iconFieldset );

		        $div.find('.' + api.iconInputField ).val('');
		        $div.find('.' + api.iconPlaceholder ).addClass(api.iconEmpty).find('i').attr( 'class', '' );
		        $div.find('.' + api.removeIcon ).addClass(api.chooseIcon).removeClass(api.removeIcon).val(bon_util_ajax.choose_icon);
		        
		        event.preventDefault();

		    });
		},
		chooseIconAction: function() {
			$('body').on( 'click', '.' + api.chooseIcon, function(event) {
				
				var $el = $(this);
       			var $div = $el.closest('.' + api.iconFieldset );

       			var id = $el.data('id');
       			var windowWidth = $(window).outerWidth();
        		var windowHeight = $(window).outerHeight();

       			api.iconModal();

       			$('#' + api.modalHolder).css({
		            'width': windowWidth,
		            'height': windowHeight,
		            'position': 'fixed',
		            'z-index': '100110',
		            'top': '10%',
		            'left': '0',
		            'overflow-y': 'auto'
		        });
		        $('#' + api.modalOverlay).css({
		            'opacity': 0.7,
		            'height': windowHeight,
		            'width': windowWidth,
		            'position': 'fixed',
		            'left': '0px',
		            'top': '0px',
		            'z-index': '100109'
		        });

		        var nonce_field = $div.find('#bon_icon_nonce').val();
		        $.post(bon_util_ajax.url, {
		            action: 'bon_icon_selection',
		            nonce: nonce_field,
		        }, function (data) {
		            data = '<div class="'+api.modalClass+'" data-id="'+id+'"><div class="bon-icon-font-popup">'+data+'<span class="'+api.modalClose+'">x</span></div></div>';
		            $('#' + api.modalHolder).find('#' + api.modalLoader ).remove().end().find('.'+api.modalClass).remove().end().append($(data));
		        });
		        event.preventDefault();

			});
		},
		closeIconPopUp: function() {
			$('body').on('click', '#'+api.modalHolder+' .'+api.modalClose, function(event) {
		        $('#'+api.modalHolder+ ', #'+ api.modalOverlay ).hide();
		        event.preventDefault();
		    });
		},
		changeIconAction: function() {
			$('body').on('change', '.'+api.iconInputField, function(){
		        var $el = $(this);
		        $el.siblings('.'+api.iconPlaceholder).removeClass(api.iconEmpty).find('i').attr('class', '').addClass('bonicons ' + $el.val() );
		    });
		},
		selectIconAction: function() {

			$('body').on('click', '.'+ api.iconCell, function(event) {

		        var id = $(this).parents('.'+api.modalClass).data('id');

		        var selected_icon = $(this).data('icon');

		        var target = $( id );

		        target.find('.' + api.iconInputField ).val(selected_icon);
		        target.find('.' + api.iconPlaceholder ).removeClass(api.iconEmpty).find('i').attr('class', '').addClass('bonicons ' + selected_icon );
		        target.find('.' + api.chooseIcon ).addClass(api.removeIcon).removeClass(api.chooseIcon).val(bon_util_ajax.remove_icon);
		        $('#'+ api.modalHolder +', #'+api.modalOverlay).hide();

		        event.preventDefault();
		    });
		}
	}


	$(document).ready(function(){ bonIconUtil.init(); });

})(jQuery);