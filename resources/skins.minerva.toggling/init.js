( function ( M, $ ) {
	var page = M.getCurrentPage(),
		$contentContainer = $( '#mw-content-text' ),
		toggle = M.require( 'mobile.toggle/toggle' );

	/**
	 * Initialises toggling code.
	 *
	 * @method
	 * @param {jQuery.Object} $container to enable toggling on
	 * @param {String} prefix a prefix to use for the id.
	 * @param {Page} page The current page
	 * @ignore
	 */
	function init( $container, prefix, page ) {
		// distinguish headings in content from other headings
		$container.find( '> h1,> h2,> h3,> h4,> h5,> h6' ).addClass( 'section-heading' );
		toggle.enable( $container, prefix, page );
	}

	// FIXME: remove when cache clears
	if ( !$contentContainer.length ) {
		$contentContainer = $( '#content #bodyContent' );
	}
	// avoid this running on Watchlist
	if (
		!page.inNamespace( 'special' ) &&
		!mw.config.get( 'wgIsMainPage' ) &&
		mw.config.get( 'wgAction' ) === 'view'
	) {
		init( $contentContainer, 'content-', page );
	}
}( mw.mobileFrontend, jQuery ) );
