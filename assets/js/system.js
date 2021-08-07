function resizeGrid() {
	$('.grid').each( function( key, value ) {
		var grid = $( value );
		var total = 0;
		var max = 0;

		grid.children().each(function(k,v) {
			var w = $( v ).width();
			total += w;
			if( w > max ) max = w;
		});

		grid.children().css('width', total > grid.width() ? max+'px' : 'auto');
	});
}

$(function(){
	var tabs = $(".nav.nav-tabs>li>a").click(function(){
		location.hash = $(this).attr("href")+'-hash';
	});

	var hash = location.hash.substr( 0, location.hash.length -5 );
	tabs.filter(":first").tab("show");
	tabs.filter("[href='"+hash+"']").tab("show");

	$( ".datepicker" ).datepicker({
		dateFormat: "dd.mm.yy"
	});

	$(".datepicker").change( function() {
		$(this).prev('input').val($(this).val()?$(this).datepicker("getDate").getTime() / 1000:'');
	});

	$('.hasTooltip' ).tooltip();

	resizeGrid();
	window.onresize = resizeGrid;
});

function popup( url, width, height ) {
	if( !width ) width = 600;
	$.get( url, function( data ) {
//		var container = $( '<div class="popup_container"></div>' );
//		container.append( '<div class="popup_background" onclick="$( this ).parent().remove();"></div>' );
//		container.append( $( '<div class="popup_content"></div>' ).width( width ).html( data ));
		$( document.body ).append( data );
	});
}
