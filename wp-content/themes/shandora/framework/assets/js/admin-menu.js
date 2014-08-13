jQuery(document).ready(function($){

    //$('textarea.bon-edit-menu-item').css('overflow', 'hidden').autogrow();
    $('.bon-activate-advanced-menu-all').on('click', function(e){
        e.preventDefault();
        var $p = $(this).parents('.bon-advanced-menu-metabox');
        if( $p.hasClass('all-checked') ) {
            $p.find('input[type="checkbox"]').prop('checked', false);
            $p.removeClass('all-checked');
        } else {
            $p.find('input[type="checkbox"]').prop('checked', true);
            $p.addClass('all-checked');
        }
    });  

    $('#submit-bon-activate-advanced-menu').on('click', function(e){
        e.preventDefault();
        var checkbox_val = new Array,
            $t = $(this);
            $p = $t.parents('.bon-advanced-menu-metabox'),
            nonce_field = $p.find('#bon_activate_advanced_menu_nonce').val();
         

        $t.parent().find('.spinner').show();
        
        $p.find('input[type="checkbox"]:checked').each(function(){
            checkbox_val.push($(this).val());
        })

        $.post(bon_admin_menu_ajax.url, {
            action: 'bon_update_advanced_menu_metabox',
            bon_activate_advanced_menu_nonce: nonce_field,
            checked: JSON.stringify( checkbox_val ),
        }, function (data) {
            if( $p.prev('.updated').length > 0 ) {
                $p.prev('.updated').remove();
            }
            $p.before('<div class="updated"><p class="success">'+data+'</p></div>');
            $t.parent().find('.spinner').hide();
        });

    });

	$('#menu-to-edit').on( 'click', '.bon-nav-menu-handle', function() {
		var p = $(this).parents( '.bon-nav-menu-edit' );
		var ins = p.find( '.bon-nav-menu-inside' );
		if ( p.hasClass('active') ) {
			p.removeClass('active');
			ins.slideUp('fast').addClass('hide');
		} else {
			p.addClass('active');
			ins.slideDown('fast').removeClass('hide');
		}
	});
    
    var menuMediaFrame;

    $('#menu-to-edit').on( 'click', '.upload-button', function (event) {

        var attachment;

        $el = $(this);
        $div = $el.closest('.bon-edit-menu-fieldset');

        event.preventDefault();

        if (menuMediaFrame) {
            menuMediaFrame.open();
            return
        }

        menuMediaFrame = wp.media({
            title: $el.data('choose'),
            multiple: false,
            library: {
                type: 'image'
            },
            button: {
                text: $el.data('update'),
            }
        });

        menuMediaFrame.on('select', function () {

            var attachment = menuMediaFrame.state().get('selection').first();

            var src = attachment.attributes.url;

            if( attachment.attributes.type == 'image' ) {
                $div.find('.bon-edit-menu-screenshot img').attr('src', src).show().removeClass('no-image');
                $div.find('.bon-edit-menu-upload').val(src);
                $div.find('.remove-image').removeClass('no-image').show();
                $div.find('.upload-button').addClass('remove-file').removeClass('upload-button').val(bon_admin_menu_ajax.remove);
            }

        });

        menuMediaFrame.open();
    });

    $('#menu-to-edit').on('click', '.remove-image, .remove-file', function () {
        $el = $(this);
        $div = $el.closest('.bon-edit-menu-fieldset');
        $div.find('.remove-image').hide().addClass('no-image');
        $div.find('.bon-edit-menu-upload').val('');
        $div.find('.bon-edit-menu-screenshot img').slideUp().attr('src', '').addClass('no-image');
        $div.find('.remove-file').addClass('upload-button').removeClass('remove-file').val(bon_admin_menu_ajax.upload);
    });

    $('#menu-to-edit').on('click', '.bon-menu-open-editor', function(event){
        var id = $(this).data('id'),
            edId = 'bon-menu-editor-dialog',
            title = $(this).data('dialog-title'),
            button_label = $(this).data('button-submit'),
            dialog = $('.bon-menu-editor-dialog-wrapper'),
            textarea = $(this).parents('.bon-nav-menu-field').find('.bon-textarea-editor' ),
            content = textarea.val();

        if( typeof tinymce != 'undefined' ) {
            if( tinymce.activeEditor != null ) {
                tinymce.activeEditor.setContent(content);
            } else {
                tinymce.get(edId).setContent(content);
            }
            
            tinymce.execCommand('mceRemoveEditor', true, edId );
        }

        dialog.dialog({
            show: false,  
            hide: false, 
            resizable: false, 
            width: 800,
            title: title,
            modal: true,
            open: function() {
               $(this).parents('.ui-dialog').css({
                 'z-index' : '100099'
               }).siblings('.ui-widget-overlay').css({
                 'z-index' : '100098'
               });
                tinymce.execCommand('mceAddEditor', true, edId );

            },
            close: function() {
                tinymce.get(edId).setContent('');
                tinymce.execCommand('mceRemoveEditor', true, edId );
                tinymce.execCommand('mceAddEditor', true, edId );
            },
            buttons: [
                {
                    text: button_label,
                    class: 'button-primary',
                    click: function(e){
                        var mceContent = tinymce.activeEditor.getContent();

                        console.log(mceContent);
                        textarea.val(mceContent);
                        $(this).dialog('close');
                    } 
                }
            ]
        });

    });
    
    /* Fixed Modal Issue Interaction with tinyMCE Float Panel */
    $.widget( "ui.dialog", $.ui.dialog, {
     /*! jQuery UI - v1.10.2 - 2013-12-12
      *  http://bugs.jqueryui.com/ticket/9087#comment:27 - bugfix
      *  http://bugs.jqueryui.com/ticket/4727#comment:23 - bugfix
      *  allowInteraction fix to accommodate windowed editors
      */
      _allowInteraction: function( event ) {
        if ( this._super( event ) ) {
          return true;
        }

        // address interaction issues with general iframes with the dialog
        if ( event.target.ownerDocument != this.document[ 0 ] ) {
          return true;
        }

        // address interaction issues with dialog window
        if ( $( event.target ).closest( ".mce-floatpanel" ).length ) {
          return true;
        }

        // address interaction issues with dialog window
        if ( $( event.target ).closest( "#wp-link" ).length ) {
          return true;
        }

        // address interaction issues with iframe based drop downs in IE
        if ( $( event.target ).closest( ".mce-panel" ).length ) {
          return true;
        }
      }
    
    });

});

