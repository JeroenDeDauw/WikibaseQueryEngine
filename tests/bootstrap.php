<?php

/**
 * PHPUnit test bootstrap file for the Wikibase QueryEngine component.
 *
 * @since 0.1
 *
 * @file
 * @ingroup QueryEngine
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !in_array( '--testsuite=QueryEngine', $GLOBALS['argv'] ) ) {
	require_once( __DIR__ . '/evilMediaWikiBootstrap.php' );
}

require_once( __DIR__ . '/../WikibaseQueryEngine.php' );

require_once( __DIR__ . '/testLoader.php' );

// If something needs to change here, a reflecting change needs to be added to INSTALL.md.