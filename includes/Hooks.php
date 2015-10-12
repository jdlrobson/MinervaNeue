<?php
/**
 * Hooks.php
 */
namespace MinervaNeue;

/**
 * Hook handlers for Gather extension
 *
 * Hook handler method names should be in the form of:
 *	on<HookName>()
 * For intance, the hook handler for the 'RequestContextCreateSkin' would be called:
 *	onRequestContextCreateSkin()
 */
class Hooks {
	public static function onExtensionSetup() {
		global $wgResourceLoaderLESSImportPaths;
		$wgResourceLoaderLESSImportPaths[] = __DIR__ . "/minerva.less/";
		if (
			!defined( 'MOBILEFRONTEND' ) &&
			!\ExtensionRegistry::getInstance()->isLoaded( 'MobileFrontend' )
		) {
			echo "MinervaNeue skin requires MobileFrontend.\n";
			die( -1 );
		}
	}
}
	