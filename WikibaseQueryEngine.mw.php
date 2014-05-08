<?php

/**
 * MediaWiki setup for the Query component of Wikibase.
 * The component should be included via the main entry point, Database.php.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

$GLOBALS['wgExtensionCredits']['wikibase'][] = array(
	'path' => __DIR__,
	'name' => 'Wikibase QueryEngine',
	'version' => WIKIBASE_QUERYENGINE_VERSION,
	'author' => array(
		'[https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
	),
	'url' => 'https://github.com/wmde/WikibaseQueryEngine',
	'description' => 'Answers Ask queries against a collection of Wikibase entities'
);