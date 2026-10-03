/* Blackroll admin: wp.media image picker for shade/project integer image fields. */
( function ( $ ) {
	'use strict';
	$( document ).on( 'click', '.blackroll-media-pick', function ( e ) {
		e.preventDefault();
		var $btn    = $( this );
		var target  = $btn.data( 'target' );
		var $input  = $( '#' + target );
		var $prev   = $( '.blackroll-media-preview[data-for="' + target + '"]' );

		var frame = wp.media( {
			title: 'Select image',
			button: { text: 'Use this image' },
			multiple: false,
		} );

		frame.on( 'select', function () {
			var att = frame.state().get( 'selection' ).first().toJSON();
			$input.val( att.id ).trigger( 'change' );
			var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
			$prev.html( '<img src="' + url + '" width="60" height="60" style="object-fit:cover" alt="">' );
		} );

		frame.open();
	} );
}( jQuery ) );
