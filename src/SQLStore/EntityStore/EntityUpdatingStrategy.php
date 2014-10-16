<?php

namespace Wikibase\QueryEngine\SQLStore\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryEngineException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityUpdatingStrategy {

	/**
	 * @param EntityDocument $entity
	 *
	 * @throws QueryEngineException
	 */
	public function updateEntity( EntityDocument $entity );

	/**
	 * @param EntityDocument $entity
	 *
	 * @return boolean
	 */
	public function canUpdate( EntityDocument $entity );

}
