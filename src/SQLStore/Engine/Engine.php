<?php

namespace Wikibase\QueryEngine\SQLStore\Engine;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\QueryEngine\QueryEngine;

/**
 * Simple query engine that works on top of the SQLStore.
 *
 * @since 0.1
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
