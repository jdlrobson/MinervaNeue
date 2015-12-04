<?php
/**
 * Hooks.php
 */
namespace MinervaNeue;

use RequestContext;

/**
 * Hook handlers for Gather extension
 *
 * Hook handler method names should be in the form of:
 *	on<HookName>()
 * For intance, the hook handler for the 'RequestContextCreateSkin' would be called:
 *	onRequestContextCreateSkin()
 */
class Hooks {
	/**
	 * ResourceLoaderGetLessVars hook handler
	 *
	 * Add the context-based less variables.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ResourceLoaderGetLessVars
	 * @param array &$lessVars Variables already added
	 */
	public static function onResourceLoaderGetLessVars( &$lessVars ) {
		// PPleaseeeee move to core.
		$config = RequestContext::getMain()->getConfig();
		$lessVars = array_merge( $lessVars,
			array(
				'wgMFDeviceWidthTablet' => "{$config->get( 'MFDeviceWidthTablet' )}px",
				'wgMFDeviceWidthMobileSmall' => "{$config->get( 'MFDeviceWidthMobileSmall' )}px"
			)
		);
	}

	public static function onExtensionSetup() {
		global $wgResourceLoaderLESSImportPaths;
		$wgResourceLoaderLESSImportPaths[] = __DIR__ . "/../minerva.less/";
	}
}
	