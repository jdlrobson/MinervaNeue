<?php

if ( function_exists( 'wfLoadSkin' ) ) {
	wfLoadSkin( 'MinervaNeue' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['MinervaNeue'] = __DIR__ . '/i18n';
	return true;
} else {
	die( 'The Minerva Neue skin requires MediaWiki 1.25+' );
}
