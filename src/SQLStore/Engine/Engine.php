<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Ask\Language\Query;
use Wikibase\Database\QueryInterface;
use Wikibase\QueryEngine\QueryEngine;
use Wikibase\QueryEngine\QueryEngineResult;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\EntityId;

/**
 * Simple query engine that works on top of the SQLStore.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Engine implements QueryEngine {

	private $matchFinder;

	public function __construct( DescriptionMatchFinder $matchFinder ) {
		$this->matchFinder = $matchFinder;
	}

	/**
	 * @see QueryEngine::getMatchingEntities
	 *
	 * @since 0.1
	 *
	 * @param Description $description
	 * @param QueryOptions $options
	 *
	 * @return EntityId[]
	 */
	public function getMatchingEntities( Description $description, QueryOptions $options ) {
		return $this->matchFinder->findMatchingEntities( $description, $options );
	}

}
