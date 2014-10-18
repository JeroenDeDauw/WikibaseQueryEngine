<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryEngineException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityInsertionStrategy {

	/**
	 * The insertion strategy is not required to support all types of entities.
	 * Hence the caller first needs to verify insertion can be done by calling canInsert.
	 *
	 * @param EntityDocument $entity
	 *
	 * @throws QueryEngineException
	 */
	public function insertEntity( EntityDocument $entity );

	/**
	 * @param EntityDocument $entity
	 *
	 * @return boolean
	 */
	public function canInsert( EntityDocument $entity );

}
