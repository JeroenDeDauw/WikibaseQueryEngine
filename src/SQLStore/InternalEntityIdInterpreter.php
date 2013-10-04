<?php

namespace Wikibase\QueryEngine\SQLStore;

use Wikibase\DataModel\Entity\EntityId;

/**
 * Finds the external entity id for the given internal entity id.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface InternalEntityIdInterpreter {

	/**
	 * @param int $internalEntityId
	 *
	 * @return EntityId
	 */
	public function getExternalIdForEntity( $internalEntityId );

}
