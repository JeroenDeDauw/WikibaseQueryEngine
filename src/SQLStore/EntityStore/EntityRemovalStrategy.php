<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryEngineException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityRemovalStrategy {

	/**
	 * The removal strategy is not required to support all types of entities.
	 * Hence the caller first needs to verify removal can be done by calling canRemove.
	 *
	 * @param EntityDocument $entity
	 *
	 * @throws QueryEngineException
	 */
	public function removeEntity( EntityDocument $entity );

	/**
	 * @param EntityDocument $entity
	 *
	 * @return boolean
	 */
	public function canRemove( EntityDocument $entity );

}
