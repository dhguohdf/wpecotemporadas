jQuery(document).ready( function($) {
	var hide_spinners = function() {
		$("#pp-install .waiting").hide();
		$("#pp-install .button-secondary").prop('disabled',false);
	}
	
	var pp_is_array = function(input) {
		return typeof(input)=='object'&&(input instanceof Array);
	}
	
	var redraw_act_status = function(data,txtStatus) {
		hide_spinners();
	
		var msg = '';
		var captions = jQuery.parseJSON( ppSettings.keyStatus.replace(/&quot;/g, '"') );
		
		if ( ! pp_is_array(data) || typeof data[0] == 'undefined' ) {
			msg = ppSettings.errCaption;
		} else if ( ! jQuery.inArray( data[0], captions ) ) {
			msg = ppSettings.errCaption;
		} else { 
			msg = captions[ data[0] ];
			
			if ( ( 1 == data[0] ) || ( data[0] <= -200 && data[0] >= -299 ) ) {
				ppSettings.activated = 1;
				$("#pp-install #activation-button").html( ppSettings.deactivateCaption );
				$("#pp-install #renewal-button").hide();
				$("#pp-install #support_key").hide();
				$("#pp-install .pp-key-active").show();
				$("#pp-install .pp-key-expired").hide();
				$("#pp-install .pp-update-link").show();
			} else if ( -1 != data[0] ) {
				ppSettings.activated = 0;
				$("#pp-install #activation-button").html( ppSettings.activateCaption );
				$("#pp-install #support_key").show();
				$("#pp-install #support_key").val('');
				$("#pp-install .pp-key-active").hide();
				$("#pp-install .pp-update-link").hide();
			}
		}
		
		$("#pp-install #activation-status").html(msg).show();
		
		if ( 1 == data[0] )
			$("#pp-install #activation-reload").show();
	}
	
	var ajax_connect_failure = function(data,txtStatus) {
		hide_spinners();
		$("#pp-install #activation-status").html( ppSettings.noConnectCaption );
		return;
	}

	// click handlers for activate / deactivate button
	$('#pp-install_table #activation-button').bind( 'click', function(e) {
		$(this).closest('td').find('.waiting').show();
		$(this).prop('disabled',true);

		e.preventDefault();
		e.stopPropagation();

		if ( 1 == ppSettings.activated ) {
			var data = { 'pp_ajax_settings': 'deactivate_key' };
			$.ajax({url:ppSettings.deactivateURL, data:data, dataType:"json", cache:false, success:redraw_act_status, error:ajax_connect_failure});
		} else {
			var key = jQuery.trim( $("#pp-install #support_key").val() );
		
			if ( ! key ) {
				$("#pp-install #activation-status").html( ppSettings.noEntryCaption );
				hide_spinners();
				return;
			}
			
			var data = { 'pp_ajax_settings': 'activate_key', 'key': key };
			$.ajax({url:ppSettings.activateURL, data:data, dataType:"json", cache:false, success:redraw_act_status, error:ajax_connect_failure});
		}
	});
	
	$('#pp-install_table #renewal-button').bind( 'click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		var data = { 'pp_ajax_settings': 'refresh_version' };
		$.ajax({url:ppSettings.renewURL, data:data, dataType:"json", success:redraw_act_status, error:ajax_connect_failure});
	});

	var nofunc = function(data,txtStatus) {
		return;
	}
	
	$('input[name="pp_submit"]').bind( 'click', function(e) {
		if ( $('a.pp-install').closest('li').hasClass("agp-selected_agent") ) {
			$('#pp_support_waiting').show();
			var data = { 'pp_ajax_settings': 'refresh_version' };
			$.ajax({url:ppSettings.refreshURL, data:data, async:false, dataType:"json", cache:false, success:nofunc, error:nofunc});
			hide_spinners();
		}
	});
	
	$('#pp_support_data_all').click( function(e) {
		$('#pp-install_table div.support_data input[disabled!="disabled"]').prop('checked', $(this).is(':checked') );
	});
	
	$('div.support_data input[type="checkbox"]').change( function() {
		$('li.upload-config a, li.pp-support-forum a').bind('click', false).css('color', '#777').css('text-decoration', 'none');
		$('li.upload-config a').html(ppSettings.supportOptChanged);
	});
});