(function ()
{
	
	tinymce.PluginManager.add("bonAdvancedMenuShortcode", function( editor, url ){

		var menuOptions = [
			{
				text: 'Video on Menu',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Shortcode',
						body: [
							{
								type: 'textbox',
								name: 'url',
								label: 'Video URL',
								value: ''
							},

							{
								type: 'listbox',
								name: 'flexible',
								label: 'Flexible Height and Width',
								values: [
									{ text: 'Yes', value: 'yes' },
									{ text: 'No', value: 'no' }
								]
							},

							{
								type: 'textbox',
								name: 'width',
								label: 'Video Width',
								value: ''
							},

							{
								type: 'textbox',
								name: 'height',
								label: 'Video Height',
								value: ''
							}
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bm-video flexible="'+e.data.flexible+'" width="'+e.data.width+'" height="'+e.data.height+'"]'+e.data.url+'[/bm-video]');
						}
					});
				}
			},
			{
				text: 'Post List',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Shortcode',
						body: [
							{
								type: 'listbox',
								name: 'numberposts',
								label: 'Number of Posts',
								values: [
									{ text: '1', value: '1'},
									{ text: '2', value: '2'},
									{ text: '3', value: '3'},
									{ text: '4', value: '4'}
								]
							},
							{
								type: 'listbox',
								name: 'show_excerpt',
								label: 'Show Excerpt',
								values: [
									{ text: 'No', value: 'no'},
									{ text: 'Yes', value: 'yes'}
								]
							},
							{
								type: 'listbox',
								name: 'order',
								label: 'Order',
								values: [
									{ text: 'DESC', value: 'DESC'},
									{ text: 'ASC', value: 'ASC'}
								]
							},
							{
								type: 'listbox',
								name: 'orderby',
								label: 'Order By',
								values: [
									{ text: 'date', value: 'date' },
									{ text: 'ID', value: 'ID' },
									{ text: 'author', value: 'author' },
									{ text: 'title', value: 'title' },
									{ text: 'name', value: 'name' },
									{ text: 'modified', value: 'modified' },
									{ text: 'rand', value: 'rand' },
									{ text: 'comment_count', value: 'comment_count' },
									{ text: 'menu_order', value: 'menu_order' }
								]
							},
							{
								type: 'textbox',
								name: 'post_type',
								label: 'Post Type (optional)',
								value: ''
							},
							{
								type: 'textbox',
								name: 'taxonomy_slug',
								label: 'Taxonomy Slug ( optional )',
								value: ''
							},
							{
								type: 'textbox',
								name: 'term_slug',
								label: 'Term Slug (optional)',
								value: ''
							},
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bm-post numberposts="'+e.data.numberposts+'" show_excerpt="'+e.data.show_excerpt+'" orderby="'+e.data.orderby+'"  order="'+e.data.order+'"  post_type="'+e.data.post_type+'" term_slug="'+e.data.term_slug+'" taxonomy_slug="'+e.data.taxonomy_slug+'"]');
						}
					});
				}
			},
			
		];

		editor.addButton( 'bon_advanced_menu_button', {
			icon: 'bon-menu-shortcode-icon',
			title: 'Insert Menu Shortcode',
			type: 'menubutton',
			menu: menuOptions
		});
	});
})();