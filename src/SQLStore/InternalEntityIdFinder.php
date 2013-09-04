<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\DataModel\Entity\EntityId;

/**
 * Finds the internal entity id for the given external entity id.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseSQLStore
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface InternalEntityIdFinder {

	/**
	 * @param EntityId $entityId
	 *
	 * @return int
	 */
	public function getInternalIdForEntity( EntityId $entityId );

}
