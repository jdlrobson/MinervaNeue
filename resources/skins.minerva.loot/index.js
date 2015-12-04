( function ( $ ) {
	/**
	 * Check if an image is in the viewport
	 * @ignore
	 * @param {jQuery.Object} $el
	 * @return {Boolean} whether in the viewport or not.
	 */
	function isElementInViewport( $el ) {
		var rect = $el[0].getBoundingClientRect();
		return (
			rect.top >= 0 &&
			rect.left >= 0 &&
			rect.bottom <= $( window ).height() * 2.5 &&
			rect.right <= $( window ).width() * 2.5
		);
	}

	/**
	 * Check any images with the class pending to see if they are in view yet and if so
	 * force a load with a query string parameter.
	 * @ignore
	 */
	function checkImagesHandler() {
		$( '.LootTransformedImage' ).each( function () {
			var $img = $( this );
			if ( isElementInViewport( $img ) ) {
				$img.replaceWith( $img.data( 'replace-with' ) );
			}
		} );
	}

	$( function () {
		$( window ).on( 'resize scroll', $.debounce( 100, checkImagesHandler ) );
		checkImagesHandler();
	} );


}( jQuery ) );
