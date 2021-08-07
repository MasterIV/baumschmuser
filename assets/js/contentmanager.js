var panelgroups = ['content', 'menu', 'header'];
var moveurl = '#';

$( function() {
	$( ".draggable" ).draggable({
		revert: "invalid",
		zIndex: 10,
		handle: ".handle"
	});

	$( "#panellist tr" ).draggable({
		revert: "invalid",
		zIndex: 10,
		handle: ".handle",
		helper: function(evt) {
			return $('<button class="btn"></button>').text( $( this ).find('td:eq(1)').text());
		}
	});

	$( ".droppable" ).droppable({
		hoverClass: "tree-hover",
		drop: function( event, ui ) {
			var start_layer = ui.draggable.data('layer');
			var start_panel = ui.draggable.find('.btn').data('panel');
			var target = $( this ).data('layer');

			if( start_layer )
				window.location.href = moveurl+'&movelayer='+start_layer+'&parent='+target;
			else
				window.location.href = moveurl+'&movepanel='+start_panel+'&parent='+target;
		}
	});

	$('#panelform input[name=group]')
			.focus( function() {
						$( "#panelform input[name=group]" ).autocomplete( "search", "" );
			})
			.autocomplete({
				source: panelgroups,
				minLength: 0
			});
});

