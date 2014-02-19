<?php

/**
 * PHPUnit test bootstrap file for the Wikibase QueryEngine component.
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !in_array( '--testsuite=QueryEngineStandalone', $GLOBALS['argv'] ) ) {
	require_once( __DIR__ . '/evilMediaWikiBootstrap.php' );
}

$pwd = getcwd();
chdir( __DIR__ . '/..' );
passthru( 'composer update' );
chdir( $pwd );

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

require_once( __DIR__ . '/../vendor/autoload.php' );

require_once( __DIR__ . '/testLoader.php' );
