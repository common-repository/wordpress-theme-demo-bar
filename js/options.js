jQuery(function($) {
	if ( 'undefined' == typeof $.fn.pngFix )
		$.fn.pngFix = function() { return this; }

	var thickDims = function() {
		var tbWindow = $('#TB_window');
		var H = $(window).height();
		var W = $(window).width();

		if ( tbWindow.size() ) {
			tbWindow.width( W - 90 ).height( H - 60 );
			$('#TB_iframeContent').width( W - 90 ).height( H - 90 );
			tbWindow.css({'margin-left': '-' + parseInt((( W - 90 ) / 2),10) + 'px'});
			if ( typeof document.body.style.maxWidth != 'undefined' )
				tbWindow.css({'top':'30px','margin-top':'0'});
		};

		return $('a.thickbox').each( function() {
			var href = $(this).parents('.wptdb_css_onetheme').find('.previewlink').attr('href');
			if ( ! href ) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 110 ) + '&height=' + ( H - 100 ) );
		});
	};

	thickDims()

	$(window).resize( function() { thickDims() } );
});